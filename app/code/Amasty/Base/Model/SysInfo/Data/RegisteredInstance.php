<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Data;

use Amasty\Base\Model\SimpleDataObject;
use Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance;
use Magento\Framework\Api\ExtensibleDataInterface;

class RegisteredInstance extends SimpleDataObject implements ExtensibleDataInterface
{
    public const INSTANCES = 'instances';
    public const CURRENT_INSTANCE = 'current_instance';

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance|null
     */
    public function getCurrentInstance(): ?Instance
    {
        return $this->getData(self::CURRENT_INSTANCE);
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance|null $instance
     * @return $this
     */
    public function setCurrentInstance(?Instance $instance): self
    {
        return $this->setData(self::CURRENT_INSTANCE, $instance);
    }

    /**
     * @return \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance[]
     */
    public function getInstances(): array
    {
        return $this->getData(self::INSTANCES) ?? [];
    }

    /**
     * @param \Amasty\Base\Model\SysInfo\Data\RegisteredInstance\Instance[] $instances
     * @return $this
     */
    public function setInstances(array $instances): self
    {
        return $this->setData(self::INSTANCES, $instances);
    }
}
