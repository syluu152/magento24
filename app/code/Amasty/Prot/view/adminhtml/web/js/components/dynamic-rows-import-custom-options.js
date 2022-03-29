/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
    'uiRegistry',
    'underscore'
], function (DynamicRows, registry, _) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            mappingSettings: {
                enabled: false,
                distinct: false
            },
            update: true,
            map: {
                'option_id': 'option_id'
            },
            identificationProperty: 'option_id',
            identificationDRProperty: 'option_id',
            dependencyComponentIndex: 'dependency',
            allowedOptionTypes: ['drop_down', 'multiple', 'radio', 'checkbox']
        },

        /**
         * Check for specific value of element.type
         *
         * @returns {Boolean}
         */
        isTypeAllowed: function (array, value) {
            return array.some(function (element) {
               return value.indexOf(element.type) > -1;
            });
        },

        /**
         * Show or hide "make dependency" components depending on allowed option types
         *
         * @inheritdoc
         */
        setToInsertData: function () {
            var self = this,
                recordData = this.recordData(),
                isVisible,
                components = registry.filter(
                  'ns=' + this.ns +
                  ', parentScope=' + this.dataScope + '.options' +
                  ', index=' + this.dependencyComponentIndex
                );

            if (recordData.length > 1) {
                self.isTypeAllowed(recordData, self.allowedOptionTypes) ? isVisible = true : isVisible = false;
            } else {
                isVisible = false
            }

            _.each(components, function (component) {
                component.visible(isVisible);
            });

            this._super();
        }
    });
});
