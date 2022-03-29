<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Model\Discount\ResourceModel\Discount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tigren\CustomerGroup\Model\Discount\ResourceModel\Discount;

/**
 *
 */
class Collection extends AbstractCollection
{

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Tigren\CustomerGroup\Model\Discount\Discount::class, Discount::class);
    }

    /**
     * @param $CustomerGroup
     * @return array|null
     */
    public function getListDiscount($CustomerGroup)
    {
        $today = date('Y/m/d h:i:s');

        $this->getSelect()->join(
            ['discountsForCustomer' => 'tigren_discount_for_customer_group'],
            'main_table.id=discountsForCustomer.id_discount',
            [
                'id_discount' => 'main_table.id',
                'discount_amount' => 'main_table.discount_amount',
                'priority' => 'main_table.priority',
                'start_time' => 'main_table.start_time',
                'end_time' => 'main_table.end_time',
                'name' => 'main_table.name'
            ]
        )->where('discountsForCustomer.id_customer_group=' . $CustomerGroup
        )->where('main_table.active=\'true\''
        )->where('main_table.end_time > \'' . $today . '\''
        )->where('main_table.start_time < \'' . $today . '\'');

        return $this->getData();
    }

}
