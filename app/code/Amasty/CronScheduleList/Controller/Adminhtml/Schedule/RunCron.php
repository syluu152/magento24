<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Controller\Adminhtml\Schedule;

use Amasty\CronScheduleList\Controller\Adminhtml\AbstractSchedule;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;
use Symfony\Component\Process\PhpExecutableFinder;

class RunCron extends AbstractSchedule
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var PhpExecutableFinder
     */
    private $phpExecutableFinder;

    protected $redirectUrl = 'amasty_cronschedulelist/schedule/index';

    public function __construct(
        Context $context,
        CacheInterface $cache,
        Shell $shell,
        PhpExecutableFinder $phpExecutableFinder
    ) {
        parent::__construct($context);

        $this->cache = $cache;
        $this->shell = $shell;
        $this->phpExecutableFinder = $phpExecutableFinder;
    }

    public function execute()
    {
        $phpPath = $this->phpExecutableFinder->find() ?: 'php';
        $this->cache->clean(['crontab']);

        try {
            $this->shell->execute($phpPath . ' %s cron:run', [BP . '/bin/magento']);
            $this->messageManager->addSuccessMessage(__('Job\'s generation started'));
        } catch (LocalizedException $e) {
            $this->messageManager->addNoticeMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect($this->redirectUrl);
    }
}
