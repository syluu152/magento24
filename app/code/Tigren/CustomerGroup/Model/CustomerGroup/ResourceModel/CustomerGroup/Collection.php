<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Model\CustomerGroup\ResourceModel\CustomerGroup;

use Tigren\CustomerGroup\Model\CustomerGroup\ResourceModel\CustomerGroup;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

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
        $this->_init(\Tigren\CustomerGroup\Model\CustomerGroup\CustomerGroup::class, CustomerGroup::class);
    }

    /**
     * @param $id_discount
     * @return array
     */
    public function getDataCustomerGroup($id_discount)
    {
        $this->getSelect()->where('id_discount=' . $id_discount);

        return $this->getColumnValues('id_customer_group');
    }

    /**
     * @param $id_discount
     * @return array
     */
    public function getIdByDiscount($id_discount)
    {
        $this->getSelect()->where('id_discount=' . $id_discount);

        return $this->getColumnValues('id');
    }
}
