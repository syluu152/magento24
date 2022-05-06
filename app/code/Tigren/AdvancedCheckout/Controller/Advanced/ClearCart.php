<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Advanced;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 *
 */
class ClearCart extends Action
{
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Json $json,
        JsonFactory $resultJsonFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $response = [];
        try {
            $cart = $this->checkoutSession->getQuote();
            $cart->removeAllItems()->collectTotals()->save();
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
