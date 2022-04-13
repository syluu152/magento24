<?php

namespace Tigren\AdvancedCheckout\Controller\Advanced;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Model\ProductRepository;

class CheckMultiAllow extends Action
{

    protected $_session;

    protected $_quoteItem;

    protected $_productRepository;

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

