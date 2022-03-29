<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Amasty\Prot\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class QuickStartGuide extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Return header comment part of html for fieldset
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getHeaderCommentHtml($element)
    {
        $text = nl2br(sprintf(
            '<div class="comment">%s</div>',
            __(
                'There are two ways to create a new options template:
                1. At any product configuration page, please open Customizable Option Templates by Amasty
                expand -> Click \'Add New Template\'and define the options -> Save the product.
                2. Go to Template List page and click \'Add New Template\' -> Define the options
                and assign at least one product -> Save the template.

                To add an existing template to multiple products:
                - Either at Product Grid, select only products you would like to add this new template to and
                with a new Mass Edit option, \'Add Custom Option Template\', choose and apply that new template
                to each product you selected.
                - Or at Template List page choose the required template and open it in edit mode
                -> Click \'Add Products\' and select the needed, confirm the action and save the template.

                To delete the existing template:
                - either remove it from all products,
                - or delete it at Catalog > Product Option Templates > Template List.'
            )
        ));
        $template = __('Template List');
        $url = $this->getUrl('amprot/templates/index');

        return str_replace($template, sprintf('<a href="%s">%s</a>', $url, $template), $text);
    }
}
