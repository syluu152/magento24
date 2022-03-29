<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Provider\Collector\LicenceService;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Platform as RequestPlatform;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Magento\Framework\App\ProductMetadataInterface;

class Platform implements CollectorInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    public function get(): array
    {
        return [
            RequestPlatform::NAME => 'Magento ' . $this->productMetadata->getEdition(),
            RequestPlatform::VERSION => $this->productMetadata->getVersion()
        ];
    }
}
