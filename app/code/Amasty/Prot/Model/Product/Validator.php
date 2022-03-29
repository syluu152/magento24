<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Product;

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as TypeD;
use Magento\Framework\Message\ManagerInterface;

class Validator
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Validator constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param int $templateId
     * @param array $productIds
     *
     * @return array
     */
    public function validateProductIds(int $templateId, array $productIds)
    {
        $collection = $this->getCollection($productIds);
        $availableIds = $collection->load()->getLoadedIds();
        $invalidIds = array_diff($productIds, $availableIds);
        if ($invalidIds) {
            $this->addErrorMessage($invalidIds);
        }

        return $availableIds;
    }

    /**
     * @param array $productIds
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getCollection(array $productIds)
    {
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('price_type', 'left');

        $collection->getSelect()->where("type_id='bundle' AND at_price_type.value=?", Price::PRICE_TYPE_FIXED)
            ->orWhere(
                'type_id in(?)',
                [Configurable::TYPE_CODE, Type::DEFAULT_TYPE, Type::TYPE_VIRTUAL, TypeD::TYPE_DOWNLOADABLE]
            );
        $collection->addIdFilter($productIds);

        return $collection;
    }

    /**
     * @param array $invalidIds
     */
    protected function addErrorMessage(array $invalidIds)
    {
        $this->messageManager->addErrorMessage(__(
            'The template was not applied to product id(s) %1: Bundle/Dynamic Price, Grouped products and custom'
            . ' product types can\'t have Custom Options.',
            implode(', ', $invalidIds)
        ));
    }
}
