<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;
use Magento\Cron\Model\Schedule;

class StatusFilter implements ArrayInterface
{
    public function toOptionArray()
    {
        $statuses = [
            ['value' => Schedule::STATUS_SUCCESS, 'label' => __('Success')],
            ['value' => Schedule::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Schedule::STATUS_RUNNING, 'label' => __('Running')],
            ['value' => Schedule::STATUS_ERROR, 'label' => __('Error')],
            ['value' => Schedule::STATUS_MISSED, 'label' => __('Missed')]
        ];

        return $statuses;
    }
}
