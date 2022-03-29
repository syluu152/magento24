<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Model\ResourceModel\TemplateOption\Collection;

/**
 * @api
 */
interface TemplateOptionRepositoryInterface
{
    /**
     * Save
     *
     * @param TemplateOptionInterface $templateOption
     *
     * @return TemplateOptionInterface
     */
    public function save(TemplateOptionInterface $templateOption);

    /**
     * Get by id
     *
     * @param int $optionId
     *
     * @return TemplateOptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($optionId);

    /**
     * @param array $optionIds
     * @return Collection
     */
    public function getOptionsByOriginalOptionIds($optionIds);

    /**
     * Delete
     *
     * @param TemplateOptionInterface $templateOption
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TemplateOptionInterface $templateOption);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Apply option schedule
     *
     * @param \Amasty\Prot\Api\Data\ScheduleInterface $schedule
     *
     * @return void
     */
    public function apply(ScheduleInterface $schedule);

    /**
     * Apply options by option list
     *
     * @param array $amastyOptionIds
     * @param int $origId
     * @param int $productId
     *
     * @return void
     */
    public function applyOptions(array $amastyOptionIds, int $origId, int $productId);

    /**
     * Copy has_option value to catalog_product_entity
     *
     * @param int $origId
     * @param array $productIds
     */
    public function updateEntityOptionsValues(int $origId, array $productIds);

    /**
     * Create new empty model
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionInterface
     */
    public function createNew();

    /**
     * Delete data from relation table
     *
     * @param int $id
     * @param int $parentId
     */
    public function deleteRelation(int $id, int $parentId);

    /**
     * Get Product Options by template
     *
     * @param int $templateId
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getTemplateProductsOptions(int $templateId);

    /**
     * @param int $templateId
     * @param int $storeId
     * @param null|int $productId
     * @param bool $withSchedule
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getProductOptions(int $templateId = 0, $storeId = 0, $productId = null, $withSchedule = false);
}
