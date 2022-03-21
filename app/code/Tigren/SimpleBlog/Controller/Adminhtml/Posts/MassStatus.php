<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;
use Tigren\SimpleBlog\Model\PostsFactory;
use Tigren\SimpleBlog\Model\ResourceModel\PostsFactory as resPostsFactory;

/**
 *
 */
class MassStatus extends Posts
{
    /**
     * @var resPostsFactory
     */
    protected $_resPostsFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param PostsFactory $postsFactory
     * @param resPostsFactory $resPostsFactory
     */
    public function __construct(
        Context         $context,
        Registry        $coreRegistry,
        PageFactory     $resultPageFactory,
        PostsFactory    $postsFactory,
        resPostsFactory $resPostsFactory
    )
    {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $postsFactory);
        $this->_resPostsFactory = $resPostsFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $status = $this->getRequest()->getParam('status', 0);
        $postIds = $this->getRequest()->getParam('posts', array());
        if (count($postIds)) {
            $i = 0;
            foreach ($postIds as $postId) {
                try {
                    $postId = (int)$postId;
                    $model = $this->_postsFactory->create();
                    $resModel = $this->_resPostsFactory->create();
                    $model->setStatus($status)->setId($postId);
                    $resModel->save($model);
                    $i++;

                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
            if ($i > 0) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 item(s) were deleted.', $i)
                );
            }
        } else {
            $this->messageManager->addError(
                __('You can not item, Please check again')
            );
        }
        $this->_redirect('*/*/index');
    }
}
