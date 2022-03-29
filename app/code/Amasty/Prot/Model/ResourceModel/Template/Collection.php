<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Model\ResourceModel\Template;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \Amasty\Prot\Api\Data\TemplateInterface;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setIdFieldName(TemplateInterface::TEMPLATE_ID);
        $this->_init(
            \Amasty\Prot\Model\Template::class,
            \Amasty\Prot\Model\ResourceModel\Template::class
        );
    }
}
