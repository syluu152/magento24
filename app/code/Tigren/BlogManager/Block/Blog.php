<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\BlogManager\Block;

/**
 *
 */
class Blog extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Tigren\BlogManager\Model\BlogFactory
     */
    public $blogFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Tigren\BlogManager\Model\BlogFactory $blogFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Tigren\BlogManager\Model\BlogFactory $blogFactory,
        array $data = []
    ) {
        $this->blogFactory = $blogFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|\Tigren\BlogManager\Model\Blog
     */
    public function getBlog()
    {
        $blogId = $this->getRequest()->getParam('id');
        return $this->blogFactory->create()->load($blogId);
    }
}
