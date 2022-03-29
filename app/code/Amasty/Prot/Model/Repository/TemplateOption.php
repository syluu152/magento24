<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Repository;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\TemplateOptionRepositoryInterface;
use Amasty\Prot\Model\ResourceModel\TemplateOption as TemplateOptionResource;
use Amasty\Prot\Model\TemplateOptionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Amasty\Prot\Model\ResourceModel\TemplateOption\CollectionFactory;
use Amasty\Prot\Model\ResourceModel\TemplateOption\Collection;
use Amasty\Prot\Model\ResourceModel\TemplateOption as OptionResource;

class TemplateOption implements TemplateOptionRepositoryInterface, ArgumentInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TemplateOptionFactory
     */
    private $templateOptionFactory;

    /**
     * @var OptionResource
     */
    private $templateOptionResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $optionCache;

    /**
     * @var CollectionFactory
     */
    private $templateOptionCollectionFactory;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\CollectionFactory
     */
    private $catalogCollectionFactory;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option
     */
    private $optionResource;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\Value
     */
    private $optionValueResource;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Catalog\Product
     */
    private $productResource;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        TemplateOptionFactory $templateOptionFactory,
        OptionResource $templateOptionResource,
        CollectionFactory $templateOptionCollectionFactory,
        \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\CollectionFactory $catalogCollectionFactory,
        \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option $optionResource,
        \Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\Value $optionValueResource,
        \Amasty\Prot\Model\ResourceModel\Catalog\Product $productResource,
        MetadataPool $metadataPool
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->templateOptionFactory = $templateOptionFactory;
        $this->templateOptionResource = $templateOptionResource;
        $this->templateOptionCollectionFactory = $templateOptionCollectionFactory;
        $this->catalogCollectionFactory = $catalogCollectionFactory;
        $this->optionResource = $optionResource;
        $this->optionValueResource = $optionValueResource;
        $this->productResource = $productResource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @inheritdoc
     */
    public function apply(ScheduleInterface $schedule)
    {
        $this->applyOptions(
            [$schedule->getAmastyOptionId()],
            $schedule->getOriginalProductId(),
            $schedule->getProductId()
        );

        $this->updateEntityOptionsValues($schedule->getOriginalProductId(), [$schedule->getProductId()]);
    }

    /**
     * @inheritdoc
     */
    public function applyOptions(array $amastyOptionIds, int $origId, int $productId)
    {
        $this->optionResource->updateOptions($amastyOptionIds, $origId, $productId, $this->optionValueResource);
    }

    /**
     * @inheritdoc
     */
    public function updateEntityOptionsValues(int $origId, array $productIds)
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $this->productResource->updateEntityOptionsValues($origId, $productIds, $productMetadata->getLinkField());
    }

    /**
     * @inheritdoc
     */
    public function save(TemplateOptionInterface $templateOption)
    {
        try {
            if ($templateOption->getOptionId()) {
                $templateOption = $this->getById($templateOption->getOptionId())->addData($templateOption->getData());
            }
            $this->templateOptionResource->save($templateOption);
            unset($this->optionCache[$templateOption->getOptionId()]);
        } catch (\Exception $e) {
            if ($templateOption->getOptionId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save template Option with ID %1. Error: %2',
                        [$templateOption->getOptionId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new template Option. Error: %1', $e->getMessage()));
        }

        return $templateOption;
    }

    /**
     * @inheritdoc
     */
    public function getById($tabId)
    {
        if (!isset($this->optionCache[$tabId])) {
            /** @var \Amasty\Prot\Model\TemplateOption $templateOption */
            $templateOption = $this->templateOptionFactory->create();
            $this->templateOptionResource->load($templateOption, $tabId);
            if (!$templateOption->getOptionId()) {
                throw new NoSuchEntityException(__('Option with specified ID "%1" not found.', $tabId));
            }
            $this->optionCache[$tabId] = $templateOption;
        }

        return $this->optionCache[$tabId];
    }

    /**
     * @param array $optionIds
     * @return Collection
     */
    public function getOptionsByOriginalOptionIds($optionIds)
    {
        return $this->templateOptionCollectionFactory->create()->getOptionsByOriginalOptionIds($optionIds);
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        return $this->templateOptionFactory->create();
    }

    /**
     * @param int $templateId
     * @param int $storeId
     * @param null|int $productId
     * @param bool $withSchedule
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getProductOptions(int $templateId = 0, $storeId = 0, $productId = null, $withSchedule = false)
    {
        return $this->catalogCollectionFactory->create()->loadOptions($templateId, $storeId, $productId, $withSchedule);
    }

    /**
     * @param int $templateId
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getTemplateProductsOptions(int $templateId)
    {
        return $this->catalogCollectionFactory->create()->applyTemplateFilter($templateId)->getItems();
    }

    /**
     * @param int $id
     * @param int $parentId
     */
    public function deleteRelation(int $id, int $parentId)
    {
        if ($id && $parentId) {
            $this->optionResource->deleteRelation($id, $parentId);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(TemplateOptionInterface $templateOption)
    {
        try {
            $this->templateOptionResource->delete($templateOption);
            unset($this->optionCache[$templateOption->getOptionId()]);
        } catch (\Exception $e) {
            if ($templateOption->getOptionId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove templateOption with ID %1. Error: %2',
                        [$templateOption->getOptionId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove templateOption. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($tabId)
    {
        $templateOptionModel = $this->getById($tabId);
        $this->delete($templateOptionModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $templateOptionCollection */
        $templateOptionCollection = $this->templateOptionCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $templateOptionCollection);
        }

        $searchResults->setTotalCount($templateOptionCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $templateOptionCollection);
        }

        $templateOptionCollection->setCurPage($searchCriteria->getCurrentPage());
        $templateOptionCollection->setPageSize($searchCriteria->getPageSize());

        $templateOptionData = [];
        /** @var TemplateOptionInterface $templateOption */
        foreach ($templateOptionCollection->getItems() as $templateOption) {
            $templateOptionData[] = $this->getById($templateOption->getOptionId());
        }

        $searchResults->setItems($templateOptionData);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $templateOptionCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $templateOptionCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $templateOptionCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $templateOptionCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $templateOptionCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $templateOptionCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
