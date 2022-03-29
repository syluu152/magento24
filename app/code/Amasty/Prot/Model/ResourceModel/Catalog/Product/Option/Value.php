<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Catalog\Product\Option;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Catalog\Model\Product\Option\Value as OptionValue;

class Value extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value
{
    /**
     * @param array $amastyOptionIdsRelation
     * @param int $origId
     * @param int $productId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function updateValuesForOption(array $amastyOptionIdsRelation, int $origId, int $productId)
    {
        $connection = $this->getConnection();

        $valuesCond = [];
        $relationsData = [];
        $valuesData = $this->getValuesData(array_keys($amastyOptionIdsRelation), $origId);
        $existedValues = $this->getExistedValues(array_keys($amastyOptionIdsRelation), $productId);

        // insert values to duplicated product
        foreach ($valuesData as $oId => $data) {
            $data['option_id'] = $amastyOptionIdsRelation[$data[TemplateOptionValueInterface::TEMPLATE_OPTION_ID]];
            $parentId = $data[TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID];
            unset($data[TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID]);
            unset($data[TemplateOptionValueInterface::TEMPLATE_OPTION_ID]);

            if (isset($existedValues[$parentId])) {
                $valuesCond[$oId] = $existedValues[$parentId];
                $connection->update($this->getMainTable(), $data, 'option_type_id=' . $existedValues[$parentId]);
            } else {
                $connection->insert($this->getMainTable(), $data);
                $newValueId = $connection->lastInsertId($this->getMainTable());
                $valuesCond[$oId] = $newValueId;
                $relationsData[] = [
                    TemplateOptionValueInterface::RELATION_VALUE_ID => $newValueId,
                    TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID => $parentId
                ];
            }
        }

        foreach ($existedValues as $parentId => $exValueId) {
            if (!in_array($exValueId, $valuesCond)) {
                $connection->delete($this->getMainTable(), 'option_type_id=' . $exValueId);
            }
        }

        if ($relationsData) {
            $connection->insertMultiple($this->getTable(TemplateOptionValueInterface::RELATION_TABLE), $relationsData);
        }

        // copy options prefs
        foreach ($valuesCond as $oldValueId => $newValueId) {
            // title
            $table = $this->getTable('catalog_product_option_type_title');

            $select = $this->getConnection()->select()->from(
                $table,
                [new \Zend_Db_Expr($newValueId), 'store_id', 'title']
            )->where(
                'option_type_id = ?',
                $oldValueId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_type_id', 'store_id', 'title'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            // price
            $table = $this->getTable('catalog_product_option_type_price');

            $select = $connection->select()->from(
                $table,
                [new \Zend_Db_Expr($newValueId), 'store_id', 'price', 'price_type']
            )->where(
                'option_type_id = ?',
                $oldValueId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_type_id', 'store_id', 'price', 'price_type'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);
        }
    }

    /**
     * @param array $amastyOptionIds
     * @param int $origId
     *
     * @return mixed
     * @throws \Zend_Db_Statement_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getValuesData(array $amastyOptionIds, int $origId)
    {
        $connection = $this->getConnection();
        $valuesData = [];

        $query = $connection->query($this->getValuesSelect($amastyOptionIds, $origId));
        while ($row = $query->fetch()) {
            $valuesData[$row['option_type_id']] = $row;
            unset($valuesData[$row['option_type_id']]['option_type_id']);
        }

        return $valuesData;
    }

    /**
     * @param array $amastyOptionIds
     * @param int $productId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    protected function getExistedValues(array $amastyOptionIds, int $productId)
    {
        $connection = $this->getConnection();
        $values = [];

        $query = $connection->query($this->getValuesSelect($amastyOptionIds, $productId));

        while ($row = $query->fetch()) {
            $values[$row[TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID]] = $row['option_type_id'];
        }

        return $values;
    }

    /**
     * @param $amastyOptionIds
     * @param $productId
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getValuesSelect($amastyOptionIds, $productId)
    {
        return $this->getConnection()->select()->from(
            ['main_table' => $this->getMainTable()]
        )->joinInner(
            ['option_table' => $this->getTable('catalog_product_option')],
            'main_table.option_id = option_table.option_id',
            []
        )->joinInner(
            ['prot_relation' => $this->getTable(TemplateOptionValueInterface::RELATION_TABLE)],
            sprintf(
                'prot_relation.%s = main_table.option_type_id',
                TemplateOptionValueInterface::RELATION_VALUE_ID
            ),
            [TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID]
        )->joinInner(
            ['prot_values' => $this->getTable(TemplateOptionValueInterface::MAIN_TABLE)],
            sprintf(
                'prot_relation.%s = prot_values.%s',
                TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID,
                TemplateOptionValueInterface::ID
            ),
            [TemplateOptionValueInterface::TEMPLATE_OPTION_ID]
        )->where(
            'product_id = ?',
            $productId
        )->where(
            sprintf('prot_values.%s in(?)', TemplateOptionValueInterface::TEMPLATE_OPTION_ID),
            $amastyOptionIds
        );
    }
}
