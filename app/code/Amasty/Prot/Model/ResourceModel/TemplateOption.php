<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel;

use \Amasty\Prot\Api\Data\TemplateOptionInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class TemplateOption extends AbstractDb
{
    const PRODUCT_OPTION_FIELD = 'catalog_product_option_id';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TemplateOptionInterface::MAIN_TABLE, TemplateOptionInterface::OPTION_ID);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()]);
        if ($field === self::PRODUCT_OPTION_FIELD) {
            $relationTable = $this->getTable(TemplateOptionInterface::RELATION_TABLE);
            $select->join(
                ['prot_relation' => $relationTable],
                'prot_relation.parent_option_id = main_table.option_id',
                []
            );
            $field = 'prot_relation.option_id';
        } else {
            $field = $this->getConnection()->quoteIdentifier(sprintf('main_table.%s', $field));
        }

        $select->where($field . '=?', $value);
        return $select;
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $relationOptionId = $object->getData(self::PRODUCT_OPTION_FIELD);
        if ($relationOptionId) {
            $insertData = [
                TemplateOptionInterface::PARENT_OPTION_ID => $object->getOptionId(),
                TemplateOptionInterface::RELATION_OPTION_ID => $relationOptionId,
            ];
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(TemplateOptionInterface::RELATION_TABLE),
                $insertData
            );
        }

        return $this;
    }
}
