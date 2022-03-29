<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Catalog\Block\Product\View\Type;

use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\CheckableFactory;
use Amasty\Prot\Block\Catalog\Block\Product\View\Type\Select\MultipleFactory;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Select extends \Magento\Catalog\Block\Product\View\Options\AbstractOptions
{
    /**
     * @var CheckableFactory
     */
    private $checkableFactory;

    /**
     * @var MultipleFactory
     */
    private $multipleFactory;

    public function __construct(
        Context $context,
        Data $pricingHelper,
        CatalogHelper $catalogData,
        CheckableFactory $checkableFactory,
        MultipleFactory $multipleFactory,
        array $data = []
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->checkableFactory = $checkableFactory;
        $this->multipleFactory = $multipleFactory;
    }

    /**
     * Return html for control element
     *
     * @return string
     */
    public function getChildValuesHtml()
    {
        $option = $this->getOption();
        $optionBlock = $this->getOptionBlock($option->getType());

        return $optionBlock ?
            $optionBlock->setOption($option)
            ->setProduct($this->getProduct())
            ->setSkipJsReloadPrice(1)
            ->_toHtml()
            : '';
    }

    /**
     * @param string $optionType
     *
     * @return Select\Checkable|Select\Multiple|null
     */
    protected function getOptionBlock(string $optionType)
    {
        $optionBlock = null;
        switch ($optionType) {
            case Option::OPTION_TYPE_DROP_DOWN:
            case Option::OPTION_TYPE_MULTIPLE:
                $optionBlock = $this->multipleFactory->create();
                break;
            case Option::OPTION_TYPE_RADIO:
            case Option::OPTION_TYPE_CHECKBOX:
                $optionBlock = $this->checkableFactory->create();
                break;
        }

        return $optionBlock;
    }
}
