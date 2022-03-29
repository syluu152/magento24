<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\BlogManager\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Tigren\BlogManager\Model\ResourceModel\Blog\Collection;
use Tigren\BlogManager\Model\ResourceModel\Blog\CollectionFactory;

/**
 *
 */
class BlogList extends Template
{
    /**
     * @var CollectionFactory
     */
    public $blogCollection;

    /**
     * @param Context $context
     * @param CollectionFactory $blogCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $blogCollection,
        array $data = []
    ) {
        $this->blogCollection = $blogCollection;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getBlogs()
    {
        $collection = $this->blogCollection->create();
        return $collection;
    }
}
