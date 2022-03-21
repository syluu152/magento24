<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Tigren\SimpleBlog\Model\PostsFactory;
use Tigren\SimpleBlog\Model\ResourceModel\Posts\CollectionFactory;


/**
 *
 */
class Detail extends Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var CollectionFactory
     */
    protected $_postsCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PostsFactory
     */
    protected $postsFactory;

    /**
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param CollectionFactory $postsCollectionFactory
     * @param array $data
     * @param StoreManagerInterface $storeManager
     * @param PostsFactory $postsFactory
     */
    public function __construct(
        Template\Context      $context,
        Registry              $coreRegistry,
        CollectionFactory     $postsCollectionFactory,
        array                 $data = [],
        StoreManagerInterface $storeManager,
        PostsFactory          $postsFactory
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_postsCollectionFactory = $postsCollectionFactory;
        $this->storeManager = $storeManager;
        $this->postsFactory = $postsFactory;
    }

    /**
     * @return $this|mixed
     */
    function getPostItems()
    {
        if ($this->_coreRegistry->registry('detail_items')) {
            $collection = $this->_coreRegistry->registry('detail_items');
        } else {
            $collection = $this->_postsCollectionFactory->create()
                ->addFieldToSelect(array('title', 'image', 'content', 'short_description', 'author', 'created_at'))
                ->setPageSize(10)
                ->setOrder('id', 'ASC');
            $this->_coreRegistry->register('detail_items', $collection);
        }
        return $collection;

    }

    /**
     * @param $url
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($url)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return '<img width="100" height="100" src="' . $mediaUrl . 'tigren/simpleblog/' . $url . '" />';
    }

    /**
     * @param $id
     * @return array|mixed|void|null
     */
    public function getDetailBlog($id)
    {
        $postModel = $this->postsFactory->create();
        if ($id) {
            $postModel->load($id);
            if ($postModel->getId()) {
                return $postModel->getData();
            }
        }
    }

}
