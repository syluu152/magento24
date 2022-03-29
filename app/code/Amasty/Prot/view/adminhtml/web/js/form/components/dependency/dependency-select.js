/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    'jquery',
    'underscore',
    './ui-select',
    'uiRegistry',
    'mage/translate',
], function ($, _, uiSelect, uiRegistry) {

    return uiSelect.extend({
        defaults: {
            optionValues: [],
            dependencyValueName: '',
            selectTypes: ['drop_down', 'radio', 'checkbox', 'multiple'],
            optionScope: '',
            imports: {
                options: '${ $.name.replace(/container_template.options(.*)/,"container_template.options") }:recordData'
            },
            listens: {
                value: 'valueChanged'
            },
            componentCustomOptions: {
                disabledClass: '_disabled',
                disabledOptionText: $.mage.__('There are no options available')
            },
        },

        initialize: function () {
            this.initialLoad = true;

            this._super();

            this.optionScope = this._getOptionScope();

            this._disableDependencyValue();

            this.initialLoad = false;

            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(['optionValues']);
            return this;
        },

        /**
         * Rewrite for first doing imports and then doing exports
         *
         * Initializes links between properties.
         *
         * @returns {Element} Chainbale.
         */
        initLinks: function () {
            return this.setListeners(this.listens)
                .setLinks(this.links, 'imports')
                .setLinks(this.links, 'exports')
                .setLinks(this.imports, 'imports')
                .setLinks(this.exports, 'exports');
        },

        /**
         * Additional observe for recordData(custom options) import
         * @param path
         * @param value
         */
        set: function (path, value) {
            if (path === 'options') {
                value = this._convertOptionsData(value);
            }

            this._super(path, value);

            if (path === 'options') {
                this._updateValuesComponent();
            }
        },

        /**
         * If value changed in dependency option dropdown, need clear value in dependency value dropdown
         */
        valueChanged: function () {
            if (!this.initialLoad) {
                uiRegistry.get(this.dependencyValueName).value('').disabled(false);
            }
        },

        /**
         * Convert recordData options in format [{value:'',label:''}]
         */
        _convertOptionsData: function (recordData) {
            var values = [];
            _.forEach(recordData, function (option) {
                if (this.selectTypes.indexOf(option.type) !== -1) {
                    var value = option.template_option_id
                        ? 'id_' + option.template_option_id
                        : 'order_' + option.sort_order;
                    if (value !== this._getCurrentOptionValue()) {
                        values.push({
                            label: option.title,
                            value: value,
                            values: option.values
                        });
                    }
                }
            }, this);

            return this._emptyDependencyCheck(values);
        },

        /**
         * Render disabled option if there is no available dependency
         */
        _emptyDependencyCheck: function (values) {
            var options = this.componentCustomOptions;

            if (Array.isArray(values) && values.length) {
                uiRegistry.get(this, function (item) {
                    if (item.rootList) {
                        item.rootList.classList.remove(options.disabledClass);
                    }
                });

                return values;
            } else  {
                uiRegistry.get(this, function (item) {
                    if (item.rootList) {
                        item.rootList.classList.add(options.disabledClass);
                    }
                });

                return [{
                    label: options.disabledOptionText,
                    value: 0
                }]
            }
        },

        /**
         * Update corresponding select with custom option values. Dependent on current selected custom option
         */
        _updateValuesComponent: function () {
            if (typeof this.getSelected()[0] != 'undefined') {
                var values = [];
                _.forEach(this.getSelected()[0].values, function (optionValue) {
                    var value = optionValue.template_option_value_id
                        ? 'id_' + optionValue.template_option_value_id
                        : 'order_' + optionValue.sort_order;
                    values.push({
                        label: optionValue.title,
                        value: value
                    });
                });
                this.optionValues(values);
            }
        },

        /**
         * Getting parent option identifier
         */
        _getCurrentOptionValue: function () {
            if (!this.optionScope) {
                this.optionScope = this._getOptionScope();
            }

            return this.source.get(this.optionScope + '.template_option_id')
                ? 'id_' + this.source.get(this.optionScope + '.template_option_id')
                : 'order_' + this.source.get(this.optionScope + '.sort_order')
        },

        /**
         * Getting data scope of parent option
         */
        _getOptionScope: function () {
            return this.dataScope.replace(/(options.\d+).*/, '$1');
        },

        /**
         * Disable dependency option value dropdown if it has no options
         */
        _disableDependencyValue: function () {
            uiRegistry.get(this.dependencyValueName, function (item) {
                if (!item.options().length) {
                    item.disabled(true);
                } else {
                    // Fix empty placeholder after removing node with existing value
                    item.value(item.value());
                }
            });
        }
    });
});
