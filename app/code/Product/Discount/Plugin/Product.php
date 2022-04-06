<?php

namespace Product\Discount\Plugin;

use Magento\Customer\Model\Session;
use Product\Discount\Model\Discount\DiscountFactory;
use Product\Discount\Model\Discount\ResourceModel\Discount\Collection as DiscountCollection;

class Product
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

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $id = $subject->getId();

        $logger->info(print_r('$id', true));
        $logger->info(print_r($id, true));

        if ($id) {
            $idCustomerGroup = $this->_customerSession->getCustomerGroupId();

            $logger->info(print_r('$idCustomerGroup', true));
            $logger->info(print_r($idCustomerGroup, true));

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

            $logger->info(print_r('$percentDiscount', true));
            $logger->info(print_r($percentDiscount, true));

            $result = $result * (1 - $percentDiscount / 100);

            $logger->info(print_r('$result', true));
            $logger->info(print_r($result, true));

            return $result;
        }
    }
}
