<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;
use Tigren\SimpleBlog\Model\PostsFactory;

/**
 *
 */
class Uploader extends Posts
{
    /**
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @param UploaderFactory $fileUploaderFactory
     * @param Action\Context $context
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        PostsFactory $postsFactory
    ) {
        parent::__construct($context, $coreRegistry, $resultPageFactory, $postsFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {

        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);

        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

        $uploader->setAllowRenameFiles(false);

        $uploader->setFilesDispersion(false);

        $path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath('images/');

        $uploader->save($path);

    }
}
