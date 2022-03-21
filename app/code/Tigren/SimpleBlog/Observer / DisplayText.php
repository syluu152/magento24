<?php
/*
 *  @author  Tigren Solutions <info@tigren.com>
 *   @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license  Open Software License (“OSL”) v. 3.0
 */

namespace Tigren\SimpleBlog\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 *
 */
class DisplayText implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Session $session
     */
    function __construct(
        Session $session
    )
    {
        $this->session = $session;
    }


    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        // TODO: Implement execute() method.
        $message = $observer->getData('hello_message');
        $message .= ' Tigren'; // change text
        $this->session->setTextMessage($message);
    }
}
