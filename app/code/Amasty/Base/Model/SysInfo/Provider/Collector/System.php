<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Provider\Collector;

use Magento\Framework\App\ProductMetadataInterface;

class System implements CollectorInterface
{
    public const MAGENTO_VERSION_KEY = 'magento_version';
    public const MAGENTO_EDITION_KEY = 'magento_edition';
    public const PHP_VERSION_KEY = 'php_version';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    public function get(): array
    {
        return [
            self::MAGENTO_VERSION_KEY => $this->productMetadata->getVersion(),
            self::MAGENTO_EDITION_KEY => $this->productMetadata->getEdition(),
            self::PHP_VERSION_KEY => phpversion()
        ];
    }
}
