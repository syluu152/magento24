<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Amasty\CronScheduleList\Model\ScheduleCollectionFactory as CollectionFactory;
use Amasty\CronScheduleList\Model\DateTimeBuilder;

class Notice extends Template
{
    protected $_template = 'Amasty_CronScheduleList::notice.phtml';

    /**
     * @var CollectionFactory
     */
    private $jobsCollection;

    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    public function __construct(
        CollectionFactory $jobsCollection,
        DateTimeBuilder $dateTimeBuilder,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jobsCollection = $jobsCollection;
        $this->dateTimeBuilder = $dateTimeBuilder;
    }

    public function getLastActivity()
    {
        $collection = $this->jobsCollection->create();

        $item = $collection->getLastActivity();

        if ($item->getId()) {
            return $this->dateTimeBuilder->formatDate($item->getData('finished_at'));
        } else {
            return __('never');
        }
    }
}
