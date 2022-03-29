<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Console\Command;

use Amasty\Base\Model\SysInfo\Command\LicenceService\RegisterLicenceKey as CommandRegisterLicenceKey;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterLicenceKey extends Command
{
    /**
     * @var CommandRegisterLicenceKey
     */
    private $registerLicenceKey;

    public function __construct(
        CommandRegisterLicenceKey $registerLicenceKey,
        string $name = null
    ) {
        parent::__construct($name);
        $this->registerLicenceKey = $registerLicenceKey;
    }

    protected function configure()
    {
        $this->setName('amasty-base:licence:register-key');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerLicenceKey->execute();
    }
}
