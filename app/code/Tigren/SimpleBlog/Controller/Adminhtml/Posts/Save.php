<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Adminhtml\Posts;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Tigren\SimpleBlog\Controller\Adminhtml\Posts;
use Tigren\SimpleBlog\Model\PostsFactory;

/**
 *
 */
class Save extends Posts
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;
    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param PostsFactory $postsFactory
     * @param Filesystem $fileSystem
     * @param AdapterFactory $adapterFactory
     */
    public function __construct(
        Context        $context,
        Registry       $coreRegistry,
        PageFactory    $resultPageFactory,
        PostsFactory   $postsFactory,
        FileSystem     $fileSystem,
        AdapterFactory $adapterFactory)
    {
        $this->fileSystem = $fileSystem;
        $this->adapterFactory = $adapterFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $postsFactory);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->getPost();
        if ($isPost) {
            $postsModel = $this->_postsFactory->create();
            $postsId = $this->getRequest()->getParam('id');

            if ($postsId) {
                $postsModel->load($postsId);
            }
            $formData = $this->getRequest()->getParam('post');
            $postsModel->setData($formData);
            $Image = $this->getRequest()->getFiles('image');

            $fileName = ($Image && array_key_exists('name', $Image)) ? $Image['name'] : null;
            if ($Image && $fileName) {
                try {
                    /** @var ObjectManagerInterface $uploader */
                    $uploader = $this->_objectManager->create(
                        'Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => 'image']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    /** @var AdapterInterface $imageAdapterFactory */
                    $imageAdapterFactory = $this->adapterFactory->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    /** @var Read $mediaDirectory */
                    $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);

                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('tigren/simpleblog/')
                    );
                    //$data['profile'] = 'Modulename/Profile/'. $result['file'];
                    $postsModel->setImage($result['file']); //Database field name
                } catch (Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            try {
                // Save news
                $postsModel->save();

                // Display success message
                $this->messageManager->addSuccess(__('The news has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $postsModel->getId(), '_current' => true]);
                    return;
                }

                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $postsId]);
        }
    }
}
