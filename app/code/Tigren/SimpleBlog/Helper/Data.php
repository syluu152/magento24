<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 *
 */
class Data extends AbstractHelper
{
    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * @param $date
     * @return bool
     */
    function checkDate($date = '')
    {
        $ok = false;
        if ($date != '') {
            $day = date('w', strtotime($date));
            if ($day == 0)
                $ok = true;
        }
        return $ok;
    }

    /**
     * @param string $path
     * @return mixed
     */
    function getBlogSetting($path = '')
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
