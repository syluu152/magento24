/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';

    var mixin = {

        /**
         * Duplicate target option
         *
         * @param {Number|String} option - prototype for clone
         */
        duplicateOption: function (option) {
            var index = this.recordData().length,
                data = this._getOptionData(option, index);

            this.recordData.push(data);
            this.processingAddChild(this, index, index + 1);
        },

        /**
         * Duplicate and prepared option data
         *
         * @param {Number|String} option - prototype for clone
         * @param {Number|String} index - next index
         */
        _getOptionData: function (option, index) {
            var data = option.data().option_id === undefined ? this.recordData()[option.data().record_id] : option.data();

            data = _.clone(data);
            data.option_id = null;
            data.record_id = index;
            data.parent_option_id = null;
            data.template_option_id = null;
            data.sort_order = index + 1;
            data.title = $t('Copy of ') + data.title;
            //data.dependency.clear(); // If we need to clear dependencies from custom options

            data.values.each(function (item) {
                item['option_id'] = null;
                item['option_type_id'] = null;
                item['template_option_value_id'] = null;
            });

            return data;
        },

        /**
         * Getting child items
         *
         * @inheritDoc
         */
        getChildItems: function (data, page) {
            var dataRecord = data || this.relatedData,
                startIndex;

            this.startIndex = (~~this.currentPage() - 1) * this.pageSize;

            startIndex = page || this.startIndex;

            return dataRecord.slice(startIndex, this.startIndex + parseFloat(this.pageSize));
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
