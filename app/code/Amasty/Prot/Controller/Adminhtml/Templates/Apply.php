<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Templates;

use Amasty\Prot\Controller\Adminhtml\Templates;
use Amasty\Prot\Model\ScheduleResolver;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;
use Amasty\Prot\Api\Data\TemplateInterface;

class Apply extends Templates
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScheduleResolver
     */
    private $scheduleResolver;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        ScheduleResolver $scheduleResolver
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->scheduleResolver = $scheduleResolver;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($templateId = $this->getRequest()->getParam('id')) {
            try {
                $this->scheduleResolver->executeByTemplateId((int)$templateId);
                $this->messageManager->addSuccessMessage(__('You have applied the template.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t apply template right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/');
    }
}
