<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model;

use \Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Amasty\Prot\Api\TemplateOptionValueRepositoryInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Model\AbstractModel;

/**
 * Class TemplateOption
 *
 * Model for option created by template
 */
class TemplateOption extends AbstractModel implements TemplateOptionInterface, IdentityInterface
{
    const CACHE_TAG = 'am_template_option';
    const PERSIST_NAME = 'am_template_option';

    /**
     * @var TemplateOptionValueRepositoryInterface
     */
    private $templateOptionValueRepository;

    public function __construct(
        TemplateOptionValueRepositoryInterface $templateOptionValueRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->templateOptionValueRepository = $templateOptionValueRepository;
    }

    protected function _construct()
    {
        $this->_init(\Amasty\Prot\Model\ResourceModel\TemplateOption::class);
        $this->setIdFieldName(TemplateOptionInterface::OPTION_ID);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getOptionId()];
    }

    /**
     * @return int
     */
    public function getOptionId()
    {
        return $this->getData(TemplateOptionInterface::OPTION_ID);
    }

    /**
     * @param int $optionId
     *
     * @return TemplateOptionInterface
     */
    public function setOptionId($optionId)
    {
        $this->setData(TemplateOptionInterface::OPTION_ID, $optionId);

        return $this;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->getData(TemplateOptionInterface::TEMPLATE_ID);
    }

    /**
     * @param int $templateId
     *
     * @return TemplateOptionInterface
     */
    public function setTemplateId($templateId)
    {
        $this->setData(TemplateOptionInterface::TEMPLATE_ID, $templateId);

        return $this;
    }

    /**
     * @return string
     */
    public function getDependency()
    {
        return $this->getData(TemplateOptionInterface::DEPENDENCY);
    }

    /**
     * @param string $dependency
     *
     * @return TemplateOptionInterface
     */
    public function setDependency($dependency)
    {
        $this->setData(TemplateOptionInterface::DEPENDENCY, $dependency);

        return $this;
    }

    /**
     * @return string
     */
    public function getFontColor()
    {
        return $this->getData(TemplateOptionInterface::FONT_COLOR);
    }

    /**
     * @param string $color
     *
     * @return TemplateOptionInterface
     */
    public function setFontColor($color)
    {
        $this->setData(TemplateOptionInterface::FONT_COLOR, $color);

        return $this;
    }

    /**
     * @return int
     */
    public function getFontSize()
    {
        return $this->getData(TemplateOptionInterface::FONT_SIZE);
    }

    /**
     * @param int $size
     *
     * @return TemplateOptionInterface
     */
    public function setFontSize($size)
    {
        $this->setData(TemplateOptionInterface::FONT_SIZE, $size);

        return $this;
    }

    /**
     * @return int
     */
    public function getOptionListType()
    {
        return $this->getData(TemplateOptionInterface::OPTIONS_LIST_TYPE);
    }

    /**
     * @param int $type
     *
     * @return TemplateOptionInterface
     */
    public function setOptionListType($type)
    {
        $this->setData(TemplateOptionInterface::OPTIONS_LIST_TYPE, $type);

        return $this;
    }

    /**
     * @return int
     */
    public function getUseSwatches()
    {
        return $this->getData(TemplateOptionInterface::USE_SWATCHES);
    }

    /**
     * @param int $value
     *
     * @return TemplateOptionInterface
     */
    public function setUseSwatches($value)
    {
        $this->setData(TemplateOptionInterface::USE_SWATCHES, $value);

        return $this;
    }

    /**
     * @return AbstractModel
     */
    public function afterSave()
    {
        if (!$this->getValuesSaved()) {
            $templateOptionValues = [];
            $values = $this->getOptionValues() ?: [];
            $optionValuesRelations = $this->_registry->registry(TemplateOptionValueInterface::DATA_IDENTIFIER)
                ?: [];
            foreach ($values as $optionValue) {
                $optionData = $optionValuesRelations[$optionValue->getId()] ?? [];
                $templateOptionValue = $this->getTemplateOptionValue(
                    $optionData[TemplateOptionValueInterface::ID] ?? 0
                );

                $templateOptionValue->setSwatchValue(
                    $optionData[TemplateOptionValueInterface::SWATCH_VALUE] ?? null
                );
                $templateOptionValue->setTemplateOptionId($this->getOptionId());
                $templateOptionValue->setSortOrder($optionValue['sort_order']);
                $templateOptionValue->setOptionValueId($optionValue['option_type_id']);

                $this->templateOptionValueRepository->save($templateOptionValue);

                $templateOptionValues[] = $templateOptionValue;
            }
            $this->setTemplateOptionValues($templateOptionValues);
            $this->setValuesSaved(true);
        }

        return parent::afterSave();
    }

    /**
     * @param int|mixed $templateOptionValueId
     * @return TemplateOptionValueInterface
     */
    private function getTemplateOptionValue($templateOptionValueId)
    {
        if ($templateOptionValueId) {
            try {
                $templateOptionValue = $this->templateOptionValueRepository->getById($templateOptionValueId);
            } catch (NoSuchEntityException $e) {
                null;
            }
        }

        if (!isset($templateOptionValue)) {
            $templateOptionValue = $this->templateOptionValueRepository->createNew();
        }

        return $templateOptionValue;
    }
}
