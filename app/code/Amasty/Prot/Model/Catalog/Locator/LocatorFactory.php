<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Catalog\Locator;

class LocatorFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Amasty\Prot\Model\ResourceModel\Template\Collection
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->getInstanceName(), $data);
    }

    /**
     * @return string
     */
    protected function getInstanceName()
    {
        if (class_exists((\Magento\CatalogStaging\Model\Product\Locator\StagingLocator::class))) {
            $instanceName = \Magento\CatalogStaging\Model\Product\Locator\StagingLocator::class;
        } else {
            $instanceName = \Magento\Catalog\Model\Locator\RegistryLocator::class;
        }

        return $instanceName;
    }
}
