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
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 *
 */
class ProductOfDiscount extends Action
{
    /**
     * @var bool|PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $idDiscount = $this->getRequest()->getParam('id');

        $resultPage = $this->resultPageFactory->create();

        $block = $resultPage->getLayout()->getBlock('product_of_discount');

        $block->setData('id_discount', $idDiscount);

        $resultPage->setActiveMenu('Exam_Discount::Parent');
        $resultPage->getConfig()->getTitle()->prepend(__('Products Of Discount'));
        return $resultPage;
    }
}
