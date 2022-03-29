<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Repository;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\TemplateRepositoryInterface;
use Amasty\Prot\Model\ResourceModel\Schedule as ScheduleResource;
use Amasty\Prot\Model\ResourceModel\Template as TemplateResource;
use Amasty\Prot\Model\ResourceModel\Template\Collection;
use Amasty\Prot\Model\ResourceModel\Template\CollectionFactory;
use Amasty\Prot\Model\TemplateFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\CacheContext;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Template implements TemplateRepositoryInterface
{
    const MAX_PROCESS_COUNT = 100;

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var TemplateResource
     */
    private $templateResource;

    /**
     * @var CollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\CollectionFactory
     */
    private $catalogCollectionFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $templates = [];

    /**
     * @var \Amasty\Prot\Model\ResourceModel\TemplateOption\CollectionFactory
     */
    private $optionCollectionFactory;

    /**
     * @var TemplateOption
     */
    private $templateOptionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ScheduleResource
     */
    private $scheduleResource;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Amasty\Prot\Model\Product\Validator
     */
    private $productValidator;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        TemplateFactory $templateFactory,
        TemplateResource $templateResource,
        CollectionFactory $templateCollectionFactory,
        \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\CollectionFactory $catalogCollectionFactory,
        \Amasty\Prot\Model\ResourceModel\TemplateOption\CollectionFactory $optionCollectionFactory,
        TemplateOption $templateOptionRepository,
        ScheduleResource $scheduleResource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MetadataPool $metadataPool,
        CacheContext $cacheContext,
        ManagerInterface $eventManager,
        \Amasty\Prot\Model\Product\Validator $productValidator,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->templateFactory = $templateFactory;
        $this->templateResource = $templateResource;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->catalogCollectionFactory = $catalogCollectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->templateOptionRepository = $templateOptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scheduleResource = $scheduleResource;
        $this->metadataPool = $metadataPool;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
        $this->productValidator = $productValidator;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritdoc
     */
    public function save(TemplateInterface $template)
    {
        try {
            $this->templateResource->save($template);
            unset($this->templates[$template->getTemplateId()]);
        } catch (\Exception $e) {
            if ($template->getTemplateId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save template with ID %1. Error: %2',
                        [$template->getTemplateId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new template. Error: %1', $e->getMessage()));
        }

        return $template;
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        return $this->templateFactory->create();
    }

    /**
     * @param int $templateId
     * @param array $productIds
     * @param int $originalProductId
     * @param bool $isRow
     *
     * @return bool
     * @throws LocalizedException
     */
    public function assignTemplateToIds(int $templateId, array &$productIds, $originalProductId = null, $isRow = false)
    {
        if ($productIds) {
            $productIds = $isRow ? $productIds : $this->productValidator->validateProductIds($templateId, $productIds);
            $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
            if ($productMetadata->getLinkField() == 'row_id' && !$isRow) {
                $rowIds = $this->getRowIds($productIds);
            } else {
                $rowIds = $productIds;
            }
        } else {
            $rowIds = $this->getAllTemplateProducts($templateId);
        }
        $shouldApply = count($rowIds) < self::MAX_PROCESS_COUNT;
        $originalProductId = $originalProductId ? : $this->getOriginalProductId($templateId);
        $rowIds = array_diff($rowIds, [$originalProductId]);

        if ($originalProductId && $rowIds) {
            $amastyOptionIds = $this->getAmastyOptionIds($templateId);
            if (!$amastyOptionIds) {
                throw new LocalizedException(__('Something wend wrong. There are no template options.'));
            }

            $scheduleData = [];
            foreach ($rowIds as $rowId) {
                if ($shouldApply) {
                    $this->templateOptionRepository->applyOptions($amastyOptionIds, $originalProductId, $rowId);
                } else {
                    foreach ($amastyOptionIds as $optionId) {
                        $scheduleData[] = [
                            ScheduleInterface::PRODUCT_ID          => $rowId,
                            ScheduleInterface::AMASTY_OPTION_ID    => $optionId,
                            ScheduleInterface::ORIGINAL_PRODUCT_ID => $originalProductId,
                        ];
                    }
                }
            }

            if ($scheduleData) {
                $this->scheduleResource->getConnection()->insertOnDuplicate(
                    $this->scheduleResource->getMainTable(),
                    $scheduleData,
                    [ScheduleInterface::CREATED_AT]
                );
            }

            if ($shouldApply) {
                $this->templateOptionRepository->updateEntityOptionsValues($originalProductId, $rowIds);
                $this->clearCache($productIds);
            }
        }

        return $shouldApply;
    }

    /**
     * @param array $productIds
     *
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    public function getRowIds($productIds)
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $connection = $metadata->getEntityConnection();
        $select = $connection->select()->from(
            ['entity_table' => $metadata->getEntityTable()],
            ['row_id']
        )->setPart(
            'disable_staging_preview',
            true
        )->where(
            $metadata->getIdentifierField() . ' IN (?)',
            $productIds
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param int $templateId
     *
     * @return array
     */
    protected function getAllTemplateProducts(int $templateId)
    {
        $ids = $this->templateResource->getAllTemplateProducts($templateId);
        if ($ids) {
            $ids = array_unique($ids);
        }

        return $ids;
    }

    /**
     * @param int $templateId
     *
     * @return int
     */
    protected function getOriginalProductId(int $templateId)
    {
        return $this->catalogCollectionFactory->create()
            ->getOriginalProductId($templateId);
    }

    /**
     * @param int $templateId
     *
     * @return \Magento\Framework\DataObject[]
     */
    protected function getAmastyOptionIds(int $templateId)
    {
        $collection = $this->optionCollectionFactory->create()
            ->addFieldToFilter(TemplateOptionInterface::TEMPLATE_ID, $templateId);

        return $collection->getAllIds();
    }

    /**
     * @param array $productIds
     */
    protected function clearCache(array $productIds)
    {
        $this->cacheContext->registerEntities(Product::CACHE_TAG, $productIds);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
    }

    /**
     * @inheritdoc
     */
    public function removeFromIds(int $templateId, array &$productIds)
    {
        try {
            $productIds = $this->filterProductsToRemove($templateId, $productIds);
            $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
            $rowIds = $productIds;
            if ($productMetadata->getLinkField() == 'row_id') {
                $rowIds = $this->getRowIds($productIds);
            }

            $this->catalogCollectionFactory->create()->removeFromIds($templateId, $rowIds);
            $this->checkAndRemoveTemplate($templateId);
            $this->clearCache($productIds);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove template with ID %1. Error: %2',
                    [$templateId, $e->getMessage()]
                )
            );
        }

        return true;
    }

    /**
     * @param int $templateId
     * @param array $productIds
     *
     * @return array
     * @throws \Exception
     */
    protected function filterProductsToRemove(int $templateId, array $productIds)
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $productIds = $this->templateResource->filterProductsToRemove(
            $templateId,
            $productIds,
            $productMetadata->getLinkField()
        );

        return $productIds;
    }

    /**
     * remove template if no options
     *
     * @param int $templateId
     */
    public function checkAndRemoveTemplate(int $templateId)
    {
        $optionCount = (bool)$this->templateResource->getOptionCount($templateId);
        if (!$optionCount) {
            $this->delete($this->get($templateId));
            $this->messageManager->addNoticeMessage(
                __(
                    'The template was also deleted, because a template can\'t exist'
                    . ' without being assigned to at least one product.'
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(TemplateInterface $template)
    {
        try {
            $this->templateResource->delete($template);
            unset($this->templates[$template->getTemplateId()]);
        } catch (\Exception $e) {
            if ($template->getTemplateId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove template with ID %1. Error: %2',
                        [$template->getTemplateId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove template. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function get($templateId)
    {
        if (!isset($this->templates[$templateId])) {
            /** @var \Amasty\Prot\Model\Template $template */
            $template = $this->templateFactory->create();
            $this->templateResource->load($template, $templateId);

            if (!$template->getTemplateId()) {
                throw new NoSuchEntityException(__('Template with specified ID "%1" not found.', $templateId));
            }

            $this->templates[$templateId] = $template;
        }

        return $this->templates[$templateId];
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    public function getAppliedTemplateByProduct(int $productId)
    {
        $templates = [];

        $ids = $this->getTemplateIdsByProduct($productId);
        if ($ids) {
            $this->searchCriteriaBuilder->addFilter(TemplateInterface::TEMPLATE_ID, $ids, 'in');

            foreach ($this->getList($this->searchCriteriaBuilder->create()) as $template) {
                $templates[] = $template;
            }
        }

        return $templates;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    public function getTemplateIdsByProduct(int $productId)
    {
        return $this->catalogCollectionFactory->create()->getAppliedTemplateByProduct($productId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Prot\Model\ResourceModel\Template\Collection $templateCollection */
        $templateCollection = $this->templateCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $templateCollection);
        }

        $searchResults->setTotalCount($templateCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $templateCollection);
        }

        $templateCollection->setCurPage($searchCriteria->getCurrentPage());
        $templateCollection->setPageSize($searchCriteria->getPageSize());

        return $templateCollection->getItems();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? : 'eq';
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $collection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $collection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * Check is template in schedule for productId (in EE productId = rowId)
     *
     * @param int $templateId
     * @param int $productId
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isTemplateScheduled($templateId, $productId)
    {
        $select = $this->templateResource->getConnection()->select()->from(
            ['template' => $this->templateResource->getMainTable()],
            ['cnt' => 'COUNT(*)']
        )->join(
            ['option' => $this->templateResource->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'template.template_id = option.template_id',
            []
        )->join(
            ['schedule' => $this->templateResource->getTable(ScheduleInterface::MAIN_TABLE)],
            'option.option_id = schedule.amasty_option_id',
            []
        )->where(
            'schedule.product_id = ?',
            $productId
        )->where(
            'template.template_id = ?',
            $templateId
        );

        return (bool)$this->templateResource->getConnection()->fetchOne($select);
    }

    /**
     * @param int $collectionSize
     * @param bool $isApplied
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSuccessMessage(int $collectionSize, $isApplied)
    {
        if ($collectionSize) {
            if ($isApplied) {
                return $this->getAppliedMessage($collectionSize);
            } else {
                return $this->getAddedToQueueMessage($collectionSize);
            }
        }

        return __('No records have been updated.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getAppliedMessage($collectionSize = 0)
    {
        return __('A total of %1 record(s) have been added to the template.', $collectionSize);
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getAddedToQueueMessage($collectionSize = 0)
    {
        return __(
            'A total of %1 record(s) have been added to the queue. Wait a couple of minutes or run apply process'
            . ' with command line or Product Options Template List action.',
            $collectionSize
        );
    }
}
