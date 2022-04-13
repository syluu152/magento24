<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Product\Discount\Model\Discount\DiscountFactory;
use Product\Discount\Model\Discount\ResourceModel\Discount\Collection as DiscountCollection;

/**
 * @api
 * @deprecated 100.2.0
 * @SuppressWarnings(PHPMD.LongVariable)
 * @since 100.0.2
 */
class Data extends AbstractHelper
{
    protected $_customerSession;
    protected $discountCollection;
    protected $discountFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        DiscountCollection $discountCollection,
        DiscountFactory $discountFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->discountCollection = $discountCollection;
        $this->discountFactory = $discountFactory;

        parent::__construct($context);
    }

    public function getDiscountMap($product)
    {
        $id = $product->getId();
        if ($id) {
            //            $normalPrice = $product->getPrice();
            $idCustomerGroup = $this->_customerSession->getCustomerGroupId();
            $collection = $this->discountCollection;
            $percentDiscount = 0;

            foreach ($collection as $item) {
                $productIdInDiscount = $item->getProductId();
                $idInCustomerGroup = $item->getIdCusGroup();
                $productIdInDiscountArr = explode(',', $productIdInDiscount);
                $idInCustomerGroupArr = explode(',', $idInCustomerGroup);
                if (count($productIdInDiscountArr) > 0 && in_array($id,
                        $productIdInDiscountArr) && in_array($idCustomerGroup, $idInCustomerGroupArr)) {
                    $percentDiscount = $item->getData('discount_amount');
                    break;
                }
            }
            return $percentDiscount;
        }
    }
}
