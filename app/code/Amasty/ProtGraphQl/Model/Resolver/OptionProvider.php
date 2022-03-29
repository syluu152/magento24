<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\ProtGraphQl\Model\Resolver;

use Amasty\Label\Api\Data\LabelInterface as LabelInterface;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Store\Model\ScopeInterface;

class OptionProvider implements ResolverInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Prot\Block\Catalog\Block\Product\View\Options
     */
    private $optionsBlock;

    /**
     * @var \Amasty\Prot\Model\Serializer\Json
     */
    private $jsonSerializer;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Prot\Block\Catalog\Block\Product\View\Options $optionsBlock,
        \Amasty\Prot\Model\Serializer\Json $jsonSerializer
    ) {
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->optionsBlock = $optionsBlock;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['productId'])) {
            throw new GraphQlNoSuchEntityException(__('Wrong parameter provided.'));
        }

        try {
            $options = [];
            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
            $product = $this->initProduct((int)$args['productId'], $storeId);
            $block = $this->optionsBlock->setProduct($product)->setData('jsonSerializer', $this->jsonSerializer);

            foreach ($block->getOptions() as $option) {
                $optionData = $option->getData();
                $optionData['required'] = (bool)$option->getData('is_require');

                $values = $option->getValues() ?: [];
                /** @var Option\Value $value */
                foreach ($values as $valueKey => $value) {
                    $optionData['value'][$valueKey] = $value->getData();
                    $optionData['value'][$valueKey]['price_type']
                        = $value->getPriceType() !== null ? strtoupper($value->getPriceType()) : 'DYNAMIC';
                }

                if (empty($values)) {
                    $optionData['value'] = $option->getData();
                    $optionData['value']['price_type']
                        = $option->getPriceType() !== null ? strtoupper($option->getPriceType()) : 'DYNAMIC';
                }

                $options[] = $optionData;
            }
            $data['items'] = $options;
            $data['dependencies'] = $this->convertConfig($this->optionsBlock->getDependencyConfig());

            return $data;
        } catch (\Exception $e) {
            throw new GraphQlNoSuchEntityException(__('Something went wrong.'));
        }
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initProduct(int $productId, int $storeId)
    {
        return $this->productRepository->getById(
            $productId,
            false,
            $storeId
        );
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function convertConfig(array $config)
    {
        foreach ($config as $key => $option) {
            if ($option) {
                foreach ($option as $optionKey => &$parent) {
                    $parent['option_id'] = $optionKey;
                }

                $config[$key] = [
                    'parents' => $option,
                    'option_id' => $key
                ];
            } else {
                unset($config[$key]);
            }
        }

        return $config;
    }
}
