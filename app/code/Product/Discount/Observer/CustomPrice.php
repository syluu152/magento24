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
            //        $id = $item->getId();
            $id = $product = $observer->getEvent()->getProduct()->getId();
            $idCustomerGroup = $this->_customerSession->getCustomerGroupId();
            $collection = $this->discountCollection;
            $percentDiscount = 0;
            $logger->info(print_r('$id', true));
            $logger->info(print_r($id, true));

            $logger->info(print_r('$idCustomerGroup', true));
            $logger->info(print_r($idCustomerGroup, true));

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


                    $logger->info(print_r($discountId, true));

                    break;
                }
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productCollection = $objectManager->create('Magento\Catalog\Model\Product')->load(product_id);
            $productPriceById = $productCollection->getPrice();

            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(print_r($productPriceById, true));

            //            $oldPrice = $item->getProduct()->getPrice();
            //            $price = $oldPrice * (1 - $percentDiscount / 100);
            $price = $productPriceById * (1 - $percentDiscount / 100);

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
