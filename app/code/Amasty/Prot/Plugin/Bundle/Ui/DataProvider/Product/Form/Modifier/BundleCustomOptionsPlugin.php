<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Plugin\Bundle\Ui\DataProvider\Product\Form\Modifier;

use Magento\Bundle\Ui\DataProvider\Product\Form\Modifier\BundleCustomOptions;
use Amasty\Prot\Ui\DataProvider\Product\Form\Modifier\OptionTemplates;

class BundleCustomOptionsPlugin
{
    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * @param BundleCustomOptions $subject
     * @param array $meta
     *
     * @return array
     */
    public function afterModifyMeta(BundleCustomOptions $subject, array $meta)
    {
        if ($groupCode = $this->getGroupCodeByField($meta, OptionTemplates::CONTAINER_TEMPLATES_HEADER_NAME)) {
            $meta[$groupCode]['children']['message'] = $subject->getErrorMessage(0);

            if (!empty($meta[$groupCode]['children'][OptionTemplates::CONTAINER_TEMPLATES_HEADER_NAME])) {
                $meta = $subject->modifyCustomOptionsButton(
                    $meta,
                    $groupCode,
                    OptionTemplates::CONTAINER_TEMPLATES_HEADER_NAME,
                    OptionTemplates::BUTTON_ADD_NEW
                );
                $meta = $subject->modifyCustomOptionsButton(
                    $meta,
                    $groupCode,
                    OptionTemplates::CONTAINER_TEMPLATES_HEADER_NAME,
                    OptionTemplates::BUTTON_ADD_EXISTING
                );
            }
        }

        return $meta;
    }

    /**
     * @param array $meta
     * @param $field
     *
     * @return bool|int|string
     */
    protected function getGroupCodeByField(array $meta, $field)
    {
        foreach ($meta as $groupCode => $groupData) {
            if (isset($groupData['children'][$field])
                || isset($groupData['children'][static::CONTAINER_PREFIX . $field])
            ) {
                return $groupCode;
            }
        }

        return false;
    }
}
