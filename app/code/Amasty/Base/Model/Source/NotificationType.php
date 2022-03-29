<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\Source;

class NotificationType implements \Magento\Framework\Option\ArrayInterface
{
    public const GENERAL = 'INFO';
    public const SPECIAL_DEALS = 'PROMO';
    public const AVAILABLE_UPDATE = 'INSTALLED_UPDATE';
    public const UNSUBSCRIBE_ALL = 'UNSUBSCRIBE_ALL';
    public const TIPS_TRICKS = 'TIPS_TRICKS';

    public function toOptionArray()
    {
        $types = [
            [
                'value' => self::GENERAL,
                'label' => __('General Info')
            ],
            [
                'value' => self::SPECIAL_DEALS,
                'label' => __('Special Deals')
            ],
            [
                'value' => self::AVAILABLE_UPDATE,
                'label' => __('Available Updates')
            ],
            [
                'value' => self::TIPS_TRICKS,
                'label' => __('Magento Tips & Tricks')
            ],
            [
                'value' => self::UNSUBSCRIBE_ALL,
                'label' => __('Unsubscribe from all')
            ]
        ];

        return $types;
    }
}
