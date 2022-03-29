<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\BlogManager\Controller\Manage;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Tigren\BlogManager\Model\BlogFactory;

/**
 *
 */
class Delete extends AbstractAccount
{
    /**
     * @var BlogFactory
     */
    public $blogFactory;
    /**
     * @var Session
     */
    public $customerSession;
    /**
     * @var ManagerInterface
     */
    public $messageManager;

    /**
     * @param Context $context
     * @param BlogFactory $blogFactory
     * @param Session $customerSession
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        BlogFactory $blogFactory,
        Session $customerSession,
        ManagerInterface $messageManager
    ) {
        $this->blogFactory = $blogFactory;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $blogId = $this->getRequest()->getParam('id');
        $customerId = $this->customerSession->getCustomerId();
        $isAuthorised = $this->blogFactory->create()
            ->getCollection()
            ->addFieldToFilter('user_id', $customerId)
            ->addFieldToFilter('entity_id', $blogId)
            ->getSize();
        if (!$isAuthorised) {
            $this->messageManager->addError(__('You are not authorised to delete this blog.'));
            return $this->resultRedirectFactory->create()->setPath('blogmanager/manage');
        } else {
            $model = $this->blogFactory->create()->load($blogId);
            $model->delete();
            $this->messageManager->addSuccess(__('You have successfully deleted the blog.'));
        }
        return $this->resultRedirectFactory->create()->setPath('blogmanager/manage');
    }
}
