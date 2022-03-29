/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (_, uiRegistry, dynamicRows) {
    return dynamicRows.extend({
        defaults: {
            templateName: ''
        },

        initialize: function () {
            this._super();

            this.templateName = this._getTemplateName();

            this.requireOptionChanged(
                this.source.get(this.dataScope + '.is_require')
            );

            return this;
        },

        /**
         * Disable or enable dependency section for custom option depend on value of corresponding REQUIRED field
         * @param is_require
         */
        requireOptionChanged: function (is_require) {
            this.disabled(!!+is_require);
        },

        /**
         * Trigger changed event if record deleted for determine template changes
         * @param event
         * @returns {boolean|*}
         */
        bubble: function (event) {
            if (event === 'deleteRecord' || event === 'update') {
                uiRegistry.get(this.templateName).changed(true);
            }

            return this._super();
        },

        /**
         *  Get current template UI name
         * @returns string
         */
        _getTemplateName: function () {
            return this.name.replace(/container_template.*/, 'container_template')
        }
    });
});
