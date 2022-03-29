<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Model\ResourceModel\Schedule\Collection;
use Amasty\Prot\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Cron\Model\Schedule as CronSchedule;
use Psr\Log\LoggerInterface;

class ScheduleResolver
{
    const CRON_BATCH_SIZE = 10000;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\Prot\Model\Repository\TemplateOption
     */
    private $optionRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CollectionFactory $collectionFactory,
        \Amasty\Prot\Model\Repository\TemplateOption $optionRepository,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->optionRepository = $optionRepository;
        $this->logger = $logger;
    }

    /**
     * @param CronSchedule $schedule
     */
    public function executeByCron(CronSchedule $schedule)
    {
        $collection = $this->getScheduleCollection();
        $collection->setPageSize(self::CRON_BATCH_SIZE);

        $this->execute($collection);
    }

    public function executeByCommandLine()
    {
        $collection = $this->getScheduleCollection();

        $this->execute($collection);
    }

    /**
     * @param int $templateId
     */
    public function executeByTemplateId(int $templateId)
    {
        $collection = $this->getScheduleCollection();
        $collection->applyTemplateIdFilter($templateId);

        $this->execute($collection);
    }

    /**
     * @param Collection $collection
     */
    protected function execute($collection)
    {
        if ($collection->getSize()) {
            foreach ($collection as $item) {
                try {
                    $this->optionRepository->apply($item);
                    $item->delete();
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    /**
     * @return Collection
     */
    protected function getScheduleCollection()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setOrder(ScheduleInterface::CREATED_AT);

        return $collection;
    }
}
