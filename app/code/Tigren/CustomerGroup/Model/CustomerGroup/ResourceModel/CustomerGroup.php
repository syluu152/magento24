<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Model\CustomerGroup\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 *
 */
class CustomerGroup extends AbstractDb
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('tigren_discount_for_customer_group', 'id');
    }
}
