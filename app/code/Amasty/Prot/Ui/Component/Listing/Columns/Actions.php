<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

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
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $itemName = $item['name'] ?? '';
                $itemQty = $item['qty'] ?? '';

                $item[$name]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amprot/templates/edit',
                        ['id' => $item['template_id']]
                    ),
                    'label' => __('Edit')
                ];

                $item[$name]['delete'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'amprot/templates/delete',
                        ['id' => $item['template_id']]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete %1', $itemName),
                        'message' => __(
                            '<p> This action will remove'
                            . ' %1 template entirely.</p><p> This action can\'t be reverted.</p><p>'
                            . ' %2 product(s) will be affected.</p><p> Would you like to proceed'
                            . ' with template deletion?</p>',
                            $itemName,
                            $itemQty
                        )
                    ]
                ];

                if ($item['status'] == \Amasty\Prot\Model\Source\Status::PROCESSING) {
                    $item[$name]['apply'] = [
                        'href'    => $this->urlBuilder->getUrl(
                            'amprot/templates/apply',
                            ['id' => $item['template_id']]
                        ),
                        'label'   => __('Apply'),
                        'confirm' => [
                            'title'   => __('Apply ${ $.$data.name }'),
                            'message' => __(
                                '<p>${ $.$data.name } Template will be applied without cron Schedule. It may affect'
                                . ' performance. </p><p>Would you like to proceed with template applying?</p>'
                            )
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
