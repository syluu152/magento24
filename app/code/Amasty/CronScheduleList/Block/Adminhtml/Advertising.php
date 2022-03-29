<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Block\Adminhtml;

use Amasty\Base\Helper\Module;
use Magento\Backend\Block\Template;
use Magento\Framework\Module\Manager;

class Advertising extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_CronScheduleList::advertising.phtml';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Module
     */
    private $moduleHelper;

    public function __construct(
        Manager $moduleManager,
        Template\Context $context,
        Module $moduleHelper,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->moduleManager->isEnabled('Amasty_CronScheduler') || $this->moduleHelper->isOriginMarketplace()) {
            return '';
        }

        return parent::toHtml();
    }

    public function getLink()
    {
        return 'https://amasty.com/cron-scheduler-for-magento-2.html'
            . '?utm_source=extension&utm_medium=backend&utm_campaign=from_cron_schedule_list_to_cron_schedule_m2';
    }
}
