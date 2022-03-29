/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'Magento_Ui/js/form/provider'
], function ($, Element) {
    'use strict';

    return Element.extend({
        /**
         * Saves currently available data.
         *
         * @param {Object} [options] - Addtitional request options.
         * @returns {Provider} Chainable.
         */
        save: function (options) {
            var data = this.get('data');

            /* delete unused data */
            delete data.parent_ids;
            delete data.child_products_container;

            var newParent = [];
            $(data.products_list_container).each(function (i, item) {
                newParent[i] = {'entity_id' : item.entity_id} // remove other data
            });
            data.products_list_container = newParent;

            this.client.save(data, options);

            return this;
        }
    });
});
