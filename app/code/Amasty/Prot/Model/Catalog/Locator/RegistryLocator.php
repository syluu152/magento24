<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Catalog\Locator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

class RegistryLocator implements LocatorInterface
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var LocatorInterface
     */
    private $locator;

    public function __construct(LocatorFactory $locatorFactory)
    {
        $this->locator = $locatorFactory->create();
    }

    /**
     * @param ProductInterface $product
     * @return $this
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProduct()
    {
        if (null !== $this->product) {
            return $this->product;
        }

        $product = $this->locator->getProduct();
        $productOptions = $product->getOptions() ?: [];
        $options = [];

        /** @var \Magento\Catalog\Model\Product\Option $option */
        foreach ($productOptions as $index => $option) {
            if ($option->getData('template_id')) {
                continue;
            }

            $options[] = $option;
        }

        $product->setOptions($options);
        $this->product = $product;

        return $product;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->locator->getStore();
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->locator->getBaseCurrencyCode();
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->locator->getWebsiteIds();
    }
}
