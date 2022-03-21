<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 *
 */
class Status implements ArrayInterface
{
    /**
     *
     */
    const ENABLED = 1;
    /**
     *
     */
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled'),
        ];
        return $options;
    }

}
