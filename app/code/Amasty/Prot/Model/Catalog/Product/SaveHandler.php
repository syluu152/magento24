<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Catalog\Product;

use Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Amasty\Prot\Ui\DataProvider\Product\Form\Modifier\OptionTemplates;
use Amasty\Prot\Model\Repository\Template as TemplateRepository;
use Amasty\Prot\Model\Repository\TemplateOption;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory as CustomOptionFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory as ValueCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class SaveHandler implements ExtensionInterface
{
    const TEMPLATE_SAVED_FLAG = 'amprot_template_saved_flag';

    /**
     * @var array
     */
    private $excludedModules = [
        'catalogstaging',
        'amasty_pgrid'
    ];

    /**
     * @var OptionRepository
     */
    protected $optionRepository;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CustomOptionFactory
     */
    private $customOptionFactory;

    /**
     * @var TemplateOption
     */
    private $templateOptionRepository;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var ValueCollectionFactory
     */
    private $valueCollectionFactory;

    public function __construct(
        OptionRepository $optionRepository,
        TemplateRepository $templateRepository,
        RequestInterface $request,
        CustomOptionFactory $customOptionFactory,
        TemplateOption $templateOptionRepository,
        MetadataPool $metadataPool,
        JsonSerializer $jsonSerializer,
        ValueCollectionFactory $valueCollectionFactory,
        array $excludedModules = []
    ) {
        $this->optionRepository = $optionRepository;
        $this->templateRepository = $templateRepository;
        $this->request = $request;
        $this->customOptionFactory = $customOptionFactory;
        $this->templateOptionRepository = $templateOptionRepository;
        $this->metadataPool = $metadataPool;
        $this->jsonSerializer = $jsonSerializer;
        $this->valueCollectionFactory = $valueCollectionFactory;
        $this->excludedModules = array_merge($this->excludedModules, $excludedModules);
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return \Magento\Catalog\Api\Data\ProductInterface|object
     */
    public function execute($entity, $arguments = [])
    {
        if (!$this->request->getModuleName() || in_array($this->request->getModuleName(), $this->excludedModules)) {
            return $entity;
        }

        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $entityId = $entity->getData($productMetadata->getLinkField());
        $currentOptions = [];
        $templatesToUpdate = [];

        $existedOptions = $this->getExistedOptions($entity, $arguments);
        $templates = $entity->getTemplates();

        if ($templates) {
            foreach ($templates as &$template) {
                $name = $template['name'] ?? '';
                $id = $template['template_id'] ?? 0;
                $productOptions = &$template['options'] ?? [];
                if (!$name || empty($productOptions)) {
                    continue;
                }

                $currentOptions = $this->mergeOptions($currentOptions, $productOptions);

                $isChanged = $template['changed'] == 1 || isset($template['new']);
                if (!$isChanged || $this->templateRepository->isTemplateScheduled($id, $entityId)) {
                    continue;
                }

                $templateModel = $this->getTemplateModel($id);
                $templateModel->setName($name);
                $this->templateRepository->save($templateModel);
                $template['template_id'] = $templateModel->getTemplateId();
                $this->saveOptions($entity, $templateModel, $productOptions);
                //TODO check if options was changes
                if ($id) {
                    $templatesToUpdate[] = $id;
                }
            }
            $entity->setTemplates($templates);
        }

        $currentOptionsIds = $this->convertOptionsToIds($currentOptions);
        $templateIdsToCheck = [];
        foreach ($existedOptions as $existedOption) {
            if (!in_array($existedOption->getOptionId(), $currentOptionsIds)) {
                $templateIdsToCheck[] = $this->deleteOption($existedOption);
            }
        }
        $this->checkTemplates($templateIdsToCheck);

        foreach ($templatesToUpdate as $templatesToUpdateId) {
            $entity->setData(self::TEMPLATE_SAVED_FLAG, true);
            $emptyArray = [];
            $this->templateRepository->assignTemplateToIds(
                $templatesToUpdateId,
                $emptyArray,
                $entityId
            );
        }

        return $entity;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return array|\Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    private function getExistedOptions(&$entity, $arguments = [])
    {
        $existedOptions = [];

        if (isset($arguments['action']) && $arguments['action'] == 'edit') {
            $templates = $entity->getTemplates();
            $template = reset($templates);
            $templateId = $template['template_id'] ?? null;
            if ($templateId) {
                $existedOptions = $this->templateOptionRepository->getProductOptions(
                    $templateId,
                    0,
                    $entity->getId()
                );
                $convertedData = $this->convert($existedOptions);
                foreach ($template['options'] as &$option) {
                    $parentOption = $option['parent_option_id'] ?? null;
                    $option['option_id'] = $convertedData[$parentOption] ?? null;
                }
                $entity->setData('templates', [$template]);
                $entity->setStoreId(0);
            }
        } else {
            $existedOptions = $this->templateOptionRepository->getProductOptions(
                0,
                0,
                $entity->getId()
            );
        }

        return $existedOptions;
    }

    /**
     * @param array $options
     * @return array
     */
    private function convert($options = [])
    {
        $data = [];
        foreach ($options as $index => $option) {
            $data[$option->getData('parent_option_id')] = $index;
        }

        return $data;
    }

    /**
     * @param array $currentOptions
     * @param array $productOptions
     *
     * @return array
     */
    protected function mergeOptions(array $currentOptions, array $productOptions)
    {
        return array_merge($currentOptions, $productOptions);
    }

    /**
     * @param $templateId
     *
     * @return TemplateInterface
     */
    protected function getTemplateModel($templateId)
    {
        if ($templateId) {
            $templateModel = $this->templateRepository->get($templateId);
        } else {
            $templateModel = $this->templateRepository->createNew();
        }

        return $templateModel;
    }

    /**
     * @param $product
     * @param TemplateInterface $templateModel
     * @param $options
     */
    protected function saveOptions($product, TemplateInterface $templateModel, &$options)
    {
        $customOptions = $this->fillProductOptions($product, $options);

        if ($customOptions) {
            $templateOptions = [];
            foreach ($customOptions as $key => $option) {
                $optionData = $option->getData();
                $optionData[TemplateOptionInterface::TEMPLATE_ID] = $templateModel->getTemplateId();
                $option = $this->optionRepository->save($option);
                $optionId = $option->getOptionId();

                $templateOption = $this->getTemplateOptionModel($option->getTemplateOptionId(), $option->getId());

                $templateOption->addData($this->updateOptionData($optionId, $optionData));

                $templateOptions[$key] = $templateOption;
            }

            list($customOptionsMap, $customOptionValuesMap) = $this->retrieveSortOrderMaps($templateOptions);
            foreach ($templateOptions as $key => $templateOption) {
                $this->updateDependency($templateOption, $customOptionsMap, $customOptionValuesMap);
                $this->templateOptionRepository->save($templateOption);
                $options[$key]['template_option_id'] = $templateOption->getId();
            }
        }
    }

    /**
     * @param int $optionId
     * @param array $optionData
     * @return array
     */
    private function updateOptionData($optionId, $optionData)
    {
        $optionData[\Amasty\Prot\Model\ResourceModel\TemplateOption::PRODUCT_OPTION_FIELD] = $optionId;
        $optionData[TemplateOptionInterface::DEPENDENCY] = $optionData[TemplateOptionInterface::DEPENDENCY]
            ?? [];
        unset($optionData['option_id']);

        return $optionData;
    }

    /**
     * @param array $templateOptions
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function retrieveSortOrderMaps(array $templateOptions)
    {
        $customOptionsMap = [];
        $customOptionValuesMap = [];
        /** @var TemplateOptionInterface $templateOption */
        foreach ($templateOptions as $templateOption) {
            $dependency = $templateOption->getDependency();
            $templateOption->setDependency('');
            $this->templateOptionRepository->save($templateOption);
            $templateOption->setDependency($dependency);
            $templateOptionId = $templateOption->getId();

            /** @var ProductCustomOptionValuesInterface $optionValue */
            foreach ($templateOption->getTemplateOptionValues() as $templateOptionValue) {
                $customOptionValuesMap[$templateOptionId][$templateOptionValue->getSortOrder()]
                    = $templateOptionValue->getId();
            }
            $customOptionsMap[$templateOption->getSortOrder()] = $templateOption->getOptionId();
        }

        return [$customOptionsMap, $customOptionValuesMap];
    }

    /**
     * @param TemplateOptionInterface $templateOption
     * @param array $customOptionsMap
     * @param array $customOptionValuesMap
     */
    private function updateDependency(
        TemplateOptionInterface $templateOption,
        array $customOptionsMap,
        array $customOptionValuesMap
    ) {
        $dependencyArray = $templateOption->getDependency() ?: [];
        if (!is_array($dependencyArray)) {
            return;
        }

        foreach ($dependencyArray as $key => &$dependency) {
            if (isset($dependency[OptionTemplates::FIELD_DEPENDENCY_OPTION])
                && isset($dependency[OptionTemplates::FIELD_DEPENDENCY_VALUE])
            ) {
                unset($dependency['record_id']);

                $dependency[OptionTemplates::FIELD_DEPENDENCY_OPTION] = $this->resolveIdFromString(
                    $dependency[OptionTemplates::FIELD_DEPENDENCY_OPTION],
                    $customOptionsMap
                );

                $optValue = $dependency[OptionTemplates::FIELD_DEPENDENCY_VALUE];
                $dependency[OptionTemplates::FIELD_DEPENDENCY_VALUE] = [];
                $optValue = is_array($optValue) ? $optValue : [$optValue];
                foreach ($optValue as $value) {
                    $dependency[OptionTemplates::FIELD_DEPENDENCY_VALUE][] = $this->resolveIdFromString(
                        $value,
                        $customOptionValuesMap[$dependency[OptionTemplates::FIELD_DEPENDENCY_OPTION]] ?? []
                    );
                }

            } else {
                unset($dependencyArray[$key]);
            }
        }

        $templateOption->setDependency($this->jsonSerializer->serialize($dependencyArray));
    }

    /**
     * @param string $string
     * @param array $sortOrderMap
     * @return int
     */
    protected function resolveIdFromString(string $string, array $sortOrderMap)
    {
        if (strpos($string, 'id_') !== false) {
            $id = str_replace('id_', '', $string);
        } else {
            $sortOrder = str_replace('order_', '', $string);
            $id = $sortOrderMap[$sortOrder] ?? 0;
        }

        return (int) $id;
    }

    /**
     * @param $amastyOptionId
     * @param $optionId
     *
     * @return TemplateOptionInterface|\Amasty\Prot\Model\TemplateOption|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTemplateOptionModel($amastyOptionId, $optionId)
    {
        if ($amastyOptionId) {
            $templateOption = $this->templateOptionRepository->getById($amastyOptionId);
        } else {
            $templateOption = $this->templateOptionRepository->createNew();
        }
        $optionValues = $this->valueCollectionFactory->create()->addFieldToFilter(
            'option_id',
            $optionId
        );
        $templateOption->setOptionValues($optionValues);

        return $templateOption;
    }

    /**
     * Fills $product with options from $productOptions array
     * copy from app/code/Magento/Catalog/Controller/Adminhtml/Product/Initialization/Helper.php
     *
     * @param Product $product
     * @param array $productOptions
     * @return array
     */
    protected function fillProductOptions(Product $product, array $productOptions)
    {
        if ($product->getOptionsReadonly() || empty($productOptions)) {
            return [];
        }

        // mark custom options that should to fall back to default value
        $productOptions = $this->mergeProductOptions(
            $productOptions,
            $this->request->getPost('options_use_default')
        );
        $customOptions = [];
        foreach ($productOptions as $customOptionData) {
            if (!empty($customOptionData['is_delete'])) {
                continue;
            }

            if (empty($customOptionData['option_id'])) {
                $customOptionData['option_id'] = null;
            }

            if (isset($customOptionData['values'])) {
                $customOptionData['values'] = array_filter($customOptionData['values'], function ($valueData) {
                    return empty($valueData['is_delete']);
                });
            }

            $customOption = $this->customOptionFactory->create(['data' => $customOptionData]);
            $customOption->setProductSku($product->getSku());
            $customOption->setProduct($product);
            $customOptions[] = $customOption;
        }

        return $customOptions;
    }

    /**
     * @param $option
     *
     * @return int
     */
    protected function deleteOption($option)
    {
        $templateId = $option->getTemplateId();
        $id = $option->getOptionid();
        $parentId = $option->getParentOptionId();

        $this->optionRepository->delete($option);
        $this->templateOptionRepository->deleteRelation((int)$id, (int)$parentId);

        return (int)$templateId;
    }

    /**
     * @param array $templateIdsToCheck
     */
    protected function checkTemplates(array $templateIdsToCheck)
    {
        if ($templateIdsToCheck) {
            $templateIdsToCheck = array_unique($templateIdsToCheck);
            foreach ($templateIdsToCheck as $templateId) {
                $this->templateRepository->checkAndRemoveTemplate($templateId);
            }
        }
    }

    /**
     * @param array $currentOptions
     *
     * @return array
     */
    protected function convertOptionsToIds(array $currentOptions)
    {
        $ids = [];
        foreach ($currentOptions as $currentOption) {
            if (isset($currentOption['option_id'])) {
                $ids[] = $currentOption['option_id'];
            }
        }

        return $ids;
    }

    /**
     * Merge product and default options for product
     *
     * @param array $productOptions product options
     * @param array $overwriteOptions default value options
     * @return array
     */
    protected function mergeProductOptions($productOptions, $overwriteOptions)
    {
        if (!is_array($productOptions)) {
            return [];
        }

        if (!is_array($overwriteOptions)) {
            return $productOptions;
        }

        foreach ($productOptions as $optionIndex => $option) {
            $optionId = $option['option_id'];
            $option = $this->overwriteValue($optionId, $option, $overwriteOptions);

            if (isset($option['values']) && isset($overwriteOptions[$optionId]['values'])) {
                foreach ($option['values'] as $valueIndex => $value) {
                    if (isset($value['option_type_id'])) {
                        $valueId = $value['option_type_id'];
                        $value = $this->overwriteValue($valueId, $value, $overwriteOptions[$optionId]['values']);
                        $option['values'][$valueIndex] = $value;
                    }
                }
            }

            $productOptions[$optionIndex] = $option;
        }

        return $productOptions;
    }

    /**
     * Overwrite values of fields to default, if there are option id and field name in array overwriteOptions
     *
     * @param int $optionId
     * @param array $option
     * @param array $overwriteOptions
     * @return array
     */
    protected function overwriteValue($optionId, $option, $overwriteOptions)
    {
        if (isset($overwriteOptions[$optionId])) {
            foreach ($overwriteOptions[$optionId] as $fieldName => $overwrite) {
                if ($overwrite && isset($option[$fieldName]) && isset($option['default_' . $fieldName])) {
                    $option[$fieldName] = $option['default_' . $fieldName];
                    if ('title' == $fieldName) {
                        $option['is_delete_store_title'] = 1;
                    }
                }
            }
        }

        return $option;
    }
}
