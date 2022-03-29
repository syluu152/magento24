<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\TemplateOption;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Amasty\Prot\Api\Data\TemplateOptionInterface;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(TemplateOptionInterface::OPTION_ID);
        $this->_init(
            \Amasty\Prot\Model\TemplateOption::class,
            \Amasty\Prot\Model\ResourceModel\TemplateOption::class
        );
    }

    /**
     * @param array $optionIds
     * @return Collection
     */
    public function getOptionsByOriginalOptionIds($optionIds)
    {
        $this->getSelect()->joinInner(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.parent_option_id = main_table.option_id',
            []
        )->columns([
            TemplateOptionInterface::DEPENDENCY,
            TemplateOptionInterface::OPTIONS_LIST_TYPE,
            TemplateOptionInterface::USE_SWATCHES,
            TemplateOptionInterface::FONT_COLOR,
            TemplateOptionInterface::FONT_SIZE,
            sprintf('prot_relation.%s', TemplateOptionInterface::PARENT_OPTION_ID),
            'originalOptionId' => 'prot_relation.option_id'
        ]);

        $this->addFieldToFilter('prot_relation.option_id', ['in' => $optionIds]);

        return $this;
    }
}
