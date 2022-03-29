<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Tigren\CustomerGroup\Model\DiscountProduct\ResourceModel\DiscountProduct\Collection as discountProduct;

/**
 *
 */
class CustomPrice implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $_customerSession;
    /**
     * @var discountProduct
     */
    protected $_discountProduct;

    /**
     * @param discountProduct $discountProductCollection
     * @param Session $customerSession
     */
    public function __construct(
        discountProduct $discountProductCollection,
        Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        $this->_discountProduct = $discountProductCollection;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        $idProduct = $item->getProductId();
        $idCustomerGroup = $this->_customerSession->getCustomerGroupId();

        $discount = $this->_discountProduct->getDiscountOfProduct($idCustomerGroup, $idProduct);
        $discountAmount = (empty($discount)) ? 0 : $discount['discount_amount'];

        $oldPrice = $item->getProduct()->getPrice();
        $price = $oldPrice * (1 - $discountAmount / 100);
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSuperMode(true);
    }
}
