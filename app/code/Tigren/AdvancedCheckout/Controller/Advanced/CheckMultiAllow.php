<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Advanced;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Item;

/**
 *
 */
class CheckMultiAllow extends Action
{

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var Item
     */
    protected $_quoteItem;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;

    /**
     * @param Context $context
     * @param Session $session
     * @param Item $quoteItem
     * @param ProductRepository $productRepository
     */
    function __construct(
        Context $context,
        Session $session,
        Item $quoteItem,
        Json $json,
        JsonFactory $resultJsonFactory,
        Cart $cart,
        ProductRepository $productRepository
    ) {
        $this->_quoteItem = $quoteItem;
        $this->json = $json;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cart = $cart;
        $this->_session = $session;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $response = [];
        try {
            $postValue = $this->getRequest()->getPostValue();
            $id_product = $postValue['id_item'];
            $product = $this->_productRepository->getById($id_product);
            $advancedCheckoutAtributeValue = $product->getCustomAttribute('product_advance_checkout') ? $product->getCustomAttribute('product_advance_checkout')->getValue() : '';

            $countQuoteItem = count($this->cart->getQuote()->getAllVisibleItems());

            if ($countQuoteItem > 0) {
                $response['isCartEmpty'] = false;
            } else {
                $response['isCartEmpty'] = true;
            }
            $response['attributeValue'] = $advancedCheckoutAtributeValue;
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
        }
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}

