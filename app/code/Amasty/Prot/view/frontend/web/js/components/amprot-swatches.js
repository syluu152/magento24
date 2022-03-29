/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * Default Swatches Module logic
 * @return widget
 */

define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('mage.amprotSwatches', {
        options: {
            id: null,
            type: null,
            selectedClass: '-selected',
            optionSelector: '[data-amprot-js="option"]',
            inputSelector: '[data-amprot-js="swatch-origin_{id}"]',
        },

        /**
         * Create Amasty Swatches Widget
         * @private
         */
        _create: function () {
            var self = this,
                options = self.options;

            this.swatchOption = this.element.find(options.optionSelector);
            this.input = this.element.find(options.inputSelector);

            this.swatchOption.on('click', function() {
                switch (options.type) {
                    case 'multiple':
                        $(this).toggleClass(options.selectedClass);
                        self._changeSelect(self.element);
                        break;
                    case 'default':
                    default:
                        self.swatchOption.not($(this)).removeClass(options.selectedClass);
                        $(this).toggleClass(options.selectedClass);
                        self._changeSelect(self.element);
                        break;
                }
            });
        },

        /**
         * Get element data-value attribute
         * @returns {Array}
         */
        getValue: function (element) {
            var result = [];

            element.each(function (index, item) {
                result.push(item.dataset.amprotValue);
            });

            return result;
        },

        /**
         * Set an option value into the origin select
         * @private
         */
        _changeSelect: function (element) {
            var options = this.options,
                selector = options.inputSelector.replace('{id}', options.id);

            $(selector).val(this.getValue(element.find('.' + options.selectedClass))).trigger('change');
        },
    });

    return $.mage.amprotSwatches;
});
