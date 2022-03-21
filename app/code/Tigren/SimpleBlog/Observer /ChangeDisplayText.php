<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 *
 */
class ChangeDisplayText implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $displayText = $observer->getData('tg_text');
        echo $displayText->getText() . " - Event </br>";
        $displayText->setText('Execute event successfully.');
        return $this;
    }
}
