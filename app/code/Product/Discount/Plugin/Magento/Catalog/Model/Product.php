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
        //        $productId = $subject->getId();
        //        $relatedProducts = $subject->getRelatedProductIds();

        $discount = $this->helper->getDiscountMap($subject);
        $price = $result * (1 - $discount / 100);
        return $price;
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
