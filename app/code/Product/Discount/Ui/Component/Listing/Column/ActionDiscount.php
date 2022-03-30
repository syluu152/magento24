<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ProductActions
 *
 * @api
 * @since 100.0.2
 */
class ActionDiscount extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit' => [
                        'href' => $this->urlBuilder->getUrl(
                            'discount_admin/discount/edit',
                            ['id' => $item['id']]
                        ),
                        'label' => __('Edit'),
                        'hidden' => false,
                    ],
                    'viewproduct' => [
                        'href' => $this->urlBuilder->getUrl(
                            'discount_admin/discount/productOfDiscount',
                            ['id' => $item['id']]
                        ),
                        'label' => __('Products of Discount'),
                        'hidden' => false,
                    ],
                    'addproduct' => [
                        'href' => $this->urlBuilder->getUrl(
                            'discount_admin/discount/product',
                            ['id' => $item['id']]
                        ),
                        'label' => __('Add Product'),
                        'hidden' => false,
                    ]
                ];

            }
        }
        return $dataSource;
    }
}
