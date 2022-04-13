<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Plugin\Magento\Catalog\Model;

use Magento\Framework\Registry;
use Product\Discount\Helper\Data;

/**
 *
 */
class Product
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Data $helper
     * @param Registry $registry
     */
    public function __construct(
        Data $helper,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return float|int
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        //        $productId = $subject->getId();
        //        $relatedProducts = $subject->getRelatedProductIds();

        $discount = $this->helper->getDiscountMap($subject);
        $price = $result * (1 - $discount / 100);
        return $price;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
