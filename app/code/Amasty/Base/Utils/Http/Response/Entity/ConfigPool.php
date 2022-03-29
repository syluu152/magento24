<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Utils\Http\Response\Entity;

use Amasty\Base\Utils\Http\Url\UrlComparator;
use Magento\Framework\Exception\NotFoundException;

class ConfigPool
{
    /**
     * @var Config[]
     */
    private $configs;

    /**
     * @var UrlComparator
     */
    private $urlComparator;

    public function __construct(
        UrlComparator $urlComparator,
        array $configs
    ) {
        $this->checkConfigInstance($configs);
        $this->urlComparator = $urlComparator;
        $this->configs = $configs;
    }

    /**
     * @param string $path
     * @return Config
     * @throws NotFoundException
     */
    public function get(string $path): Config
    {
        $result = false;
        foreach ($this->configs as $configPath => $config) {
            if ($this->urlComparator->isEqual($path, $configPath)) {
                $result = $config;
                break;
            }
        }

        if (!$result) {
            throw new NotFoundException(__('Entity config not found for given path %1.', $path));
        }

        return $result;
    }

    /**
     * @param array $configs
     * @throws \InvalidArgumentException
     * @return void
     */
    private function checkConfigInstance(array $configs): void
    {
        foreach ($configs as $configPath => $config) {
            if (!$config instanceof Config) {
                throw new \InvalidArgumentException(
                    'The config instance "' . $configPath . '" must be ' . Config::class
                );
            }
        }
    }
}
