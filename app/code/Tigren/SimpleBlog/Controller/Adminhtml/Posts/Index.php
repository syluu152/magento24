<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;
use Tigren\SimpleBlog\Model\PostsFactory;

/**
 *
 */
class Index extends Posts
{
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param PostsFactory $postsFactory
     */
    public function __construct(
        Context      $context,
        Registry     $coreRegistry,
        PageFactory  $resultPageFactory,
        PostsFactory $postsFactory
    )
    {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $postsFactory);
    }

    /**
     * @return Page|void
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        /** @var Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Tigren_SimpleBlog::tigren_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Tigren Manage Posts'));

        return $resultPage;
    }
}
