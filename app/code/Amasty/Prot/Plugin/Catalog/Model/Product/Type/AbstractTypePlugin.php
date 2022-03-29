<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\AbstractType;

class AbstractTypePlugin
{
    /**
     * @param AbstractType $subject
     * @param bool $result
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function afterHasOptions(AbstractType $subject, $result, $product)
    {
        $result = $result || count($product->getOptions());
        return $result;
    }

    /**
     * @param AbstractType $subject
     * @param bool $result
     * @param $product
     *
     * @return bool
     */
    public function afterHasRequiredOptions(AbstractType $subject, $result, $product)
    {
        if ($product->getOptions()) {
            $hasRequired = false;
            foreach ($product->getOptions() as $option) {
                if ($option instanceof \Magento\Catalog\Api\Data\ProductCustomOptionInterface) {
                    $option = $option->getData();
                }

                if (isset($option['is_require']) && $option['is_require'] == '1') {
                    $hasRequired = true;
                    break;
                }
            }

            $result = $result || $hasRequired;
        }

        return $result;
    }
}
