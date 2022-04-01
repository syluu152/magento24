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
            $item = $observer->getEvent()->getData('quote_item');
            $item = ($item->getParentItem() ? $item->getParentItem() : $item);
            $id = $item->getId();
            $idCustomerGroup = $this->_customerSession->getCustomerGroupId();
            $collection = $this->discountCollection;
            $discountId = '';
            foreach ($collection as $item) {
                $productIdInDiscount = $item->getProductId();
                $idInCustomerGroup = $item->getIdCusGroup();
                $productIdInDiscountArr = explode(',', $productIdInDiscount);
                $idInCustomerGroupArr = explode(',', $idInCustomerGroup);
                if (in_array($id, $productIdInDiscountArr) && in_array($idCustomerGroup, $idInCustomerGroupArr)) {
                    $discountId = $item->getId();
                    break;
                }
            }
            if ($discountId) {
                $discountData = $this->discountFactory->create()->load($discountId); //load Model
                $percentDiscount = $discountData->getData('discount_amount');
            }
            $discountAmount = (empty($percentDiscount)) ? 0 : $percentDiscount['discount_amount'];

            $oldPrice = $item->getProduct()->getPrice();
            $price = $oldPrice * (1 - $discountAmount / 100);
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
