/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'uiRegistry',
    'Magento_Catalog/js/custom-options-type'
], function ($, registry, customOptions) {
    'use strict';

    return customOptions.extend({
        defaults: {
            swatchCheckboxIndex: 'use_swatches',
            allowedOptionTypes: {
                typeDropDown: 'drop_down',
                typeMultiple: 'multiple'
            }
        },

        /**
         * EXTEND Show, hide or clear components based on the current type value.
         *
         * @param {String} currentValue
         * @param {Boolean} isInitialization
         * @returns {Element}
         */
        updateComponents: function (currentValue, isInitialization) {
            this._super();

            this._toggleComponentState(currentValue);
        },

        /**
         * Toggle state for only dropdown and multiple type
         *
         * @returns {Object}
         */
        _toggleComponentState: function (currentValue) {
            var component = this._getSwatch();

            if (component) {
                (currentValue === this.allowedOptionTypes.typeDropDown
                  || currentValue === this.allowedOptionTypes.typeMultiple)
                  ? component.visible(true) : component.checked(false).visible(false);
            }
        },

        /**
         * Get swatch checkbox uiClass
         *
         * @returns {Object}
         */
        _getSwatch: function () {
            var template = 'ns=' + this.ns +
              ', dataScope=' + this.parentScope + '.' + this.swatchCheckboxIndex +
              ', index=' + this.swatchCheckboxIndex;

            return registry.get(template);
        }
    });
});
