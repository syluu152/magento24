<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Ui\DataProvider\Product\Form\Modifier;

use Amasty\Prot\Api\TemplateRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

class GridOptionTemplates extends OptionTemplates
{
    const CHANGED_TEMPLATE = 'amprot_templates_form';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Amasty\Prot\Model\ResourceModel\Template
     */
    private $resourceTemplate;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

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
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Amasty\Prot\Model\ResourceModel\Template $resourceTemplate,
        ImageHelper $imageHelper,
        \Magento\Framework\App\RequestInterface $request,
        $visible = true
    ) {
        parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager,
            $templateRepository,
            $templateOptionRepository,
            $metadataPool,
            $jsonSerializer,
            $mediaHelper,
            $magentoVersion,
            $visible
        );

        $this->productCollectionFactory = $productCollectionFactory;
        $this->resourceTemplate = $resourceTemplate;
        $this->imageHelper = $imageHelper;
        $this->request = $request;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->createCustomOptionsPanel();

        return $this->meta;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function modifyData(array $data)
    {
        $templates = ['title' => ''];
        $templateId = (int)$this->request->getParam('id');
        if ($templateId) {
            $template = $this->templateRepository->get($templateId);

            $templateOptions = $this->templateOptionRepository->getProductOptions(
                $template->getTemplateId(),
                0,
                null,
                true
            );

            $templates = $this->convertTemplateData($template, array_values($templateOptions));
        }

        $data = $this->generateData($data, $templates);

        return $data;
    }

    /**
     * @param int $templateId
     * @return array
     */
    private function getProducts(int $templateId)
    {
        $field = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $productIds = array_unique($this->resourceTemplate->getAllTemplateProducts($templateId));
        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left')
            ->addAttributeToFilter($field, ['in' => $productIds])->getItems();

        return array_map([$this, 'fillData'], $products);
    }

    /**
     * @param array $data
     * @param array $template
     * @return array
     */
    protected function generateData($data = [], $template = [])
    {
        $products = [];
        if (isset($template['template_id'])) {
            $templateId = $template['template_id'];
            $products = array_values($this->getProducts($templateId));
        } else {
            $templateId = null;
        }

        $result = array_replace_recursive(
            $data,
            [
                $templateId => [
                    'data' => [
                        static::DATA_SOURCE_DEFAULT => [
                            static::FIELD_ENABLE => 1,
                            static::GRID_TEMPLATES_NAME => [$template]
                        ]
                    ],
                    'parent_ids' => [
                        'products_list_container' => $products
                    ]
                ]
            ]
        );

        return $result;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return array
     */
    public function fillData(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return [
            'entity_id' => $product->getId(),
            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
            'name' => $product->getName(),
            'status' => $product->getStatus(),
            'type_id' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'price' => $product->getPrice() ?: '-'
        ];
    }
}
