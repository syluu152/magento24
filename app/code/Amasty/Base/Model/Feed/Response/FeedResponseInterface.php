<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\Feed\Response;

interface FeedResponseInterface
{
    public function getContent(): ?string;

    public function setContent(?string $content): FeedResponseInterface;

    public function getStatus(): ?string;

    public function setStatus(?string $status): FeedResponseInterface;

    public function isNeedToUpdateCache(): bool;

    public function isFailed(): bool;
}
