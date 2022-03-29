<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Product\Discount\Block\Adminhtml\Discount;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Product\Discount\Model\DiscountProduct\ResourceModel\DiscountProduct\Collection as discountProductCollection;

/**
 *
 */
class ProductOfDiscount extends Template
{
    /**
     * @var discountProductCollection
     */
    protected $_discountProductCollection;
    /**
     * @var ResourceConnection
     */
    protected $_resource;
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Context $context
     * @param ResourceConnection $Resource
     * @param discountProductCollection $DiscountProductCollection
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        ResourceConnection $Resource,
        discountProductCollection $DiscountProductCollection,
        ProductFactory $productFactory
    ) {
        $this->_productFactory = $productFactory;
        $this->_resource = $Resource;
        $this->_discountProductCollection = $DiscountProductCollection;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getAllDataProducts()
    {
        $id_discount = $this->getData('id_discount');
        $listDiscountForProduct = $this->_discountProductCollection->getListIdProductOfDiscount($id_discount);

        $listIdProduct = array();
        foreach ($listDiscountForProduct as $index => $value) {
            $listIdProduct[$index] = $value['id_product'];
        }

        $dataProduct = array();
        foreach ($listIdProduct as $index => $idProduct) {
            $dataProduct[$index] = $this->_productFactory->create()->load($idProduct);
        }

        return $dataProduct;
    }
}
