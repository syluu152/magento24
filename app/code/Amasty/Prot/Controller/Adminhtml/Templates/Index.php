<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Controller\Adminhtml\Templates;

use Amasty\Prot\Controller\Adminhtml\Templates;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Templates
{
    /**
     * @var \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->checkCronConfiguration();
        $this->initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Template List'));
        $this->_view->renderLayout();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Prot::templates')
            ->_addBreadcrumb(__('Template List'), __('Template List'));

        return $this;
    }

    protected function checkCronConfiguration()
    {
        $scheduleCollection = $this->getScheduledCollection();
        if ($scheduleCollection->getSize() == 0) {
            $this->messageManager->addNoticeMessage(
                __('No cron job "amprot_schedule" found. Please check your cron configuration.')
            );
        }

        $scheduleCollection = $this->getScheduledRunCollection();
        if ($scheduleCollection->getSize() !== 0) {
            $this->messageManager->addNoticeMessage(
                __('Cron job "amprot_schedule" is still running. Please delete it and check your error log.')
            );
        }
    }

    /**
     * @return \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected function getScheduledCollection()
    {
        $scheduleCollection = $this->collectionFactory->create()
            ->addFieldToFilter('job_code', ['eq' => 'amprot_schedule']);
        $scheduleCollection->getSelect()->order('schedule_id desc');

        return $scheduleCollection;
    }

    /**
     * @return \Magento\Cron\Model\ResourceModel\Schedule\Collection
     */
    protected function getScheduledRunCollection()
    {
        $scheduleCollection = $this->getScheduledCollection()
            ->addFieldToFilter('status', ['eq' => 'running'])
            ->addFieldToFilter('executed_at', ['lteq' => new \Zend_Db_Expr('NOW() - INTERVAL 1 DAY')]);

        return $scheduleCollection;
    }
}
