<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Tigren\SimpleBlog\Model\PostsFactory;

/**
 *
 */
class Posts extends Action
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var PostsFactory
     */
    protected $_postsFactory;

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
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_postsFactory = $postsFactory;

    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {

    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
