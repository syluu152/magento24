<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Cron;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager;

class Activity
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function execute()
    {
        $moduleManager = $this->moduleManager;

        if ($moduleManager->isEnabled('Amasty_CronScheduler')) {
            /** @var \Amasty\CronScheduler\Model\JobsGenerator $jobsGenerator */
            $jobsGenerator = ObjectManager::getInstance()->get(\Amasty\CronScheduler\Model\JobsGenerator::class);
            $jobsGenerator->execute();

            /** @var @var \Amasty\CronScheduler\Model\FailedJobsNotifier $emailNotifier */
            $emailNotifier = ObjectManager::getInstance()->get(\Amasty\CronScheduler\Model\FailedJobsNotifier::class);
            $emailNotifier->updateFailedJobs();
        }
    }
}
