<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Data\RegisteredInstance;

use Amasty\Base\Model\SimpleDataObject;
use Magento\Framework\Api\ExtensibleDataInterface;

class Instance extends SimpleDataObject implements ExtensibleDataInterface
{
    public const DOMAIN = 'domain';
    public const SYSTEM_INSTANCE_KEY = 'system_instance_key';

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->getData(self::DOMAIN);
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain(string $domain): self
    {
        return $this->setData(self::DOMAIN, $domain);
    }

    /**
     * @return string
     */
    public function getSystemInstanceKey(): string
    {
        return $this->getData(self::SYSTEM_INSTANCE_KEY);
    }

    /**
     * @param string $systemInstanceKey
     * @return $this
     */
    public function setSystemInstanceKey(string $systemInstanceKey): self
    {
        return $this->setData(self::SYSTEM_INSTANCE_KEY, $systemInstanceKey);
    }
}
