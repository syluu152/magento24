<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;

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
        UploaderFactory $fileUploaderFactory,
        Action\Context                                   $context

    )
    {

        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context);
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
