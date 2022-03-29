<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Model\ResourceModel\Product\Option;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection;
use Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\Collection as CustomCollection;

class CollectionPlugin
{
    /**
     * @param Collection $subject
     * @param $productId
     * @param $storeId
     * @param bool $requiredOnly
     *
     * @return array
     */
    public function beforeGetProductOptions(Collection $subject, $productId, $storeId, $requiredOnly = false)
    {
        // do not apply for our collection
        if (!($subject instanceof CustomCollection)) {
            $subject->getSelect()->joinLeft(
                ['am_relation' => $subject->getTable(TemplateOptionInterface::RELATION_TABLE)],
                'am_relation.option_id = main_table.option_id',
                []
            )->joinLeft(
                ['am_options' => $subject->getTable(TemplateOptionInterface::MAIN_TABLE)],
                'am_options.option_id = am_relation.parent_option_id',
                ['template_id']
            );
        }

        return [$productId, $storeId, $requiredOnly];
    }
}
