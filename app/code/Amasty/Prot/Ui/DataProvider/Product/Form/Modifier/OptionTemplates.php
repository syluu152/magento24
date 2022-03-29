<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\DataProvider\Product\Form\Modifier;

use Amasty\Prot\Api\Data\ScheduleInterface;
use Amasty\Prot\Api\Data\TemplateInterface;
use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Amasty\Prot\Api\TemplateRepositoryInterface;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel;
use Magento\Downloadable\Model\Product\Type as TypeD;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\UrlInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\ColorPicker;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;

class OptionTemplates extends ModifiedCustomOptions
{
    /**#@+
     * Group values
     */
    const GROUP_CUSTOM_OPTIONS_TEMPLATES_NAME = 'amcustom_options';
    const GROUP_CUSTOM_OPTIONS_SCOPE = 'data.product';
    const GROUP_CUSTOM_OPTIONS_PREVIOUS_NAME = 'custom_options';
    const GROUP_CUSTOM_OPTIONS_DEFAULT_SORT_ORDER = 32;
    /**#@-*/

    /**#@+
     * Button values
     */
    const BUTTON_ADD_NEW = 'button_add_new';
    const BUTTON_ADD_EXISTING = 'button_add_existing';
    /**#@-*/

    /**#@+
     * Import options values
     */
    const EXISTING_TEMPLATES_MODAL = 'existing_templates_modal';
    const EXISTING_TEMPLATES_LISTING = 'amprot_existing_templates_listing';
    const CUSTOM_OPTIONS_LISTING = 'product_template_custom_options_listing';
    /**#@-*/

    const FIELD_ENABLE = 'affect_product_option_templates';

    /**#@+
     * Container values
     */
    const CONTAINER_TEMPLATES_HEADER_NAME = 'container_template_header';
    const CONTAINER_TEMPLATE = 'container_template';
    const CONTAINER_TEMPLATE_NAME = 'container_template_name';
    const GRID_TEMPLATES_NAME = 'templates';
    const CONTAINER_ADDITIONAL_NAME = 'container_additional';
    const GRID_DEPENDENCY_CONFIG = 'dependency';

    const FIELD_TEMPLATE_ID = 'template_id';
    const FIELD_TEMPLATE_TITLE_NAME = 'name';
    const FIELD_TEMPLATE_OPTION_ID = 'template_option_id';
    const FIELD_CHANGED_NAME = 'changed';
    const FIELD_DEPENDENCY_OPTION = 'dependency_option';
    const FIELD_DEPENDENCY_VALUE = 'dependency_value';
    const FIELD_FONT_SIZE = 'font_size';
    const FIELD_FONT_COLOR = 'font_color';
    const FIELD_LIST_TYPE = 'option_list_type';
    const FIELD_USE_SWATCHES = 'use_swatches';

    const SWATCH_VALUE = 'swatch_value';

    const CHANGED_TEMPLATE = 'product_form';

    /**
     * @var TemplateRepositoryInterface
     */
    protected $templateRepository;

    /**
     * @var \Amasty\Prot\Model\Repository\TemplateOption
     */
    protected $templateOptionRepository;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var bool
     */
    private $visible;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    private $mediaHelper;

    /**
     * @var \Amasty\Base\Model\MagentoVersion
     */
    private $magentoVersion;

    public function __construct(
        \Amasty\Prot\Model\Catalog\Locator\RegistryLocator $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        TemplateRepositoryInterface $templateRepository,
        \Amasty\Prot\Model\Repository\TemplateOption $templateOptionRepository,
        MetadataPool $metadataPool,
        JsonSerializer $jsonSerializer,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Amasty\Base\Model\MagentoVersion $magentoVersion,
        $visible = true
    ) {
        parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager
        );
        $this->templateRepository = $templateRepository;
        $this->templateOptionRepository = $templateOptionRepository;
        $this->metadataPool = $metadataPool;
        $this->visible = $visible;
        $this->jsonSerializer = $jsonSerializer;
        $this->mediaHelper = $mediaHelper;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        if ($this->isFieldSetAvailable()) {
            $this->createCustomOptionsPanel();
        }

