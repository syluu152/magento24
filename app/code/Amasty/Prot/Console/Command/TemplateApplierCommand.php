<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TemplateApplierCommand extends Command
{
    const NAME = 'amprot:apply';

    /**
     * @var \Amasty\Prot\Model\ScheduleResolver
     */
    private $scheduleResolver;

    public function __construct(
        \Amasty\Prot\Model\ScheduleResolver $scheduleResolver
    ) {
        $this->scheduleResolver = $scheduleResolver;
        parent::__construct(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::NAME)->setDescription('Apply Amasty Product Options Templates');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->scheduleResolver->executeByCommandLine();
            $output->writeln(__('Product Options Templates have been applied.'));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
