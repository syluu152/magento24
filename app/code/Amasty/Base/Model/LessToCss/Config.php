<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Model\LessToCss;

use Magento\Framework\Config\CacheInterface;
use Amasty\Base\Model\LessToCss\Config\Reader;

/**
 * Extension attributes config
 */
class Config extends \Magento\Framework\Config\Data
{
    public const CACHE_ID = 'amasty_less_to_css';

    /**
     * Initialize reader and cache.
     *
     * @param Reader $reader
     * @param CacheInterface $cache
     */
    public function __construct(
        Reader $reader,
        CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, self::CACHE_ID);
    }
}
