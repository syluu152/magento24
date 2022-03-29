<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\LicenceService\Request\Url;

use Amasty\Base\Model\Config;

class Builder
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function build($path, $params = []): string
    {
        $apiUrl = $this->config->getLicenceServiceApiUrl();
        $requestParams = [$apiUrl, $path];
        if (!empty($params)) {
            $requestParams[] = '?';
            $requestParams[] = http_build_query($params);
        }

        return implode('', $requestParams);
    }
}
