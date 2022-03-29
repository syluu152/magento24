<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;
use Magento\Cron\Model\Schedule;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $statuses = [
            [
                'value' => Schedule::STATUS_SUCCESS, 'label' => '<span class="grid-severity-notice">'
                . htmlspecialchars(__("Success"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_PENDING, 'label' => '<span class="grid-severity-minor">'
                . htmlspecialchars(__("Pending"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_RUNNING, 'label' => '<span class="grid-severity-minor">'
                . htmlspecialchars(__("Running"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_ERROR, 'label' => '<span class="grid-severity-critical">'
                . htmlspecialchars(__("Error"))
                . '</span>'
            ],
            [
                'value' => Schedule::STATUS_MISSED, 'label' => '<span class="grid-severity-critical">'
                . htmlspecialchars(__("Missed"))
                . '</span>'
            ]
        ];

        return $statuses;
    }
}
