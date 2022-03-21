<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Tigren\SimpleBlog\Helper\Data;
use Tigren\SimpleBlog\Model\ResourceModel\Posts\CollectionFactory;

/**
 *
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var CollectionFactory
     */
    protected $_postsFactory;
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $postsFactory
     * @param Data $dataHelper
     */
    public function __construct(
        Context                                    $context,
        PageFactory $resultPageFactory,
        CollectionFactory                          $postsFactory,
        Data                                       $dataHelper)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_postsFactory = $postsFactory;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        echo "Get Data From tigren_blog table <br>";
//        $numberPosts = $this->_dataHelper->getBlogSetting('blog/setting/number_posts');
//        echo $numberPosts;
//        echo "Number Posts = {$numberPosts}";
        $this->_postsFactory->create();
        $collection = $this->_postsFactory->create()
            ->addFieldToSelect(array('title', 'short_description', 'content', 'author', 'created_at'))
//            ->addFieldToFilter('author',Peter)
            ->setPageSize(10);
//            ->setPageSize($numberPosts); // get 2 items
//        var_dump($collection);
        echo '<pre>';
        print_r($collection->getData());
        echo '<pre>';
        echo "========== Check date, helper function ======== <br>";
        $date = '2022-03-09';
        if ($this->_dataHelper->checkDate($date)) {
            echo "Yes, {$date} is Sunday , I can go to your home";
        } else {
            echo "Yes, {$date} is not Sunday , I was to busy";
        }
    }
}
