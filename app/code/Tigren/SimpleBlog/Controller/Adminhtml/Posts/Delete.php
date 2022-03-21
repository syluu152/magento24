<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Exception;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;

/**
 *
 */
class Delete extends Posts
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $postId = (int)$this->getRequest()->getParam('id');

        if ($postId) {
            /** @var $postModel \Tigren\SimpleBlog\Model\Posts */
            $postModel = $this->_postsFactory->create();
            $postModel->load($postId);

            // Check this news exists or not
            if (!$postModel->getId()) {
                $this->messageManager->addError(__('This news no longer exists.'));
            } else {
                try {
                    // Delete news
                    $postModel->delete();
                    $this->messageManager->addSuccess(__('The news has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $postModel->getId()]);
                }
            }
        }
    }
}
