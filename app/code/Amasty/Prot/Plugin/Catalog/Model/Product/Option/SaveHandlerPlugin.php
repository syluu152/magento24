<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Model\Product\Option\SaveHandler;

class SaveHandlerPlugin
{
    const EXCLUDE_AMASTY_TEMPLATE_OPTIONS = 'exclude_amasty_template_options';

    /**
     * @param SaveHandler $subject
     * @param $entity
     * @param array $arguments
     *
     * @return array
     */
    public function beforeExecute($subject, $entity, $arguments = [])
    {
        $entity->setData(self::EXCLUDE_AMASTY_TEMPLATE_OPTIONS, true);
        return [$entity, $arguments];
    }
}
