<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Controller\Adminhtml\Discount;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Tigren\CustomerGroup\Model\DiscountProduct\DiscountProductFactory;

/**
 *
 */
class RemoveProduct extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var DiscountProductFactory
     */
    protected $_discountProductFactory;
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var
     */
    protected $_coreRegistry;

    /**
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param DiscountProductFactory $discountProductFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        DiscountProductFactory $discountProductFactory,
        ManagerInterface $messageManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_discountProductFactory = $discountProductFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $model = $this->_discountProductFactory->create();
        $modelId = $this->getRequest()->getParam('id');

        $id_discount = $this->getRequest()->getParam('id_discount');

        //        echo $modelId .' - '.$id_discount;
        //        die();
        if ($model->load($modelId)) {
            $model->delete();
            $message = 'Đã xoá thành công';
            $this->_messageManager->addSuccessMessage($message);
            return $this->_redirect('discount_admin/discount/productOfDiscount/id/' . $id_discount, [$resultPage]);
        } else {
            $message = 'Xoá không được';
            $this->_messageManager->addErrorMessage($message);
            return $this->_redirect('discount_admin/discount/productOfDiscount/id/' . $id_discount, [$resultPage]);
        }
    }
}
