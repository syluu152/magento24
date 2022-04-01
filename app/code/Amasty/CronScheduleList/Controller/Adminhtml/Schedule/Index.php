<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Controller\Adminhtml\Schedule;

use Amasty\CronScheduleList\Controller\Adminhtml\AbstractSchedule;
use Magento\Framework\Controller\ResultFactory;

class Index extends AbstractSchedule
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_CronScheduleList::schedule_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Tasks List'));
        $resultPage->addBreadcrumb(__('Cron Tasks List'), __('Cron Tasks List'));

        return $resultPage;
    }
}