<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Controller\Adminhtml\Discount;


use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Product\Discount\Model\Discount\DiscountFactory;

/**
 *
 */
class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var DiscountFactory
     */
    protected $_discountFactory;
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
     * @param DiscountFactory $discountFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        DiscountFactory $discountFactory,
        ManagerInterface $messageManager
    ) {
        $this->_messageManager = $messageManager;
        $this->_discountFactory = $discountFactory;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $model = $this->_discountFactory->create();
        $modelId = $this->getRequest()->getParam('id');

        //        die("deleting");
        if ($model->load($modelId)) {
            $model->delete();
            $message = 'Đã xoá thành công';
            $this->_messageManager->addSuccessMessage($message);
            return $this->_redirect('discount_admin/discount/view', [$resultPage]);
        } else {
            $message = 'Xoá không được';
            $this->_messageManager->addErrorMessage($message);
            return $this->_redirect('discount_admin/discount/view', [$resultPage]);
        }
    }
}
