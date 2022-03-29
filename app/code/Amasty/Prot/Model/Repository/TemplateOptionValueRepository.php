<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Repository;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Amasty\Prot\Api\TemplateOptionValueRepositoryInterface;
use Amasty\Prot\Model\TemplateOptionValueFactory;
use Amasty\Prot\Model\ResourceModel\TemplateOptionValue as TemplateOptionValueResource;
use Amasty\Prot\Model\ResourceModel\TemplateOptionValue\CollectionFactory;
use Amasty\Prot\Model\ResourceModel\TemplateOptionValue\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TemplateOptionValueRepository implements TemplateOptionValueRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TemplateOptionValueFactory
     */
    private $templateOptionValueFactory;

    /**
     * @var TemplateOptionValueResource
     */
    private $templateOptionValueResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $templateOptionValues;

    /**
     * @var CollectionFactory
     */
    private $templateOptionValueCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        TemplateOptionValueFactory $templateOptionValueFactory,
        TemplateOptionValueResource $templateOptionValueResource,
        CollectionFactory $templateOptionValueCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->templateOptionValueFactory = $templateOptionValueFactory;
        $this->templateOptionValueResource = $templateOptionValueResource;
        $this->templateOptionValueCollectionFactory = $templateOptionValueCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(TemplateOptionValueInterface $templateOptionValue)
    {
        try {
            if ($templateOptionValue->getId()) {
                $templateOptionValue = $this->getById($templateOptionValue->getId())
                    ->addData($templateOptionValue->getData());
            }
            $this->templateOptionValueResource->save($templateOptionValue);
            unset($this->templateOptionValues[$templateOptionValue->getId()]);
        } catch (\Exception $e) {
            if ($templateOptionValue->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save templateOptionValue with ID %1. Error: %2',
                        [$templateOptionValue->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new templateOptionValue. Error: %1', $e->getMessage()));
        }

        return $templateOptionValue;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->templateOptionValues[$id])) {
            /** @var \Amasty\Prot\Model\TemplateOptionValue $templateOptionValue */
            $templateOptionValue = $this->templateOptionValueFactory->create();
            $this->templateOptionValueResource->load($templateOptionValue, $id);
            if (!$templateOptionValue->getId()) {
                throw new NoSuchEntityException(__('TemplateOptionValue with specified ID "%1" not found.', $id));
            }
            $this->templateOptionValues[$id] = $templateOptionValue;
        }

        return $this->templateOptionValues[$id];
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        return $this->templateOptionValueFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function delete(TemplateOptionValueInterface $templateOptionValue)
    {
        try {
            $this->templateOptionValueResource->delete($templateOptionValue);
            unset($this->templateOptionValues[$templateOptionValue->getId()]);
        } catch (\Exception $e) {
            if ($templateOptionValue->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove templateOptionValue with ID %1. Error: %2',
                        [$templateOptionValue->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove templateOptionValue. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $templateOptionValueModel = $this->getById($id);
        $this->delete($templateOptionValueModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\Prot\Model\ResourceModel\TemplateOptionValue\Collection $templateOptionValueCollection */
        $templateOptionValueCollection = $this->templateOptionValueCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $templateOptionValueCollection);
        }

        $searchResults->setTotalCount($templateOptionValueCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $templateOptionValueCollection);
        }

        $templateOptionValueCollection->setCurPage($searchCriteria->getCurrentPage());
        $templateOptionValueCollection->setPageSize($searchCriteria->getPageSize());

        $templateOptionValues = [];
        /** @var TemplateOptionValueInterface $templateOptionValue */
        foreach ($templateOptionValueCollection->getItems() as $templateOptionValue) {
            $templateOptionValues[] = $this->getById($templateOptionValue->getId());
        }

        $searchResults->setItems($templateOptionValues);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $templateOptionValueCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $templateOptionValueCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $templateOptionValueCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $templateOptionValueCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $templateOptionValueCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $templateOptionValueCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
