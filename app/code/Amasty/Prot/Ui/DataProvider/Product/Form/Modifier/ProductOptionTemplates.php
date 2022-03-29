<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;

class ProductOptionTemplates extends OptionTemplates
{
    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getHeaderTemplateContainerConfig($sortOrder)
    {
        $result = parent::getHeaderTemplateContainerConfig($sortOrder);
        $result['children'] = [
            static::BUTTON_ADD_NEW => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'title' => __('Add New Template'),
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'component' => 'Magento_Ui/js/form/components/button',
                            'sortOrder' => 20,
                            'actions' => [
                                [
                                    'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                        . static::GRID_TEMPLATES_NAME,
                                    'actionName' => 'processingAddChild'
                                ]
                            ]
                        ]
                    ],
                ],
            ],
            static::BUTTON_ADD_EXISTING => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'title' => __('Add Existing Template'),
                            'formElement' => Container::NAME,
                            'componentType' => Container::NAME,
                            'component' => 'Magento_Ui/js/form/components/button',
                            'actions' => [
                                [
                                    'targetName' => 'ns=' . static::FORM_NAME . ', index=options',
                                    'actionName' => 'clearDataProvider'
                                ],
                                [
                                    'targetName' => 'ns=' . static::FORM_NAME . ', index='
                                        . static::EXISTING_TEMPLATES_MODAL,
                                    'actionName' => 'openModal',
                                ],
                                [
                                    'targetName' => 'ns=' . static::EXISTING_TEMPLATES_LISTING
                                        . ', index=' . static::EXISTING_TEMPLATES_LISTING,
                                    'actionName' => 'render',
                                ],
                            ],
                            'sortOrder' => 10,
                        ],
                    ],
                ]
            ]
        ];

        return $result;
    }
}
