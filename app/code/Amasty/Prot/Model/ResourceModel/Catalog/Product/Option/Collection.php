<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Catalog\Product\Option;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection as OptionCollection;
use Magento\Framework\DB\Select;

class Collection extends OptionCollection
{
    const OPTION_JOINED_FIELDS = [
        'template_option_id' => 'am_options.option_id',
        TemplateOptionInterface::TEMPLATE_ID,
        TemplateOptionInterface::DEPENDENCY,
        TemplateOptionInterface::FONT_SIZE,
        TemplateOptionInterface::FONT_COLOR,
        TemplateOptionInterface::USE_SWATCHES,
        TemplateOptionInterface::OPTIONS_LIST_TYPE,
    ];

    /**
     * @param int $productId
     * @param int $amastyOptionId
     * @param int $storeId
     *
     * @return \Magento\Catalog\Model\Product\Option
     */
    public function loadOption(int $productId, int $amastyOptionId, int $storeId)
    {
        $amastyOptionId = (int)$amastyOptionId;
        $this->setPageSize(1);

        $this->getSelect()->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id AND prot_relation.parent_option_id=' . $amastyOptionId,
            []
        );

        $items = $this->getProductOptions($productId, $storeId);

        return array_shift($items);
    }

    /**
     * @param int $templateId
     * @param int $storeId
     * @param $productId
     * @param bool $withSchedule
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function loadOptions(int $templateId, int $storeId, $productId, $withSchedule = false)
    {
        $templateId = (int)$templateId;
        if ($templateId && !$productId) {
            $this->addStagingSkip();
        }
        $productId = $productId ?: $this->getOriginalProductId($templateId);

        $this->getSelect()->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id ',
            ['parent_option_id' => 'prot_relation.parent_option_id']
        )->joinLeft(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = prot_relation.parent_option_id',
            self::OPTION_JOINED_FIELDS
        )->group('parent_option_id');

        if ($templateId) {
            $this->getSelect()->where('am_options.template_id = ?', $templateId);
        }

        if ($withSchedule) {
            $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
            $this->getSelect()->joinLeft(
                ['schedule' => $this->getTable(ScheduleInterface::MAIN_TABLE)],
                'schedule.amasty_option_id = am_options.option_id AND schedule.product_id = cpe.' . $linkField,
                [ScheduleInterface::SCHEDULE_ID]
            );
        }

        return $this->getProductOptions($productId, $storeId);
    }

    /**
     * @param int $templateId
     * @param array $productIds
     */
    public function removeFromIds(int $templateId, array $productIds)
    {
        $this->addStagingSkip();
        $this->addFieldToFilter('product_id', ['in' => $productIds]);
        $this->getSelect()->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            []
        )->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = prot_relation.parent_option_id AND am_options.template_id=' . $templateId,
            []
        );

        foreach ($this->getItems() as $item) {
            $item->delete();
        }
    }

    /**
     * @param int $templateId
     *
     * @return $this
     */
    public function applyTemplateFilter(int $templateId)
    {
        $this->getSelect()->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            []
        )->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = prot_relation.parent_option_id AND am_options.template_id=' . $templateId,
            []
        );

        $this->addStagingSkip();

        return $this;
    }

    /**
     * @param int $templateId
     *
     * @return int
     */
    public function getOriginalProductId(int $templateId)
    {
        $select = clone $this->getSelect();

        $select = $select->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            []
        )->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = prot_relation.parent_option_id AND am_options.template_id=' . $templateId,
            []
        );

        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('main_table.product_id');
        $productId = $this->getConnection()->fetchOne($select);

        return (int)$productId;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    public function getAppliedTemplateByProduct(int $productId)
    {
        $this->addFieldToFilter('product_id', $productId);
        $select = $this->getSelect()->distinct()->join(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            []
        )->join(
            ['am_options' => $this->getTable(TemplateOptionInterface::MAIN_TABLE)],
            'am_options.option_id = prot_relation.parent_option_id',
            ['am_options.template_id']
        );

        $this->addStagingSkip();

        $select->reset(Select::COLUMNS)->columns(['am_options.template_id']);
        return $this->getConnection()->fetchCol($select);
    }

    private function addStagingSkip()
    {
        try {
            $this->getSelect()->setPart('disable_staging_preview', true);
        } catch (\Zend_Db_Select_Exception $exception) {
            null;//skip for CE version
        }
    }
}
