<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Block\Adminhtml\Discount;

use Magento\Framework\View\Element\Template;
use Tigren\CustomerGroup\Model\DiscountProduct\ResourceModel\DiscountProduct\Collection as discountProductCollection;

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
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\App\ResourceConnection $Resource
     * @param discountProductCollection $DiscountProductCollection
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $Resource,
        discountProductCollection $DiscountProductCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory
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
