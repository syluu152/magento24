<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api;

use Amasty\Prot\Api\Data\TemplateInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface TemplateRepositoryInterface
{
    /**
     * Save
     *
     * @param TemplateInterface $template
     *
     * @return TemplateInterface
     */
    public function save(TemplateInterface $template);

    /**
     * Get by id
     *
     * @param int $optionId
     *
     * @return TemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($optionId);

    /**
     * Delete
     *
     * @param TemplateInterface $template
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TemplateInterface $template);

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
     * Create new model
     *
     * @return \Amasty\Prot\Api\Data\TemplateInterface
     */
    public function createNew();

    /**
     * Assign options from template to product ids
     *
     * @param int $templateId
     * @param array $productIds
     * @param int $originalProductId
     * @param bool $isRow
     *
     * @return bool
     * @throws LocalizedException
     */
    public function assignTemplateToIds(int $templateId, array &$productIds, $originalProductId = null, $isRow = false);

    /**
     * Convert entity_id to row_id
     *
     * @param array $productIds
     *
     * @return int[]
     * @throws \Zend_Db_Select_Exception
     */
    public function getRowIds($productIds);

    /**
     * Remove template options from products
     *
     * @param int $templateId
     * @param array $productIds
     *
     * @return bool
     */
    public function removeFromIds(int $templateId, array &$productIds);

    /**
     * remove template if no options
     *
     * @param int $templateId
     */
    public function checkAndRemoveTemplate(int $templateId);

    /**
     * Get list of templates by product id
     *
     * @param int $productId
     *
     * @return \Amasty\Prot\Api\Data\TemplateInterface[]
     */
    public function getAppliedTemplateByProduct(int $productId);

    /**
     * @param int $productId
     *
     * @return int[]
     */
    public function getTemplateIdsByProduct(int $productId);

    /**
     * Check is template in schedule for productId (in EE productId = rowId)
     *
     * @param int $templateId
     * @param int $productId
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isTemplateScheduled($templateId, $productId);
}
