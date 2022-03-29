<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Amasty\Base\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Notice as NativeNotice;

class Notice
{
    public function aroundRender(
        NativeNotice $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);

        $amastyLogo = '';
        $amastyImage = '';
        if ($row->getData('is_amasty')) {
            if ($row->getData('image_url')) {
                $amastyImage = ' style="background: url(' . $row->getData("image_url") . ') no-repeat;"';
            } else {
                $amastyLogo = ' amasty-grid-logo';
            }
        }
        $result = '<div class="ambase-grid-message' . $amastyLogo . '"' . $amastyImage . '>' . $result . '</div>';

        return  $result;
    }
}
