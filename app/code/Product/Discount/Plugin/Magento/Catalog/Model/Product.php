<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Plugin\Magento\Catalog\Model;

use Product\Discount\Helper\Data;

class Product
{
    protected $helper;
    protected $registry;

    public function __construct(
        Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $productId = $subject->getId();
        $relatedProducts = $subject->getRelatedProductIds();

        $logger->info(print_r('$productId', true));
        $logger->info(print_r($productId, true));

        $logger->info(print_r('$relatedProducts', true));
        $logger->info(print_r($relatedProducts, true));
        // discount
        //        $originalPrice = $subject->getPrice();
        //
        //        $logger->info(print_r('$originalPrice', true));
        //        $logger->info(print_r($originalPrice, true));

        $discount = $this->helper->getDiscountMap($subject);


        $logger->info(print_r('$discount', true));
        $logger->info(print_r($discount, true));

        $price = $result * (1 - $discount / 100);

        $logger->info(print_r('$price', true));
        $logger->info(print_r($price, true));

        return $price;
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
