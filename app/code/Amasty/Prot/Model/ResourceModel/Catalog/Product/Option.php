<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Catalog\Product;

use Amasty\Prot\Api\Data\TemplateOptionInterface;
use Amasty\Prot\Model\ResourceModel\Catalog\Product\Option\Value as ValueResource;

class Option extends \Magento\Catalog\Model\ResourceModel\Product\Option
{
    /**
     * @param int $id
     * @param int $parentId
     */
    public function deleteRelation(int $id, int $parentId)
    {
        $this->getConnection()->delete(
            $this->getTable(TemplateOptionInterface::RELATION_TABLE),
            sprintf('option_id=%s AND parent_option_id=%s', $id, $parentId)
        );
    }

    /**
     * @param array $amastyOptionIds
     * @param int $origId
     * @param int $productId
     * @param ValueResource $valueResource
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
     */
    public function updateOptions(array $amastyOptionIds, int $origId, int $productId, ValueResource $valueResource)
    {
        $connection = $this->getConnection();

        $optionsCond = [];
        $relationsData = [];
        $optionsData = $this->getOptionsData($amastyOptionIds, $origId, $productId);
        $existedOptions = $this->getExistedOptions($amastyOptionIds, $productId);

        $amastyOptionIdsRelation = [];

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $parentId = $data['parent_option_id'];
            unset($data['parent_option_id']);

            if (isset($existedOptions[$parentId])) {
                $optionsCond[$oId] = $existedOptions[$parentId];
                $connection->update($this->getMainTable(), $data, 'option_id=' . $existedOptions[$parentId]);
                $amastyOptionIdsRelation[$parentId] = $existedOptions[$parentId];
            } else {
                $connection->insert($this->getMainTable(), $data);
                $newOptionId = $connection->lastInsertId($this->getMainTable());
                $optionsCond[$oId] = $newOptionId;
                $relationsData[] = [
                    'option_id' => $newOptionId,
                    'parent_option_id' => $parentId
                ];
                $amastyOptionIdsRelation[$parentId] = $newOptionId;
            }
        }

        foreach ($existedOptions as $parentId => $exOptionId) {
            if (!in_array($exOptionId, $optionsCond)) {
                $connection->delete($this->getMainTable(), 'option_id=' . $exOptionId);
            }
        }

        if ($relationsData) {
            $connection->insertMultiple($this->getTable(TemplateOptionInterface::RELATION_TABLE), $relationsData);
        }

        // copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog_product_option_title');

            $select = $this->getConnection()->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'title']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'title'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);

            // price
            $table = $this->getTable('catalog_product_option_price');

            $select = $connection->select()->from(
                $table,
                [new \Zend_Db_Expr($newOptionId), 'store_id', 'price', 'price_type']
            )->where(
                'option_id = ?',
                $oldOptionId
            );

            $insertSelect = $connection->insertFromSelect(
                $select,
                $table,
                ['option_id', 'store_id', 'price', 'price_type'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
            );
            $connection->query($insertSelect);
        }
        $valueResource->updateValuesForOption($amastyOptionIdsRelation, $origId, $productId);

        return true;
    }

    /**
     * @param array $amastyOptionIds
     * @param int $origId
     * @param int $productId
     *
     * @return mixed
     * @throws \Zend_Db_Statement_Exception
     */
    protected function getOptionsData(array $amastyOptionIds, int $origId, int $productId)
    {
        $connection = $this->getConnection();
        $optionsData = [];
        // read and prepare product options related to template
        $select = $connection->select()->from(
            ['main_table' => $this->getTable('catalog_product_option')]
        )->joinInner(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            ['parent_option_id']
        )->where(
            'product_id = ?',
            $origId
        )->where(
            'prot_relation.parent_option_id in(?)',
            $amastyOptionIds
        );

        $query = $connection->query($select);
        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $productId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        return $optionsData;
    }

    /**
     * @param array $amastyOptionIds
     * @param int $productId
     *
     * @return array
     */
    protected function getExistedOptions(array $amastyOptionIds, int $productId)
    {
        $connection = $this->getConnection();
        $options = [];

        // read and prepare product options related to template
        $select = $connection->select()->from(
            ['main_table' => $this->getTable('catalog_product_option')]
        )->joinInner(
            ['prot_relation' => $this->getTable(TemplateOptionInterface::RELATION_TABLE)],
            'prot_relation.option_id = main_table.option_id',
            ['parent_option_id']
        )->where(
            'product_id = ?',
            $productId
        )->where(
            'prot_relation.parent_option_id in(?)',
            $amastyOptionIds
        );

        $query = $connection->query($select);

        while ($row = $query->fetch()) {
            $options[$row['parent_option_id']] = $row['option_id'];
        }

        return $options;
    }
}
