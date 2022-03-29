<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Model;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Framework\Model\AbstractModel;

class TemplateOptionValue extends AbstractModel implements TemplateOptionValueInterface
{
    protected function _construct()
    {
        $this->_init(\Amasty\Prot\Model\ResourceModel\TemplateOptionValue::class);
        $this->setIdFieldName(TemplateOptionValueInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function getTemplateOptionId()
    {
        return $this->_getData(TemplateOptionValueInterface::TEMPLATE_OPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTemplateOptionId($templateOptionId)
    {
        $this->setData(TemplateOptionValueInterface::TEMPLATE_OPTION_ID, $templateOptionId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOptionValueId()
    {
        return $this->_getData(TemplateOptionValueInterface::RELATION_VALUE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOptionValueId($optionValueId)
    {
        $this->setData(TemplateOptionValueInterface::RELATION_VALUE_ID, $optionValueId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSwatchValue()
    {
        return $this->_getData(TemplateOptionValueInterface::SWATCH_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setSwatchValue($swatchValue)
    {
        $this->setData(TemplateOptionValueInterface::SWATCH_VALUE, $swatchValue);

        return $this;
    }
}
