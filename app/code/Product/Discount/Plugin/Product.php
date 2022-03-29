<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Plugin;

use Magento\Catalog\Pricing\Render\FinalPriceBox;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;

/**
 *
 */
class Product
{
    /**
     * @var mixed|null
     */
    protected $isLoggedIn;

    /**
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $objectManager = ObjectManager::getInstance();
        $httpContext = $objectManager->get('Magento\Framework\App\Http\Context');
        $this->isLoggedIn = $httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function afterIsSaleable(\Magento\Catalog\Model\Product $product)
    {

        if ($this->isLoggedIn) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param FinalPriceBox $subject
     * @param $result
     * @return mixed|string
     */
    function afterToHtml(FinalPriceBox $subject, $result)
    {
        if ($this->isLoggedIn) {
            return $result;
        } else {
            return '<p style="font-weight: bold; color: red" >Please Sign In</p>';
        }
    }
}
