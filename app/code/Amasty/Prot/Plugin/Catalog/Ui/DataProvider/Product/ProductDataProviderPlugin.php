<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Ui\DataProvider\Product;

use Amasty\Prot\Controller\Adminhtml\Product\MassAssign;
use Amasty\Prot\Controller\Adminhtml\Product\MassRemove;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider as ProductDataProvider;
use Magento\Framework\AuthorizationInterface;

class ProductDataProviderPlugin
{
    const GRID_NAME = 'product_listing_data_source';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Amasty\Prot\Model\Source\Template
     */
    private $templateSource;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Amasty\Prot\Model\Source\Template $templateSource,
        AuthorizationInterface $authorization
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->templateSource = $templateSource;
        $this->authorization = $authorization;
    }

    /**
     * Add plugin because on magento 2.2.9 modifier doesn't exist
     *
     * @param ProductDataProvider $subject
     * @param array $meta
     *
     * @return array
     */
    public function afterGetMeta(ProductDataProvider $subject, array $meta)
    {
        if ($subject->getName() === self::GRID_NAME) {
            $children = &$meta['listing_top']['children']['listing_massaction']['children'];
            if ($this->authorization->isAllowed(MassAssign::ADMIN_RESOURCE)) {
                $children['prot_add'] = $this->generateAddComponent();
            }

            if ($this->authorization->isAllowed(MassRemove::ADMIN_RESOURCE)) {
                $children['prot_remove'] = $this->generateRemoveComponent();
            }

            if (!$children) {
                $children = [];
            }
        }

        return $meta;
    }

    /**
     * @return array
     */
    protected function generateAddComponent()
    {
        $data = [
            'name' => 'prot_add',
            'confirm_title' => __('Assign Custom Option Template')->render(),
            'confirm_message' => __('Are you sure you want to assign template to selected items?')->render(),
            'label' => __('Assign Custom Option Template')->render(),
            'fieldLabel' => __('Assign')->render(),
            'url' => $this->urlBuilder->getUrl('amprot/product/massAssign'),
        ];

        return $this->generateElement($data);
    }

    /**
     * @return array
     */
    protected function generateRemoveComponent()
    {
        $data = [
            'name' => 'prot_remove',
            'confirm_title' => __('Remove Custom Option Template')->render(),
            'confirm_message' => __('Are you sure you want to remove template from selected items?')->render(),
            'label' => __('Remove Custom Option Template')->render(),
            'fieldLabel' => __('Remove')->render(),
            'url' => $this->urlBuilder->getUrl('amprot/product/massRemove'),
        ];

        return $this->generateElement($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function generateElement(array $data)
    {
        $result = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'uiComponent',
                        'componentType' => 'action',
                        'amasty_actions' => 'true',
                        'type' => 'amasty_' . $data['name'],
                        'label' => $data['label'],
                        'url' => $data['url'],
                        'confirm' => [
                            'title' => $data['confirm_title'],
                            'message' => $data['confirm_message'],
                        ],
                    ]
                ],
                'actions' => [
                    0 => [
                        'typefield' => 'select',
                        'fieldLabel' => $data['fieldLabel'],
                        'url' => $data['url'],
                        'type' => 'amasty_' . $data['name'],
                        'child' => []
                    ]
                ]
            ],
            'attributes' => [
                'class' => \Magento\Ui\Component\Action::class,
                'name' => $data['name']
            ],
            'children' => []

        ];

        foreach ($this->templateSource->toOptionArray() as $key => $attributeSet) {
            $result['arguments']['actions'][0]['child'][$key] = [
                'label' => $attributeSet['label'],
                'fieldvalue' => $attributeSet['value']
            ];
        }

        return $result;
    }
}
