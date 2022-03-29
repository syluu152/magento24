<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Plugin\Catalog\Model\Product\Option;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Registry;

class ValuePlugin
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param Value $optionValue
     * @return Value
     */
    public function afterAfterSave(Value $optionValue)
    {
        if ($optionValue->hasData(TemplateOptionValueInterface::DATA_IDENTIFIER)) {
            $optionValuesRelations = $this->registry->registry(TemplateOptionValueInterface::DATA_IDENTIFIER) ?: [];
            $optionValuesRelations[$optionValue->getId()] = [
                TemplateOptionValueInterface::ID => $optionValue->getData(
                    TemplateOptionValueInterface::DATA_IDENTIFIER
                ),
                TemplateOptionValueInterface::SWATCH_VALUE => $optionValue->getData(
                    TemplateOptionValueInterface::SWATCH_VALUE
                )
            ];
            $this->registry->unregister(TemplateOptionValueInterface::DATA_IDENTIFIER);
            $this->registry->register(TemplateOptionValueInterface::DATA_IDENTIFIER, $optionValuesRelations);
        }

        return $optionValue;
    }
}
