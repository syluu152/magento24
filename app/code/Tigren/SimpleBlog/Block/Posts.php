<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Tigren\SimpleBlog\Model\ResourceModel\Posts\CollectionFactory;

/**
 *
 */
class Posts extends Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var CollectionFactory
     */
    protected $_postsFactory;

    /**
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param CollectionFactory $postsFactory
     * @param array $data
     */
    public function __construct(
        Template\Context                                               $context,
        Registry                                                       $coreRegistry,
        CollectionFactory $postsFactory,
        array                                                          $data = []
    )
    {

        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_postsFactory = $postsFactory;
    }

    /**
     * @return $this|mixed
     */
    function getPostItems()
    {
        if ($this->_coreRegistry->registry('post_items')) {
            $collection = $this->_coreRegistry->registry('post_items');
        } else {
            $collection = $this->_postsFactory->create()
                ->addFieldToSelect(array('title', 'short_description', 'content', 'author', 'created_at'))
//                ->addFieldToFilter('status',1)
                ->setPageSize(10)
                ->setOrder('id', 'ASC');
//                ->setOrder('id');
            $this->_coreRegistry->register('post_items', $collection);
        }
        return $collection;

    }

}
