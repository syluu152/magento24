<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel;

use \Amasty\Prot\Api\Data\ScheduleInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Schedule extends AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ScheduleInterface::MAIN_TABLE, ScheduleInterface::SCHEDULE_ID);
    }
}
