<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Amasty\Prot\Model\ResourceModel\TemplateOptionValue;

use Amasty\Prot\Api\Data\TemplateOptionValueInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(TemplateOptionValueInterface::ID);
        $this->_init(
            \Amasty\Prot\Model\TemplateOptionValue::class,
            \Amasty\Prot\Model\ResourceModel\TemplateOptionValue::class
        );
    }
}