        return $this->meta;
    }

    /**
     * @return $this
     */
    protected function createCustomOptionsPanel()
    {
        $fieldSet = [
            static::GROUP_CUSTOM_OPTIONS_TEMPLATES_NAME => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Customizable Option Templates by Amasty'),
                            'componentType' => Fieldset::NAME,
                            'dataScope' => static::GROUP_CUSTOM_OPTIONS_SCOPE,
                            'collapsible' => true,
                            'visible' => $this->visible,
                            'sortOrder' => $this->getNextGroupSortOrder(
                                $this->meta,
                                static::GROUP_CUSTOM_OPTIONS_PREVIOUS_NAME,
                                static::GROUP_CUSTOM_OPTIONS_DEFAULT_SORT_ORDER
                            ),
                        ],
                    ],
                ],
                'children' => [
                    static::CONTAINER_TEMPLATES_HEADER_NAME => $this->getHeaderTemplateContainerConfig(10),
                    static::FIELD_ENABLE => $this->getEnableFieldConfig(20),
                    static::GRID_TEMPLATES_NAME => $this->getTemplatesGridConfig(30)
                ]
            ]
        ];

        $isConfigurable = $this->locator->getProduct()->getTypeId() === ConfigurableProductType::TYPE_CODE;
        if ($isConfigurable) {
            $fieldSet[static::GROUP_CUSTOM_OPTIONS_TEMPLATES_NAME]['children']['configurable_warning'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Container::NAME,
                            'component' => 'Magento_ConfigurableProduct/js/components/custom-options-warning',
                            'additionalClasses' => 'message message-warning',
                            'sortOrder' => 15,
                            'isConfigurable' => $isConfigurable,
                            'content' => __(
                                'Please keep in mind that custom option templates containing Percent price option'
                                . ' values can be assigned to Configurable products, however, due to Magento\'s '
                                . 'internal limitations, such Percent price option values will become Free upon saving.'
                            ),
                            'imports' => [
                                'updateVisibility' => 'ns = ${ $.ns }, index = '
                                    . ConfigurablePanel::CONFIGURABLE_MATRIX . ':isEmpty'
                            ]
                        ]
                    ]
                ]
            ];
        }

        $this->meta = array_replace_recursive($this->meta, $fieldSet);
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::EXISTING_TEMPLATES_MODAL => $this->getExistingTemplatesModalConfig()
            ]
        );

        return $this;
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getHeaderTemplateContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __(
                            'After saving this product with Amasty Product Option Template(s), please assign it'
                            . ' to additional products through Product Grid\'s mass actions menu as necessary.<br/> '
                            . '\'Add Existing Template\' allows to add a particular existing template into particular'
                            . ' product\'s configuration. <br/>Once saved, the template can be unassigned from product'
                            . ' by using trash can icon.<br/> The template can also be deleted from templates grid at'
                            . ' <a href="%1">Templates list</a>',
                            $this->urlBuilder->getUrl('amprot/templates/index')
                        ),
                    ],
                ],
            ],
        ];
    }

    protected function getExistingTemplatesModalConfig()
    {
        $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.product_form_data_source',
                        'options' => [
                            'title' => __('Select Template'),
                            'buttons' => [
                                [
                                    'text' => __('Add'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . static::EXISTING_TEMPLATES_LISTING,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                static::EXISTING_TEMPLATES_LISTING => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => static::EXISTING_TEMPLATES_LISTING,
                                'externalProvider' => static::EXISTING_TEMPLATES_LISTING . '.'
                                    . static::EXISTING_TEMPLATES_LISTING . '_data_source',
                                'selectionsProvider' => static::EXISTING_TEMPLATES_LISTING . '.'
                                    . static::EXISTING_TEMPLATES_LISTING . '.template_columns.ids',
                                'ns' => static::EXISTING_TEMPLATES_LISTING,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'externalFilterMode' => false,
                                'currentProductId' => $this->locator->getProduct()
                                    ->getData($productMetadata->getLinkField()),
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'exports' => [
                                    'currentProductId' => '${ $.externalProvider }:params.current_product_id',
                                    '__disableTmpl' =>  false
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for the whole templates grid
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getTemplatesGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Template'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Amasty_Prot/js/components/dynamic-rows-import-templates',
                        'template' => 'ui/dynamic-rows/templates/collapsible',
                        'additionalClasses' => 'admin__field-wide amprot-template-block',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'addButton' => false,
                        'renderDefaultRecord' => true,
                        'columnsHeader' => false,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                        'dataProvider' => static::EXISTING_TEMPLATES_LISTING,
                        'imports' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' =>  false
                        ],
                        'dndConfig' => [
                            'enabled' => false
                        ]
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'headerLabel' => __('New Template'),
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::CONTAINER_TEMPLATE . '.' . static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        'amprot-fieldset-with-message' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'collapsible' => true,
                                        'label' => null,
                                        'sortOrder' => 10,
                                        'opened' => true,
                                        'imports' => [
                                            'additionalClasses' => '${ $.provider }:${ $.dataScope }.class',
                                            '__disableTmpl' =>  false
                                        ]
                                    ],
                                ],
                            ],
                            'children' => [
                                'disabled-message' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Container::NAME,
                                                'component' => 'Magento_Ui/js/form/components/html',
                                                'additionalClasses' => 'message message-warning',
                                                'visible' => false,
                                                'sortOrder' => 5,
                                                'content' => __(
                                                    'Current Template has been scheduled for update
                                                    and will be available for editing after the update is finished.'
                                                ),
                                                'imports' => [
                                                    'visible' => '${ $.provider }:${ $.dataScope }.disabled',
                                                    '__disableTmpl' =>  false
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                static::CONTAINER_TEMPLATE => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Fieldset::NAME,
                                                'collapsible' => true,
                                                'label' => null,
                                                'sortOrder' => 10,
                                                'opened' => true,
                                                'imports' => [
                                                    'disabled' => '${ $.provider }:${ $.dataScope }.disabled',
                                                    '__disableTmpl' =>  false
                                                ]
                                            ],
                                        ],
                                    ],
                                    'children' => [
                                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(40),
                                        static::CONTAINER_TEMPLATE_NAME => $this->getNameContainerConfig(5),
                                        static::CONTAINER_TEMPLATES_HEADER_NAME => $this->getHeaderContainerConfig(10),
                                        static::FIELD_ENABLE => $this->getEnableFieldConfig(20),
                                        static::GRID_OPTIONS_NAME => $this->getOptionsGridConfig(30),
                                        static::FIELD_CHANGED_NAME => $this->getChangedFieldConfig(50)
                                    ]
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for container with template name
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getNameContainerConfig($sortOrder)
    {
        $nameContainer = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_TEMPLATE_ID => $this->getIdFieldConfig(static::FIELD_TEMPLATE_ID, 10),
                static::FIELD_TEMPLATE_TITLE_NAME => $this->getTitleFieldConfig(
                    20,
                    [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Template Name'),
                                    'component' => 'Amasty_Prot/js/components/static-type-input',
                                    'valueUpdate' => 'input',
                                    'dataScope' => static::FIELD_TEMPLATE_TITLE_NAME,
                                    'validation' => [
                                        'max_text_length' => 50
                                    ],
                                    'imports' => [
                                        'templateId' => '${ $.provider }:${ $.parentScope }.template_id',
                                        'isUseDefault' => '${ $.provider }:${ $.parentScope }.is_use_default',
                                        '__disableTmpl' =>  false
                                    ]
                                ],
                            ],
                        ],
                    ]
                )
            ]
        ];

        if ($this->locator->getProduct()->getStoreId()) {
            $useDefaultConfig = [
                'service' => [
                    'template' => 'Magento_Catalog/form/element/helper/custom-option-service',
                ]
            ];
            $titlePath = $this->arrayManager->findPath(static::FIELD_TITLE_NAME, $nameContainer, null)
                . static::META_CONFIG_PATH;
            $nameContainer = $this->arrayManager->merge($titlePath, $nameContainer, $useDefaultConfig);
        }

        return $nameContainer;
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsGridConfig($sortOrder)
    {
        $optionsGrid = parent::getOptionsGridConfig($sortOrder);
        $optionsGrid = array_merge_recursive(
            $optionsGrid,
            [
                'children' => [
                    'record' => [
                        'children' => [
                            static::CONTAINER_OPTION => [
                                'children' => [
                                    static::CONTAINER_ADDITIONAL_NAME => $this->getAdditionalContainerConfig(25),
                                    static::FIELD_TEMPLATE_OPTION_ID => $this->getIdFieldConfig(
                                        static::FIELD_TEMPLATE_OPTION_ID,
                                        10
                                    ),
                                    static::GRID_DEPENDENCY_CONFIG => $this->getDependencyGridConfig(100)
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dndConfig' => [
                                'tableClass' => 'ignore'
                            ]
                        ]
                    ]
                ]
            ]
        );

        $optionsGrid['arguments']['data']['config']['component'] =
            'Amasty_Prot/js/components/dynamic-rows-import-custom-options';

        return $optionsGrid;
    }

    /**
     * Get config for hidden id field
     *
     * @param string $name
     * @param int $sortOrder
     * @return array
     */
    protected function getIdFieldConfig($name, $sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Hidden::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => $name,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getChangedFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::FIELD_CHANGED_NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                        'dataFormPartValBP' => $this::CHANGED_TEMPLATE,
                        'elementTmpl' => 'Amasty_Prot/form/components/changed/checkbox'
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getAdditionalContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal amprot-row-block',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_FONT_COLOR => $this->getFontColorField(10),
                static::FIELD_FONT_SIZE => $this->getFontSizeField(20),
                static::FIELD_USE_SWATCHES => $this->getUseSwatchesField(30),
                static::FIELD_LIST_TYPE => $this->getListTypeField(40)
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getFontSizeField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Font Size'),
                        'addafter' => __('px.'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_FONT_SIZE,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'validate-number' => true,
                            'validate-zero-or-greater' => true
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    public function getFontColorField($sortOrder)
    {
        $component = 'Magento_Ui/js/form/element/abstract';
        $formElement = Input::NAME;
        if (version_compare($this->magentoVersion->get(), '2.3.0', '>=')) {
            $component = 'Magento_Ui/js/form/element/color-picker';
            $formElement = ColorPicker::NAME;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Font Color'),
                        'component' => $component,
                        'componentType' => Field::NAME,
                        'formElement' => $formElement,
                        'dataScope' => static::FIELD_FONT_COLOR,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'colorFormat' => 'HEX'
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getUseSwatchesField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Use Swatches'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::FIELD_USE_SWATCHES,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getListTypeField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Option List Type'),
                        'formElement' => Select::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::FIELD_LIST_TYPE,
                        'options' => [
                            [
                                'label' => __('Vertical'),
                                'value' => 0
                            ],
                            [
                                'label' => __('Horizontal'),
                                'value' => 1
                            ],
                            [
                                'label' => __('Thumbnail'),
                                'value' => 2
                            ]
                        ],
                        'sortOrder' => $sortOrder,
                        'imports' => [
                            'visible' => '${ $.name.replace(/container_option(.*)/,'
                                . '"container_option.container_additional.use_swatches") }:checked',
                            '__disableTmpl' =>  false
                        ]
                    ]
                ],
            ],
        ];
    }

    protected function getSelectTypeGridConfig($sortOrder)
    {
        return array_merge_recursive(
            parent::getSelectTypeGridConfig($sortOrder),
            [
                'children' => [
                    'record' => [
                        'children' => [
                            TemplateOptionValueInterface::DATA_IDENTIFIER => $this->getIdFieldConfig(
                                TemplateOptionValueInterface::DATA_IDENTIFIER,
                                10
                            ),
                            static::SWATCH_VALUE => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Swatch'),
                                            'component' => 'Amasty_Prot/js/components/swatch-extend',
                                            'template' => 'Amasty_Prot/swatch-visual',
                                            'componentType' => Select::NAME,
                                            'formElement' => Select::NAME,
                                            'dataScope' => static::SWATCH_VALUE,
                                            'dataType' => Text::NAME,
                                            'sortOrder' => 15,
                                            'uploadUrl' => $this->urlBuilder->getUrl('swatches/iframe/show'),
                                            'prefixName' => 'swatchvisual.value',
                                            'prefixElementName' => 'option_',
                                            'swatchPath' => $this->mediaHelper->getSwatchMediaUrl(),
                                            'additionalClasses' => [
                                                'swatches-visual-col' => true,
                                                'amprot-swatches-visual' => true,
                                            ],
                                            'imports' => [
                                                'visible' => '${ $.name.replace(/container_option(.*)/,'
                                                    . '"container_option.container_additional.use_swatches") }:checked',
                                                '__disableTmpl' =>  false
                                            ]
                                        ],
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getIsRequireFieldConfig($sortOrder)
    {
        $config = parent::getIsRequireFieldConfig($sortOrder);

        return array_merge_recursive($config, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Amasty_Prot/js/form/components/dependency/single-checkbox',
                        'listens' => [
                            '${ $.provider }:${ $.parentScope }.dependency' => 'dependencyChanged'
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getDependencyGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Dependency'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Amasty_Prot/js/form/components/dependency/dynamic-rows',
                        'template' => 'Amasty_Prot/dynamic-rows/templates/default',
                        'additionalClasses' => 'admin__field-wide amprot-dependency-wrapper',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                        'listens' => [
                            '${ $.provider }:${ $.dataScope }.is_require' => 'requireOptionChanged'
                        ],
                        'dndConfig' => [
                            'enabled' => false
                        ],
                        'tooltip' => [
                            'description' => __('To make the current option dependent on any other (parent)' .
                                                ' option’s value(s) please uncheck the ‘Required’ checkbox.' .
                                                '</br>Note! There is AND logic between separate dependency ' .
                                                'lines and OR logic between several option\'s values within one line.')
                        ]
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_DEPENDENCY_OPTION => $this->getDependencyOption(10),
                        static::FIELD_DEPENDENCY_VALUE => $this->getDependencyValue(20),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(30)
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getDependencyOption($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Custom Option Title'),
                        'formElement' => Select::NAME,
                        'componentType' => Field::NAME,
                        'component' => 'Amasty_Prot/js/form/components/dependency/dependency-select',
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'dataScope' => static::FIELD_DEPENDENCY_OPTION,
                        'sortOrder' => $sortOrder,
                        '__disableTmpl' =>  false,
                        'exports' => [
                            'optionValues' => sprintf(
                                '${ $.parentName }.%s:options',
                                static::FIELD_DEPENDENCY_VALUE
                            ),
                            '__disableTmpl' =>  false
                        ],
                        'additionalClasses' => 'amprot-custom-field',
                        'dependencyValueName' => sprintf(
                            '${ $.parentName }.%s',
                            static::FIELD_DEPENDENCY_VALUE
                        )
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getDependencyValue($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Custom Option Value'),
                        'formElement' => MultiSelect::NAME,
                        'componentType' => Field::NAME,
                        'component' => 'Amasty_Prot/js/form/components/dependency/ui-select',
                        'multiple' => true,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'additionalClasses' => 'amprot-custom-field',
                        'dataScope' => static::FIELD_DEPENDENCY_VALUE,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function modifyData(array $data)
    {
        if ($this->isFieldSetAvailable()) {
            $templates = [];
            $product = $this->locator->getProduct();
            $productMetadata = $this->metadataPool->getMetadata(ProductInterface::class);
            $productTemplates = [];
            if ($product->getId()) {
                $productTemplates = $this->templateRepository->getAppliedTemplateByProduct(
                    $this->locator->getProduct()->getData($productMetadata->getLinkField())
                ) ? : [];
            }

            foreach ($productTemplates as $index => $template) {
                $templateOptions = $this->templateOptionRepository->getProductOptions(
                    $template->getTemplateId(),
                    $product->getStoreId(),
                    $product->getId(),
                    true
                );

                $templates[$index] = $this->convertTemplateData($template, array_values($templateOptions));
            }

            $data = $this->generateData($data, $templates);
        }

        return $data;
    }

    /**
     * @param array $data
     * @param array $templates
     * @return array
     */
    protected function generateData($data = [], $templates = [])
    {
        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                        static::FIELD_ENABLE        => 1,
                        static::GRID_TEMPLATES_NAME => $templates
                    ]
                ]
            ]
        );
    }

    /**
     * @param TemplateInterface $template
     * @param array $productOptions
     *
     * @return array
     */
    public function convertTemplateData(TemplateInterface $template, array $productOptions)
    {
        $templateData = $template->getData();
        [$templateDisabled, $options] = $this->generateOptionsByTemplate(
            $template->getTemplateId(),
            $productOptions
        );
        $templateData[static::GRID_OPTIONS_NAME] = $options;
        $templateData['disabled'] = $templateDisabled;
        if ($templateDisabled) {
            $templateData['class'] = 'amprot-disabled';
        }

        return $templateData;
    }

    /**
     * @param int $templateId
     * @param array $productOptions
     *
     * @return array
     */
    protected function generateOptionsByTemplate(int $templateId, array $productOptions)
    {
        $templateDisabled = false;
        $options = [];

        /** @var \Magento\Catalog\Model\Product\Option $option */
        foreach ($productOptions as $index => $option) {
            $optionData = $option->getData();
            $optionData[static::FIELD_IS_USE_DEFAULT] = !$option->getData(static::FIELD_STORE_TITLE_NAME);
            $optionData['disabled'] = (bool) $option->getData(ScheduleInterface::SCHEDULE_ID);
            $optionData = $this->convertDependency($optionData);

            $templateDisabled |= $optionData['disabled'];
            $options[$index] = $this->formatPriceByPath(static::FIELD_PRICE_NAME, $optionData);
            $values = $option->getValues() ?: [];

            foreach ($values as $value) {
                $value->setData(static::FIELD_IS_USE_DEFAULT, !$value->getData(static::FIELD_STORE_TITLE_NAME));
            }

            /** @var \Magento\Catalog\Model\Product\Option $value */
            $options[$index][static::GRID_TYPE_SELECT_NAME] = [];
            foreach ($values as $value) {
                $options[$index][static::GRID_TYPE_SELECT_NAME][] = $this->formatPriceByPath(
                    static::FIELD_PRICE_NAME,
                    $value->getData()
                );
            }
        }

        return [$templateDisabled, $options];
    }

    /**
     * @param array $optionData
     *
     * @return array
     */
    protected function convertDependency(array $optionData)
    {
        $depValue = $optionData[TemplateOptionInterface::DEPENDENCY] ?? '[]';
        $dependencyArray = $this->jsonSerializer->unserialize(
            $depValue ?: '[]'
        );

        foreach ($dependencyArray as &$dependency) {
            if ($dependency[static::FIELD_DEPENDENCY_OPTION] ?? false) {
                $dependency[static::FIELD_DEPENDENCY_OPTION] = 'id_' . $dependency[static::FIELD_DEPENDENCY_OPTION];

                $values = $dependency[static::FIELD_DEPENDENCY_VALUE];
                $values = is_array($values) ? $values : [$values];
                $dependency[static::FIELD_DEPENDENCY_VALUE] = [];
                foreach ($values as $value) {
                    $dependency[static::FIELD_DEPENDENCY_VALUE][] = 'id_' . $value;
                }
            }
        }

        $optionData[TemplateOptionInterface::DEPENDENCY] = $dependencyArray;

        return $optionData;
    }

    /**
     * @return bool
     */
    protected function isFieldSetAvailable()
    {
        $product = $this->locator->getProduct();
        return in_array(
            $product->getTypeId(),
            [Configurable::TYPE_CODE, Type::DEFAULT_TYPE, Type::TYPE_VIRTUAL, TypeD::TYPE_DOWNLOADABLE]
        ) || ($product->getTypeId() == 'bundle' && $product->getPriceType() == Price::PRICE_TYPE_FIXED);
    }
}
