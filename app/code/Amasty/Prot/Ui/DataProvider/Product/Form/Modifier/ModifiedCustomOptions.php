<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Framework\Locale\CurrencyInterface;

class ModifiedCustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions
{
    const GROUP_CUSTOM_OPTIONS_NAME = 'template_custom_options';

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getHeaderContainerConfig($sortOrder)
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
                            'One template can have multiple Customizable Options incorporated into it.<br/> '
                            . 'Each change made inside this template will be applied to each product into which that '
                            . 'template was added.<br/> (instantly upon saving changes in product if there are'
                            . ' less than %1 products to update, upon nearest Cron run otherwise).',
                            \Amasty\Prot\Model\Repository\Template::MAX_PROCESS_COUNT
                        ),
                    ],
                ],
            ],
            'children' => [
                static::BUTTON_ADD => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add Option'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Amasty_Prot/js/form/components/button',
                                'sortOrder' => 20,
                                'actions' => [
                                    [
                                        'targetName' => '${ $.ns }.${ $.ns }.'
                                            . static::GROUP_CUSTOM_OPTIONS_TEMPLATES_NAME
                                            . '.' . static::GRID_TEMPLATES_NAME . '.' . static::CONTAINER_TEMPLATE .
                                            '.' . static::GRID_OPTIONS_NAME,
                                        'actionName' => 'processingAddChild',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     *
     * @return array
     */
    protected function getTypeFieldConfig($sortOrder)
    {
        $config = parent::getTypeFieldConfig($sortOrder);
        $config['arguments']['data']['config']['component'] = 'Amasty_Prot/js/components/custom-options-type-extend';

        return $config;
    }

    /**
     * @param int $sortOrder
     *
     * @return array
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        $config = parent::getSelectTypeGridConfig($sortOrder);
        $config['children']['record']['arguments']['data']['config']['component'] = 'Amasty_Prot/js/grid/record';
        $config['arguments']['data']['config']['component'] = 'Amasty_Prot/js/form/components/dynamic-rows';

        return $config;
    }

    /**
     * Get config for the whole grid
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getOptionsGridConfig($sortOrder)
    {
        $config = parent::getOptionsGridConfig($sortOrder);
        $config['arguments']['data']['config']['template'] = 'Amasty_Prot/dynamic-rows/templates/collapsible';

        return $config;
    }
}
