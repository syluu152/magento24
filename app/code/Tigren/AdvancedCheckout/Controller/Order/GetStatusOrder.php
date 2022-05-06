<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 *
 */
class GetStatusOrder extends Action
{

    /**
     * @var Session
     */
    protected $customer;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param Session $customer
     * @param CollectionFactory $orderCollection
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customer,
        CollectionFactory $orderCollection,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->customer = $customer;
        $this->orderCollection = $orderCollection;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return Json
     */
    public function execute()
    {
        //        $resultJson = $this->resultJsonFactory->create();
        //        return $resultJson->setData(['orderStatus' => $this->checkOrderStatus()]);
        $orderStatus = $this->checkOrderStatus();
        $response['orderStatus'] = $orderStatus;
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * @return bool
     */
    public function checkOrderStatus()
    {
        $orderStatus = true;
        foreach ($this->getOrdersOfCustomer() as $order) {
            if ($order->getStatus() !== "complete") {
                $orderStatus = false;
                break;
            }
        }
        return $orderStatus;
    }

    /**
     * @return Collection
     */
    public function getOrdersOfCustomer()
    {
        $customerOrder = $this->orderCollection->create()
            ->addAttributeToFilter('customer_email', $this->getEmailCustomerCurrent())->load();

        return $customerOrder;

    }

    /**
     * @return mixed
     */
    public function getEmailCustomerCurrent()
    {
        return $this->customer->getCustomer()->getEmail();
    }
}
