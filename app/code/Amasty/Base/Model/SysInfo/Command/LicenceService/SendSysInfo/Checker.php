<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

class Checker
{
    public function isChangedCacheValue(?string $cacheValue, string $newValue): bool
    {
        return !($cacheValue && hash_equals($cacheValue, $newValue));
    }
}
