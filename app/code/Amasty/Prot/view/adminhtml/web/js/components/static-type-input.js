/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'uiRegistry',
    'Magento_Catalog/component/static-type-input'
], function (registry, staticType) {
    'use strict';

    return staticType.extend({
        initLinkToParent: function () {
            var pathToParent = this.parentName.replace(/(\.[^.]*){3}$/, '');

            this.parentOption = registry.async(pathToParent);
            this.value() && this.parentOption('label', this.value());

            return this;
        },
    });
});
