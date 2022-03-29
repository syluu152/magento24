<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Catalog;

class Product extends \Magento\Catalog\Model\ResourceModel\Product
{
    /**
     * @param int $origId
     * @param array $productIds
     * @param string $linkField
     */
    public function updateEntityOptionsValues(int $origId, array $productIds, string $linkField)
    {
        $connection = $this->getConnection();
        $connection->update(
            $this->getTable($this->getEntityTable()),
            ['has_options' => 1],
            $connection->quoteInto($linkField . ' IN(?)', $productIds)
        );
    }
}
