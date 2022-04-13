<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Product\Discount\Model\Discount\DiscountFactory;
use Product\Discount\Model\Discount\ResourceModel\Discount\Collection as DiscountCollection;
use Product\Discount\Helper\Data;


/**
 *
 */
class CustomPrice implements ObserverInterface
{
    protected $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $normalPrice = $product->getPrice();
        $discount = $this->helper->getDiscountMap($product);
        $price = $normalPrice * (1 - $discount / 100);
        $product->setPrice($price);
        // second way: add to cart and save data to cart
        // $product->setPrice($price); // without save this does the trick
        // $this->cart->addProduct($product);
        // $this->cart->save();
    }
}
