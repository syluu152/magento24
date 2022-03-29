<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Utils\Http\Response\Entity;

use Magento\Framework\DataObject;

class Config extends DataObject
{
    public const CLASS_NAME = 'class_name';
    public const TYPE = 'type';
    public const DATA_PROCESSOR = 'data_processor';

    public function getClassName(): string
    {
        return $this->getData(self::CLASS_NAME);
    }

    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    public function getDataProcessor(): ?DataProcessorInterface
    {
        return $this->getData(self::DATA_PROCESSOR);
    }
}
