<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Controller\Adminhtml\Discount;


class Edit extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;
    protected $_contentFactory;
    protected $_messageManager;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context,
        //        \Exam\Testimonial\Model\TestimonialContentFactory $contentFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_messageManager = $messageManager;
        //        $this->_contentFactory = $contentFactory;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        //        $model = $this->_contentFactory->create();
        $modelId = $this->getRequest()->getParam('id');
        $model->load($modelId);
        $this->_coreRegistry->register('discount', $model);

        $resultPage->getConfig()->getTitle()->prepend(__("Edit Discount"));
        $resultPage->setActiveMenu('Tigren_CustomerGroup::DiscountManagement');
        return $resultPage;
    }
}
