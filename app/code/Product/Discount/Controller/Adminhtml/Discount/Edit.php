<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Controller\Adminhtml\Discount;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Product\Discount\Model\Discount\DiscountFactory;

/**
 *
 */
class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;
    /**
     * @var DiscountFactory
     */
    protected $discountFactory;
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param DiscountFactory $discountFactory
     * @param ManagerInterface $messageManager
     * @param Registry $coreRegistry
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        DiscountFactory $discountFactory,
        ManagerInterface $messageManager,
        Registry $coreRegistry
    ) {
        $this->_messageManager = $messageManager;
        $this->discountFactory = $discountFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $model = $this->discountFactory->create();
        $modelId = $this->getRequest()->getParam('id');
        $model->load($modelId);
        $this->_coreRegistry->register('discount', $model);

        $resultPage->getConfig()->getTitle()->prepend(__("Edit Discount"));
        $resultPage->setActiveMenu('Product_Discount::DiscountManagement');
        return $resultPage;
    }
}
