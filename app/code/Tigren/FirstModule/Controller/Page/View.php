<?php

//declare(strict_types=1);

namespace Tigren\FirstModule\Controller\Page;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\Page;

class View extends Action
{
    public function execute()
    {
//        /** @var Json $jsonResult */
//        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
//        $jsonResult->setData([
//            'message' => 'My First Page'
//        ]);
//        return $jsonResult;
        /** @var Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        /** @var Template $block */
        $block = $page->getLayout()->getBlock('tigren.first.layout.example');
        $block->setData('custom_parameter', 'Data from the Controller');
        return $page;
    }
}
