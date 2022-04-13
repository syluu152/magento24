<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Advanced;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\ProductRepository;

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
        ProductRepository $productRepository
    ) {
        $this->_quoteItem = $quoteItem;
        $this->_session = $session;
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $postValue = $this->getRequest()->getPostValue();
        $id_product = $postValue['id_item'];

        $product = $this->_productRepository->getById($id_product);
        $attributes = $product->getCustomAttribute('product_advance_checkout');

        if (!empty($attributes)) {
            $attributeValue = $attributes->getValue();
            echo $attributeValue;
        } else {
            echo "0";
        }
    }
}

