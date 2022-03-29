<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;

use Amasty\Base\Model\SimpleDataObject;

class Domain extends SimpleDataObject
{
    public const URL = 'url';

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getData(self::URL);
    }
}
