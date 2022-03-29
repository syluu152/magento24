/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'Magento_Ui/js/dynamic-rows/record'
], function (Record) {
    'use strict';

    return Record.extend({
        defaults: {
            swatchIndex: 'swatch_value',
        },

        /**
         * OVERRIDE Set visibility to record child
         *
         * @param {Boolean} state
         */
        setVisible: function (state) {
            var self = this;

            this.elems.each(function (cell) {
                cell.index == self.swatchIndex ? cell.visible(false) : cell.visible(state);
            });
        }
    });
});
