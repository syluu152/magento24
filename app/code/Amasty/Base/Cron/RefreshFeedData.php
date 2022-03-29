<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Cron;

use Amasty\Base\Model\Feed\FeedTypes\Ads;
use Amasty\Base\Model\Feed\FeedTypes\Extensions;

class RefreshFeedData
{
    /**
     * @var Ads
     */
    private $adsFeed;

    /**
     * @var Extensions
     */
    private $extensionsFeed;

    public function __construct(
        Ads $adsFeed,
        Extensions $extensionsFeed
    ) {
        $this->adsFeed = $adsFeed;
        $this->extensionsFeed = $extensionsFeed;
    }

    /**
     * Force reload feeds data
     */
    public function execute()
    {
        $this->extensionsFeed->getFeed();
        $this->adsFeed->getFeed();
    }
}
