<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select;

use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Model\Product\Option;

class Checkable extends AbstractOptions
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Prot::product/view/options/view/checkable.phtml';

    /**
     * Return price in correct format
     * copy from magento code for compatibility with magento less 2.3.1
     *
     * @param ProductCustomOptionValuesInterface $value
     * @return string
     */
    public function formatPrice(ProductCustomOptionValuesInterface $value)
    {
        return parent::_formatPrice(
            [
                'is_percent' => $value->getPriceType() === 'percent',
                'pricing_value' => $value->getPrice($value->getPriceType() === 'percent')
            ]
        );
    }

    /**
     * Returns current currency for store
     * copy from magento code for compatibility with magento less 2.3.1
     *
     * @param ProductCustomOptionValuesInterface $value
     * @return float|string
     */
    public function getCurrencyByStore(ProductCustomOptionValuesInterface $value)
    {
        return $this->pricingHelper->currencyByStore(
            $value->getPrice(true),
            $this->getProduct()->getStore(),
            false
        );
    }

    /**
     * Returns preconfigured value for given option
     * copy from magento code for compatibility with magento less 2.3.1
     *
     * @param Option $option
     * @return string|array|null
     */
    public function getPreconfiguredValue(Option $option)
    {
        return $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());
    }
}
