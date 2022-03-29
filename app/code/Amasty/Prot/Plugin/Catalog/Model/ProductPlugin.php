<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Model;

use Magento\Bundle\Model\Product\Price;
use Magento\Bundle\Model\Product\Type;

class ProductPlugin
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function afterBeforeSave(\Magento\Catalog\Model\Product $product)
    {
        $hasOptions = $product->getHasOptions();
        $hasRequiredOptions = $product->getRequiredOptions();
        if ($product->getTypeId() === Type::TYPE_CODE && $product->getPriceType() == Price::PRICE_TYPE_DYNAMIC) {
            /** unset product custom options for dynamic price */
            if ($product->hasData('templates')) {
                $product->unsetData('templates');
            }
        }

        $templates = $product->getTemplates();
        if (is_array($templates)) {
            foreach ($templates as $template) {
                $options = $template['options'] ?? false;
                if (is_array($options)) {
                    foreach ($options as $option) {
                        if ($option instanceof \Magento\Catalog\Api\Data\ProductCustomOptionInterface) {
                            $option = $option->getData();
                        }
                        if (!isset($option['is_delete']) || $option['is_delete'] != '1') {
                            $hasOptions = true;
                        }
                        if ($option['is_require'] == '1') {
                            $hasRequiredOptions = true;
                            break;
                        }
                    }
                }
            }

            $product->setHasOptions($hasOptions);
            $product->setRequiredOptions($hasRequiredOptions);
        }
    }
}
