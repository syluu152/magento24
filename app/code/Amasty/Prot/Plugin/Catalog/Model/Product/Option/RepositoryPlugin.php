<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\Catalog\Model\Product\Option\Repository;

class RepositoryPlugin
{
    /**
     * @param Repository $subject
     * @param $options
     * @param Product $product
     * @param bool $requiredOnly
     *
     * @return array
     */
    public function afterGetProductOptions(Repository $subject, $options, Product $product, $requiredOnly = false)
    {
        if ($options && $product->getData(SaveHandlerPlugin::EXCLUDE_AMASTY_TEMPLATE_OPTIONS)) {
            $productOptions = [];

            /** @var \Magento\Catalog\Model\Product\Option $option */
            foreach ($options as $index => $option) {
                if ($option->getData('template_id')) {
                    continue;
                }

                $productOptions[] = $option;
            }

            $options = $productOptions;
        }

        return $options;
    }
}
