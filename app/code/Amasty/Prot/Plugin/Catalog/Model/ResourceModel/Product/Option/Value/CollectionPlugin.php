<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Plugin\Catalog\Model\ResourceModel\Product\Option\Value;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;

class CollectionPlugin
{
    const JOIN_FLAG = 'value_relation_joined';

    /**
     * @param Collection $subject
     */
    public function beforeLoad(Collection $subject)
    {
        if (!$subject->getFlag(self::JOIN_FLAG)) {
            $subject->getSelect()->joinLeft(
                ['value_relation_table' => $subject->getTable(TemplateOptionValueInterface::RELATION_TABLE)],
                sprintf(
                    'value_relation_table.%s = main_table.option_type_id',
                    TemplateOptionValueInterface::RELATION_VALUE_ID
                ),
                [TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID]
            )->joinLeft(
                ['template_value_table' => $subject->getTable(TemplateOptionValueInterface::MAIN_TABLE)],
                sprintf(
                    'template_value_table.%s = value_relation_table.%s',
                    TemplateOptionValueInterface::ID,
                    TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID
                ),
                [TemplateOptionValueInterface::SWATCH_VALUE]
            );
            $subject->setFlag(self::JOIN_FLAG, true);
        }
    }
}
