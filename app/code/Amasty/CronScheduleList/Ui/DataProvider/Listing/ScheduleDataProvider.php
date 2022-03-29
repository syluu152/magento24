<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\CronScheduleList\Ui\DataProvider\Listing;

class ScheduleDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\CronScheduleList\Model\ScheduleCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\CronScheduleList\Model\ScheduleCollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create()->removeActivitySchedule();
        }

        return $this->collection;
    }
}
