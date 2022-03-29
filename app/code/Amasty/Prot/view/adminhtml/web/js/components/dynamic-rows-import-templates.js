
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
    'underscore',
    'mageUtils'
], function (DynamicRows, _, utils) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            mappingSettings: {
                enabled: false,
                distinct: false
            },
            update: true,
            map: {
                'template_id': 'template_id'
            },
            identificationProperty: 'template_id',
            identificationDRProperty: 'template_id'
        },

        /**
         * Removes option_id and option_type_id from every option
         *
         * @param {Array} options
         */
        removeOptionsIds: function (options) {
            _.each(options, function (optionValue) {
                delete optionValue['option_id'];
                delete optionValue['option_type_id'];
            });
        },

        /** @inheritdoc */
        processingAddChild: function (ctx, index, prop) {
            if (!ctx) {
                this.showSpinner(true);
                this.addChild(ctx, index, prop);

                return;
            }

            this._super(ctx, index, prop);
        },

        /**
         * Set empty array to dataProvider
         */
        clearDataProvider: function () {
            this.source.set(this.dataProvider, []);
        },

        /**
         * Mutes parent method
         */
        updateInsertData: function () {
            return false;
        }
    });
});
