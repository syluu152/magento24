<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\AdvancedCheckout\Controller\Advanced;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;

/**
 *
 */
class ClearCart extends Action
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
     * @param Context $context
     * @param Session $session
     * @param Item $quoteItem
     */
    function __construct(
        Context $context,
        Session $session,
        Item $quoteItem
    ) {
        $this->_quoteItem = $quoteItem;
        $this->_session = $session;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $allItems = $this->_session->getQuote()->getAllVisibleItems();
        $quote_Id = $this->_session->getQuoteId();
        $error = 0;
        foreach ($allItems as $item) {
            $itemId = $item->getItemId();//item id of particular item
            $allItems = $this->_quoteItem->load($itemId);
            try {
                $allItems->delete();
            } catch (Exception $e) {
                $error++;
            }
        }
        if (!empty($quote_Id)) {
            $objectManager = ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('quote');
            $sql = "DELETE  FROM " . $tableName . " WHERE entity_id = " . $quote_Id;
            $connection->query($sql);
        }

        if ($error) {
            $this->messageManager->addErrorMessage("fail " . $error . " item");
            echo "error";
        } else {
            echo "success";
        }
    }
}
