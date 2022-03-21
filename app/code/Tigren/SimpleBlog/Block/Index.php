<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block;

use Magento\Framework\View\Element\Template;

/**
 *
 */
class Index extends Template
{
    /**
     * @return string
     */
    public function getHelloWorldTxt()
    {
        return 'Hello world 123!';
    }
}
