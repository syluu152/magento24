<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\CatalogStaging\Model\Product\Operation\Update;

use Amasty\Prot\Plugin\Catalog\Model\Product\Option\SaveHandlerPlugin;
use Magento\CatalogStaging\Model\Product\Operation\Update\TemporaryUpdateProcessor;

class TemporaryUpdateProcessorPlugin
{
    /**
     * @param TemporaryUpdateProcessor $subject
     * @param $entity
     * @return array
     */
    public function beforeLoadEntity($subject, $entity)
    {
        $entity->setData(SaveHandlerPlugin::EXCLUDE_AMASTY_TEMPLATE_OPTIONS, true);
        return [$entity];
    }
}
