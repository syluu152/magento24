<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel;

use \Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Template extends AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TemplateInterface::MAIN_TABLE, TemplateInterface::TEMPLATE_ID);
    }

    /**
     * @param $templateId
     *
     * @return string
     */
    public function getOptionCount($templateId)
    {
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getMainTable()], [])
            ->joinInner(
                ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
                'am_options.template_id = main_table.template_id AND main_table.template_id = ' . $templateId,
                ['count(am_options.option_id)']
            )->joinInner(
                ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
                'prot_relation.parent_option_id = am_options.option_id',
                []
            );

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $templateId
     *
     * @return array
     */
    public function getAllTemplateProducts(int $templateId)
    {
        $select = $this->getTemplateFilterSelect($templateId);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param int $templateId
     * @param array $productIds
     * @param string $field
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterProductsToRemove(int $templateId, array $productIds, string $field)
    {
        $ids = $this->getTemplateEntityIds($templateId, $field);

        return array_intersect($productIds, $ids);
    }

    /**
     * @param int $templateId
     * @param string $field
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTemplateEntityIds(int $templateId, string $field)
    {
        $select = $this->getTemplateFilterSelect($templateId);
        if ($field === 'row_id') {
            $select->reset('columns')->join(
                ['pe' => $this->getTable('catalog_product_entity')],
                'pe.row_id = po.product_id',
                ['product_id' => 'pe.entity_id']
            );
        }

        $ids = $this->getConnection()->fetchCol($select);
        $ids = $ids ? array_unique($ids) : [];

        return $ids;
    }

    /**
     * @param int $templateId
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTemplateFilterSelect(int $templateId)
    {
        $select = $this->getConnection()->select()
            ->from(['main_table' => $this->getMainTable()], [])
            ->join(
                ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
                'am_options.template_id = main_table.template_id AND main_table.template_id = ' . $templateId,
                null
            )->join(
                ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
                'prot_relation.parent_option_id = am_options.option_id',
                null
            )->join(
                ['po' => $this->getTable('catalog_product_option')],
                'prot_relation.option_id = po.option_id',
                ['product_id' => 'po.product_id']
            );

        return $select;
    }
}
