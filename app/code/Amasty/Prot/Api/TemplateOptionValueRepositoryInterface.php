<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Api;

/**
 * @api
 */
interface TemplateOptionValueRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Prot\Api\Data\TemplateOptionValueInterface $templateOptionValue
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function save(\Amasty\Prot\Api\Data\TemplateOptionValueInterface $templateOptionValue);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @return \Amasty\Prot\Api\Data\TemplateOptionValueInterface
     */
    public function createNew();

    /**
     * Delete
     *
     * @param \Amasty\Prot\Api\Data\TemplateOptionValueInterface $templateOptionValue
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Prot\Api\Data\TemplateOptionValueInterface $templateOptionValue);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
