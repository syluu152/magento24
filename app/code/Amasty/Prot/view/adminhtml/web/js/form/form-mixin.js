/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'uiRegistry'
], function ($, modalConfirm, uiRegistry) {
    'use strict';

    var mixin = {
        defaults: {
            templatesContainerName: 'product_form.product_form.amcustom_options.templates',
            productsContainerName: 'amprot_templates_form.amprot_templates_form.parent_products.products_list_container',
            productName: 'amprot_templates_form.amprot_templates_form.parent_products.products_list_container.0'
        },

        submit: function (redirect) {
            var submit = this._super.bind(this, redirect);

            if (uiRegistry.get(this.productsContainerName) != undefined
                && uiRegistry.get(this.productName) == undefined
            ) {
                modalConfirm({
                    title: $.mage.__('Error'),
                    content: $.mage.__("Please assign at least one product to the template.")
                });
            } else {
                if (this._checkIsOptionsChanged()) {
                    modalConfirm({
                        title: $.mage.__('Save current product?'),
                        content: $.mage.__("Each change made inside this product's Template will be applied to each product" +
                            " into which that template was added " +
                            "(instantly upon saving changes in product if there are less than 100 products to update, upon nearest Cron run otherwise).</br>" +
                            "Would you like to proceed?"),
                        actions: {
                            confirm: function () {
                                submit();
                            }
                        }
                    });
                } else {
                    submit();
                }
            }
        },

        _checkIsOptionsChanged: function () {
            var changed = false,
                templatesContainer = uiRegistry.get(this.templatesContainerName);

            if (templatesContainer) {
                templatesContainer.elems().each(function (template) {
                    if (!template.data() || !template.data().template_id) {
                        return;
                    }
                    var optionsContainer = uiRegistry.get(template.name + '.amprot-fieldset-with-message.container_template.options');
                    if (optionsContainer) {
                        optionsContainer.elems().each(function (option) {
                            if (typeof option.elems()[0] != 'undefined' && option.elems()[0].changed()) {
                                changed = true;
                            }
                        });
                    }
                });
            }

            return changed;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
