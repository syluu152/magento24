<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Model\DiscountProduct\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 *
 */
class DiscountProduct extends AbstractDb
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('discount_for_products', 'id');
    }
}