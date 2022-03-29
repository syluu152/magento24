/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * Modify check for refresh options. (Change option type must trigger refresh options in cache)
 */
define([
    'Magento_Ui/js/form/element/ui-select',
    'ko'
], function (uiSelect, ko) {
    return uiSelect.extend({
        defaults: {
            disableLabel: true,
            multiple: false,
            visible: true,
            elementTmpl: 'ui/grid/filters/elements/ui-select',
            validation: {
                'validate-select': true
            }
        },

        checkOptionsList: function (options) {
            this.cacheOptions.plain = options;
            var length;
            if (!_.isArray(this.value()) && this.value()) {
                length = 1;
            } else if (this.value()) {
                length = this.value().length;
            } else {
                this.value([]);
                length = 0;
            }
            if (length && !this.getSelected().length) {
                this.value([]);
            }
            this.setCaption();
        },

        /**
         * Check hovered option
         * copy from 2.3.4 - because they fixed bug fron 2.2.9
         *
         * @param {Object} data - element data
         * @return {Boolean}
         */
        isHovered: function (data) {
            var element = this.hoveredElement,
                elementData;

            if (!element) {
                return false;
            }

            elementData = ko.dataFor(this.hoveredElement);

            if (_.isUndefined(elementData)) {
                return false;
            }

            return data.value === elementData.value;
        },
    })
});
