<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Block\Product\View\Options\AbstractOptions;

class Multiple extends AbstractOptions
{
    const SWATCH_TEMPLATE = 'Amasty_Prot::product/view/options/view/swatches.phtml';

    /**
     * @var string
     */
    protected $_template = 'Amasty_Prot::product/view/options/view/dropdown.phtml';

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    private $mediaHelper;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    public function __construct(
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->mediaHelper = $mediaHelper;
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param ProductCustomOptionValuesInterface $value
     *
     * @return string
     */
    public function getPriceContent(ProductCustomOptionValuesInterface $value)
    {
        $isPercentPriceType = $value->getPriceType() === 'percent';
        $priceStr = $this->_formatPrice(
            [
                'is_percent' => $isPercentPriceType,
                'pricing_value' => $value->getPrice($isPercentPriceType)
            ],
            false
        );

        return $this->convertCorrectPrice($priceStr);
    }

    /**
     * @param string $priceStr
     *
     * @return string
     */
    protected function convertCorrectPrice(string $priceStr)
    {
        $priceStr = str_replace(["\n", "\r"], '', $priceStr);
        $priceValue = strip_tags($priceStr);
        $parts = explode(' ', $priceValue);
        $parts = array_filter($parts);
        $parts = array_values($parts);

        //TODO refactor;
        //for both prices show the second label
        if (isset($parts[2])) {
            $labels = [];
            preg_match_all('/data-label="([\s\S]*?)"/', $priceStr, $labels);
            $parts[2] = $labels[1][1] ? '(' . $labels[1][1] . ': ' . $parts[2] . ')' : $parts[2];
            $priceValue = implode(' ', $parts);
        }

        return $priceValue;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ((int)$this->getOption()->getData(TemplateOptionInterface::USE_SWATCHES)) {
            $this->setTemplate(self::SWATCH_TEMPLATE);
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
    }

    /**
     * @param ProductCustomOptionValuesInterface $value
     * @return string
     */
    public function getBackground(ProductCustomOptionValuesInterface $value)
    {
        if ($swatchValue = $value->getData(TemplateOptionValueInterface::SWATCH_VALUE)) {
            if (strpos($swatchValue, '#') !== 0) {
                $result = sprintf('background-image: url(%s)', $this->mediaHelper->getSwatchAttributeImage(
                    'swatch_thumb',
                    $swatchValue
                ));
            } else {
                $result = sprintf('background-color: %s', $swatchValue);
            }
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * @param ProductCustomOptionValuesInterface $value
     * @return bool
     */
    public function hasBackground(ProductCustomOptionValuesInterface $value)
    {
        return (bool) $value->getData(TemplateOptionValueInterface::SWATCH_VALUE);
    }

    /**
     * @param ProductCustomOptionValuesInterface $value
     * @return string
     */
    public function getText(ProductCustomOptionValuesInterface $value)
    {
        return $value->getTitle();
    }

    /**
     * @param string $listType
     * @return string
     */
    public function getSwatchClassName(string $listType)
    {
        $swatchOptionType = '';
        switch ($listType) {
            case 0:
                $swatchOptionType = '-swatch-vertical';
                break;
            case 2:
                $swatchOptionType = '-swatch-thumbnail';
                break;
        }

        return $swatchOptionType;
    }
}
