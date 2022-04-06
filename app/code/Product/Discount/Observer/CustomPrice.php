<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Observer;

use Product\Discount\Model\Discount\ResourceModel\Discount\Collection as DiscountCollection;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Product\Discount\Model\Discount\DiscountFactory;

//load model

class CustomPrice implements ObserverInterface
{
    protected $_customerSession;
    protected $discountCollection;
    protected $discountFactory;

    public function __construct(
        Session $customerSession,
        DiscountCollection $discountCollection,
        DiscountFactory $discountFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->discountCollection = $discountCollection;
        $this->discountFactory = $discountFactory; //load Model
    }

    public function execute(Observer $observer)
    {
        try {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $item = $observer->getEvent()->getData('quote_item');
            $item = $item->getParentItem() ? $item->getParentItem() : $item;
            $product = $observer->getEvent()->getProduct();
            $id = $product->getId();
            $normalPrice = $product->getPrice();
            $idCustomerGroup = $this->_customerSession->getCustomerGroupId();
            $collection = $this->discountCollection;
            $percentDiscount = 0;
            $logger->info(print_r('$id', true));
            $logger->info(print_r($id, true));

            $logger->info(print_r('$idCustomerGroup', true));
            $logger->info(print_r($idCustomerGroup, true));

            $logger->info(print_r('$normalPrice', true));
            $logger->info(print_r($normalPrice, true));

            foreach ($collection as $item) {
                $productIdInDiscount = $item->getProductId();
                $logger->info(print_r('$productIdInDiscount', true));
                $logger->info(print_r($productIdInDiscount, true));

                $idInCustomerGroup = $item->getIdCusGroup();
                $logger->info(print_r('$idInCustomerGroup', true));
                $logger->info(print_r($idInCustomerGroup, true));
                $productIdInDiscountArr = explode(',', $productIdInDiscount);
                $idInCustomerGroupArr = explode(',', $idInCustomerGroup);
                if (in_array($id, $productIdInDiscountArr) && in_array($idCustomerGroup, $idInCustomerGroupArr)) {
                    $logger->info(print_r('pass', true));
                    $discountId = $item->getId();
                    $percentDiscount = $item->getData('discount_amount');

                    $logger->info(print_r('$discountId', true));
                    $logger->info(print_r($discountId, true));

                    $logger->info(print_r('$percentDiscount', true));
                    $logger->info(print_r($percentDiscount, true));
                    break;
                }
            }
            $price = $normalPrice * (1 - $percentDiscount / 100);

            $logger->info(print_r('$price', true));
            $logger->info(print_r($price, true));

            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        } catch (\Exception $e) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(print_r($e->getMessage(), true));
        }
    }
}
