<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\FlagRepository;

class CacheStorage
{
    public const PREFIX = 'amasty_base_';

    /**
     * @var FlagRepository
     */
    private $flagRepository;

    public function __construct(FlagRepository $flagRepository)
    {
        $this->flagRepository = $flagRepository;
    }

    public function get(string $identifier): ?string
    {
        return $this->flagRepository->get(self::PREFIX . $identifier);
    }

    public function set(string $identifier, string $value): bool
    {
        $this->flagRepository->save(self::PREFIX . $identifier, $value);

        return true;
    }
}
