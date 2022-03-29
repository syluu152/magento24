<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\CustomerGroup\Model\DiscountProduct\ResourceModel\DiscountProduct;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tigren\CustomerGroup\Model\DiscountProduct\ResourceModel\DiscountProduct;

/**
 *
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    public function _construct()
    {

        $this->_init(\Tigren\CustomerGroup\Model\DiscountProduct\DiscountProduct::class, DiscountProduct::class);
    }

    /**
     * @param $idDiscount
     * @return array|null
     */
    public function getListIdProductOfDiscount($idDiscount)
    {

        $this->getSelect()->where('main_table.id_discount = ' . $idDiscount);

        return $this->getData();
    }

    /**
     * @param $CustomerGroup
     * @param $idProduct
     * @return mixed|null
     */
    public function getDiscountOfProduct(
        $CustomerGroup,
        $idProduct
    ) {
        $discount = null;

        $obj = ObjectManager::getInstance();
        $discountFactory = $obj->get('Tigren\CustomerGroup\Model\Discount\ResourceModel\Discount\CollectionFactory');
        $discountCollection = $discountFactory->create();

        $dataListDiscount = $discountCollection->getListDiscount($CustomerGroup);

        if (count($dataListDiscount) != 0) {
            $listIdDiscount = '(';
            foreach ($dataListDiscount as $index => $value) {
                if (($index + 1) == count($dataListDiscount)) {
                    $listIdDiscount .= $value['id_discount'] . ')';
                } else {
                    $listIdDiscount .= $value['id_discount'] . ',';
                }
            }

            $this->getSelect()->join(
                ['discount' => 'tigren_discounts'],
                'discount.id = main_table.id_discount'
            )->where('main_table.id_product = ' . $idProduct
            )->where('main_table.id_discount in ' . $listIdDiscount);

            $listDiscountCanUse = $this->getData();

            if (!empty($listDiscountCanUse)) {
                $priorityMax = -1;

                foreach ($listDiscountCanUse as $value) {
                    if ($value['priority'] > $priorityMax) {
                        $priorityMax = $value['priority'];
                    }
                }
                $discountPriorityMax = array();
                foreach ($listDiscountCanUse as $index => $value) {
                    if ($value['priority'] == $priorityMax) {
                        $discountPriorityMax[$index] = $value;
                    }
                }

                $discount = $discountPriorityMax[0];
                for ($i = 1; $i < count($discountPriorityMax); $i++) {
                    if ($discountPriorityMax[$i]['discount_amount'] > $discount['discount_amount']) {
                        $discount = $discountPriorityMax[$i];
                    }
                }

                return $discount;
            } else {
                return $discount;
            }
        } else {
            return $discount;
        }

    }
}
