<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Catalog\Block\Product\View;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Amasty\Prot\Model\Serializer\Json as JsonSerializer;
use Amasty\Prot\Ui\DataProvider\Product\Form\Modifier\OptionTemplates;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Options extends \Magento\Catalog\Block\Product\View\Options implements IdentityInterface
{
    const BLOCK_PREFIX = 'amprot_';

    /**
     * @var null|array
     */
    private $dependencyConfig = null;

    /**
     * @var null|array
     */
    private $cache = null;

    /**
     * @param string $type
     * @return string
     */
    public function getGroupOfOption($type)
    {
        return self::BLOCK_PREFIX . parent::getGroupOfOption($type);
    }

    /**
     * @param ProductCustomOptionInterface $option
     *
     * @return bool
     */
    public function hasOptionStyles(ProductCustomOptionInterface $option)
    {
        return $option->getFontColor() || $option->getFontSize();
    }

    /**
     * @return array|null
     */
    public function getOptions()
    {
        if ($this->cache === null) {
            $this->cache = parent::getOptions();
        }

        return $this->cache;
    }

    /**
     * @param $option
     * @return int
     */
    public function getOptionId($option)
    {
        return $option->getId();
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getOptionTemplates() as $template) {
            // phpcs:ignore
            $identities = array_merge($identities, $template->getIdentities());
        }

        return $identities;
    }

    /**
     * see plugin /Amasty/Prot/Plugin/Catalog/Block/Product/View/OptionsPlugin
     * @return array
     */
    protected function getOptionTemplates()
    {
        return [];
    }

    /**
     * @return array|null
     */
    public function getDependencyConfig()
    {
        if ($this->dependencyConfig === null && $this->getJsonSerializer()) {
            $config = [];
            foreach ($this->getOptions() as $option) {
                $dependencies = $this->getJsonSerializer()->unserialize(
                    $option->getData(TemplateOptionInterface::DEPENDENCY) ?: '[]'
                );
                $config[$option->getId()] = [];
                foreach ($dependencies as $dependency) {
                    list($dependencyOption, $dependencyValues) = $this->convertDependency($dependency);
                    if ($dependencyOption && $dependencyValues) {
                        $config[$option->getId()][$dependencyOption->getId()] = [
                            'values' => $this->mergeDependencyValues(
                                $config,
                                $option->getId(),
                                $dependencyOption->getId(),
                                $dependencyValues
                            ),
                            'type' => $dependencyOption->getType()
                        ];
                    }
                }
            }
            $this->dependencyConfig = $config;
        }

        return $this->dependencyConfig;
    }

    /**
     * @return string
     */
    public function getSerializedDependencyConfig()
    {
        if ($this->getJsonSerializer()) {
            $config = $this->getJsonSerializer()->serialize($this->getDependencyConfig());
        } else {
            $config = '{}';
        }

        return $config;
    }

    /**
     * @return JsonSerializer|null
     */
    private function getJsonSerializer()
    {
        return $this->_data['jsonSerializer'] ?? null;
    }

    /**
     * Retrieve dependencyOption and dependencyValue of magento entities from amasty dependency
     *
     * @param array $dependency
     * @return array
     */
    private function convertDependency(array $dependency)
    {
        $dependValues = $dependency[OptionTemplates::FIELD_DEPENDENCY_VALUE];
        $dependValues = is_array($dependValues) ? $dependValues : [$dependValues];

        $dependencyOption = null;
        $dependencyValues = [];
        foreach ($this->getOptions() as $productCustomOption) {
            if ($productCustomOption->getData(TemplateOptionInterface::PARENT_OPTION_ID)
                == $dependency[OptionTemplates::FIELD_DEPENDENCY_OPTION]
            ) {
                $dependencyOption = $productCustomOption;
                foreach ($dependencyOption->getValues() as $value) {
                    if (in_array(
                        $value->getData(TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID),
                        $dependValues
                    )) {
                        $dependencyValues[] = $value->getId();
                    }
                }
                break;
            }
        }

        return [$dependencyOption, $dependencyValues];
    }

    /**
     * @param array $config
     * @param int $optionId
     * @param $dependencyOptionId
     * @param $dependencyValueIds
     * @return array
     */
    private function mergeDependencyValues($config, $optionId, $dependencyOptionId, $dependencyValueIds)
    {
        return array_merge(
            $config[$optionId][$dependencyOptionId]['values'] ?? [],
            $dependencyValueIds
        );
    }

    /**
     * @param $option
     * @return bool
     */
    public function isOptionHasDependency($option)
    {
        $this->getDependencyConfig();
        return !empty($this->dependencyConfig[$option->getId()]);
    }

    /**
     * @param $option
     * @return string
     */
    public function getDependencyClass($option)
    {
        return $this->isOptionHasDependency($option) ? ' amprot-dependent-field' : '';
    }
}
