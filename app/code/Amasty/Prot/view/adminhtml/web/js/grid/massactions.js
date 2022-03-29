
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'underscore',
    'Magento_Ui/js/grid/massactions',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/lib/collapsible',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (_, Massactions, registry, utils, Collapsible, confirm, alert, $t) {
    'use strict';

    return Massactions.extend({
         /**
          * Default action callback. Sends selections data
          * via POST request.
          *
          * @param {Object} action - Action data.
          * @param {Object} data - Selections data.
          */
        defaultCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            _.extend(selections, data.params || {});

            if (action.type && action.type.indexOf('amasty') === 0) {
                selections['action'] = action.type;
            }

            utils.submit({
                url: action.url,
                data: selections
            });
        },

        applyMassaction: function (parent, action) {
            var data = this.getSelections(),
                callback;

            action = this.getAction(action.type);

            try {
                var pactionElement = jQuery(event.target).parents('.amasty-paction-form').find('input, select');
            } catch (ex) {
                pactionElement = null;
            }

            if (!pactionElement || !pactionElement.length) {
                pactionElement = jQuery('.amasty-paction-form:visible').find('input, select');
            }

            var value = pactionElement.length? pactionElement[0].value : null;
            if (!value && pactionElement.length > 1) {
                value = pactionElement[1].value;
            }

            if (!value && pactionElement.length) {
                alert({
                    content: 'Required field is empty.'
                });

                return this;
            }

            if (!data.total) {
                alert({
                    content: this.noItemsMsg
                });

                return this;
            }

            var me = this;
            callback = function () {
                me.massactionCallback(action, data, value)
            };

            action.confirm
                ? this._confirm(action, callback)
                : callback();
        },

        massactionCallback: function (action, data, value) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];
            selections['amasty_paction_field'] = value;
            selections['action'] = action.type;

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            _.extend(selections, data.params || {});

            utils.submit({
                url: action.url,
                data: selections
            });
        }
    });
});
