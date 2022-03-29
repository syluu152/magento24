<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\Value;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection
{
    protected function _renderFiltersBefore()
    {
        $this->_select->joinLeft(
            ['relation_table' => $this->getTable(TemplateOptionValueInterface::RELATION_TABLE)],
            sprintf(
                'relation_table.%s = main_table.option_type_id',
                TemplateOptionValueInterface::RELATION_VALUE_ID
            ),
            []
        )->joinLeft(
            ['template_value_table' => $this->getTable(TemplateOptionValueInterface::MAIN_TABLE)],
            sprintf(
                'relation_table.%s = template_value_table.%s',
                TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID,
                TemplateOptionValueInterface::ID
            ),
            [
                TemplateOptionValueInterface::DATA_IDENTIFIER => sprintf(
                    'template_value_table.%s',
                    TemplateOptionValueInterface::ID
                ),
                TemplateOptionValueInterface::SWATCH_VALUE
            ]
        );

        parent::_renderFiltersBefore();
    }
}
