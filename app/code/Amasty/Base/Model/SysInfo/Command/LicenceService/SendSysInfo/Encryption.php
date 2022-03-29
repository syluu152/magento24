<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Magento\Framework\Serialize\SerializerInterface;

class Encryption
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function encryptArray(array $value): string
    {
        $serializedValue = $this->serializer->serialize($value);

        return $this->encryptString($serializedValue);
    }

    public function encryptString(string $value): string
    {
        return hash('sha256', $value);
    }
}
