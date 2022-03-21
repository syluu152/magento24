<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;

/**
 *
 */
class Test extends Action
{

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $textDisplay = new DataObject(array('text' => 'Tigren'));
        $this->_eventManager->dispatch('tigren_simpleblog_display_text', ['tg_text' => $textDisplay]);
        echo $textDisplay->getText();
        exit;
    }
}
