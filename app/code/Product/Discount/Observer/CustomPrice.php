<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Product\Discount\Model\Discount\DiscountFactory;
use Product\Discount\Model\Discount\ResourceModel\Discount\Collection as DiscountCollection;


class CustomPrice implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var DiscountCollection
     */
    protected $discountCollection;
    /**
     * @var DiscountFactory
     */
    protected $discountFactory;

    protected $_cart;

    /**
     * @param Session $customerSession
     * @param DiscountCollection $discountCollection
     * @param DiscountFactory $discountFactory
     */
    public function __construct(
        Session $customerSession,
        DiscountCollection $discountCollection,
        DiscountFactory $discountFactory,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->_customerSession = $customerSession;
        $this->discountCollection = $discountCollection;
        $this->discountFactory = $discountFactory; //load Model
        $this->cart = $cart;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $id = $product->getId();
        if ($id) {
            $normalPrice = $product->getPrice();
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
            $price = $normalPrice * (1 - $percentDiscount / 100);
            $product->setPrice($price);
            // second way: add to cart and save data to cart
            // $product->setPrice($price); // without save this does the trick
            // $this->cart->addProduct($product);
            // $this->cart->save();
        }
    }
}
