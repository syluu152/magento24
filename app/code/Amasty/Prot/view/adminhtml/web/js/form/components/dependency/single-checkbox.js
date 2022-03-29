/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/single-checkbox'
], function (_, singleCheckbox) {
    return singleCheckbox.extend({
        initialize: function () {
            this._super();

            this.dependencyChanged(
                this.source.get(this.dataScope + '.dependency')
            );

            return this;
        },

        /**
         * Disable or enable required checkbox for custom option depend on count corresponding DEPENDENCY
         * @param dependency
         */
        dependencyChanged: function (dependency) {
            this.disabled(dependency && dependency.length);
        }
    });
});
