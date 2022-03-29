<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\Template;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type as TypeD;

class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        $addFieldStrategies = [],
        $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->initCollection();
    }

    private function initCollection()
    {
        $this->collection->addAttributeToSelect(['status', 'thumbnail', 'name', 'price'], 'left')
            ->addAttributeToSelect('price_type', 'left');
        $this->collection->getSelect()->where(
            "type_id='bundle' AND at_price_type.value=?",
            Price::PRICE_TYPE_FIXED
        )->orWhere(
            'type_id in(?)',
            [Configurable::TYPE_CODE, Type::DEFAULT_TYPE, Type::TYPE_VIRTUAL, TypeD::TYPE_DOWNLOADABLE]
        );
    }
}
