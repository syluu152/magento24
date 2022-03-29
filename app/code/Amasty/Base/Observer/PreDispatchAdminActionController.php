<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Observer;

use Amasty\Base\Model\Feed\NewsProcessor;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class PreDispatchAdminActionController implements ObserverInterface
{
    /**
     * @var Session
     */
    private $backendSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NewsProcessor
     */
    private $newsProcessor;

    public function __construct(
        NewsProcessor $newsProcessor,
        Session $backendAuthSession,
        LoggerInterface $logger
    ) {
        $this->backendSession = $backendAuthSession;
        $this->logger = $logger;
        $this->newsProcessor = $newsProcessor;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->backendSession->isLoggedIn()) {
            try {
                $this->newsProcessor->checkUpdate();
                $this->newsProcessor->removeExpiredItems();
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }
    }
}
