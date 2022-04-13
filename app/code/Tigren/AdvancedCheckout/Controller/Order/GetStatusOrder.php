<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Order;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class GetStatusOrder extends Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customer;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customer,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->customer = $customer;
        $this->orderCollection = $orderCollection;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['orderStatus' => $this->checkOrderStatus()]);
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
     * @return mixed
     */
    public function getEmailCustomerCurrent()
    {
        return $this->customer->getCustomer()->getEmail();
    }

    public function getOrdersOfCustomer()
    {
        $customerOrder = $this->orderCollection->create()
            ->addAttributeToFilter('customer_email', $this->getEmailCustomerCurrent())->load();

        return $customerOrder;

    }
}
