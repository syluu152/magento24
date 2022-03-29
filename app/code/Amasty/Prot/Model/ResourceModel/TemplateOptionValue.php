<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Model\ResourceModel;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TemplateOptionValue extends AbstractDb
{
    const PRODUCT_OPTION_VALUE_FIELD = 'catalog_product_option_value_id';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TemplateOptionValueInterface::MAIN_TABLE, TemplateOptionValueInterface::ID);
    }

    /**
     * @param AbstractModel|TemplateOptionValueInterface $object
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->getOptionValueId()) {
            $insertData = [
                TemplateOptionValueInterface::RELATION_TEMPLATE_VALUE_ID => $object->getId(),
                TemplateOptionValueInterface::RELATION_VALUE_ID => $object->getOptionValueId(),
            ];
            $this->getConnection()->insertOnDuplicate(
                $this->getTable(TemplateOptionValueInterface::RELATION_TABLE),
                $insertData
            );
        }

        return parent::_afterSave($object);
    }
}
