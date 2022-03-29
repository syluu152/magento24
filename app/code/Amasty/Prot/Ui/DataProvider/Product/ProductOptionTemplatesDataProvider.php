<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\DataProvider\Product;

use Amasty\Prot\Api\Data\TemplateInterface;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Amasty\Prot\Model\ResourceModel\Template\Grid\ModalCollectionFactory;
use Magento\Catalog\Model\Product\Option\Value as ProductOptionValueModel;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class ProductOptionTemplatesDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProductOptionValueModel
     */
    private $productOptionValueModel;

    /**
     * @var \Amasty\Prot\Model\Repository\TemplateOption
     */
    private $templateOptionRepository;

    /**
     * @var Form\Modifier\OptionTemplates
     */
    private $optionTemplatesModifier;

    /**
     * @var \Amasty\Prot\Model\Repository\Template
     */
    private $templateRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ModalCollectionFactory $collectionFactory,
        ProductOptionValueModel $productOptionValueModel,
        \Amasty\Prot\Model\Repository\TemplateOption $templateOptionRepository,
        \Amasty\Prot\Model\Repository\Template $templateRepository,
        RequestInterface $request,
        \Amasty\Prot\Ui\DataProvider\Product\Form\Modifier\OptionTemplates $optionTemplatesModifier,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->productOptionValueModel = $productOptionValueModel;
        $this->templateOptionRepository = $templateOptionRepository;
        $this->optionTemplatesModifier = $optionTemplatesModifier;
        $this->templateRepository = $templateRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $templates = [];
        $currentProductId = (int)$this->request->getParam('current_product_id');

        if (0 !== $currentProductId) {
            $ids = $this->templateRepository->getTemplateIdsByProduct($currentProductId);
            if ($ids) {
                $this->getCollection()->addFieldToFilter('template_id', ['nin' => $ids]);
            }
        }

        /** @var TemplateInterface $template */
        foreach ($this->getCollection() as $template) {
            $options = [];

            /** @var ProductOption|DataObject $option */
            foreach ($this->getProductOptions($template->getTemplateId()) as $option) {
                if ($option->getValues()) {
                    foreach ($option->getValues() as $value) {
                        $value->setOptionTypeId(null);
                    }
                }

                $options[] = $option->setOptionId(null);
            }
            $template = $this->optionTemplatesModifier->convertTemplateData($template, $options);
            $template['new'] = 1;
            $templates[] = $template;
        }

        $data = [
            'totalRecords' => $this->getCollection() ->getSize(),
            'items' => $templates
        ];

        return $data;
    }

    /**
     * @param int $templateId
     * @param int $storeId
     * @param null|int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    protected function getProductOptions(int $templateId, $storeId = 0, $productId = null)
    {
        return $this->templateOptionRepository->getProductOptions($templateId, $storeId, $productId);
    }
}
