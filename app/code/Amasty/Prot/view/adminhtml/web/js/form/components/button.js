
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'underscore'
], function (Button, registry, layout, utils, _) {
    'use strict';

    return Button.extend({
        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            var targetName = action.targetName,
                params = utils.copy(action.params) || [],
                actionName = action.actionName,
                target;

            if (!registry.has(targetName)) {
                targetName = this.getFromParent(targetName);
            }
            if (!registry.has(targetName)) {
                this.getFromTemplate(targetName);
            }
            target = registry.async(targetName);

            if (target && typeof target === 'function' && actionName) {
                params.unshift(actionName);
                target.apply(target, params);
            }
        },

        getFromParent: function (targetName) {
            var parentName = targetName.split('.'),
                index = parentName.pop(),
                parent;

            parent = registry.get(this.parentName);

            return parent.parentName + '.' + index;
        }
    });
});
