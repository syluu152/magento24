/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * Default Options Dependency Module logic
 * @return widget
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal'
], function ($, _) {
    'use strict';

    $.widget('mage.amprotDependency', {
        options: {
            dependencyMap: {},
            optionSelector: ['[data-option-id="', '"]'],
            parentFieldSelector: '[data-amprot-js="field"]'
        },

        /**
         * Create Amasty Options Dependency Widget
         * @private
         */
        _create: function () {
            this._initializeTrack();
        },

        _initializeTrack: function () {
            $.each(this.options.dependencyMap, function (optionTo, optionsFrom) {
                $.each(optionsFrom, function (optionFrom) {
                    var optionToElement = $(this._getOptionSelectorById(optionTo)).closest(
                        this.options.parentFieldSelector
                    );
                    $(this._getOptionSelectorById(optionFrom)).on('change', this.trackChange.bind(
                        this,
                        optionToElement,
                        optionsFrom
                    ));
                    this.trackChange(optionToElement, optionsFrom);
                }.bind(this));
            }.bind(this));
        },

        trackChange: function (optionTo, optionsFrom) {
            var shouldShow = true,
                self = this;

            $.each(optionsFrom, function (optionFrom, optionData) {
                var value = self._getOptionValues(optionFrom, optionData),
                    diff = _.intersection(value, optionData.values);

                if (!diff.length) {
                    shouldShow = false;
                }
            });

            if (shouldShow) {
                optionTo.removeClass('amprot-dependent-field').show();
            } else {
                if (!optionTo.hasClass('amprot-dependent-field')) {
                    this._clearElement(optionTo);
                    optionTo.hide();
                }
            }
        },

        _clearElement: function (optionTo) {
            optionTo.find('[name]:not([type="radio"]):not([type="checkbox"])').val('')
                .trigger('change');

            optionTo.find('[name][type="radio"], [type="checkbox"]')
                .prop('checked', false)
                .prop('selected', false)
                .trigger('change');

            optionTo.find('.amprot-option:not([data-amprot-value])').trigger('click');
            optionTo.find('.amprot-radio-block .amprot-input[value=""]').trigger('click');

            optionTo.find('.amprot-option').removeClass('-selected');
            optionTo.find('.amprot-tags-block .amprot-option').remove();
        },

        _getOptionValues: function (optionFrom, optionData) {
            var value = [],
                optionSelector = this._getOptionSelectorById(optionFrom);
            switch (optionData.type) {
                case 'drop_down':
                case 'multiple':
                    var optionFromElement = $(optionSelector);
                    value =  optionFromElement.val()
                        ? optionFromElement.val()
                        : [];
                    break;
                case 'radio':
                case 'checkbox':
                    $(optionSelector + ':checked').each(function (index, optionFromElement) {
                        value.push($(optionFromElement).val());
                    });
                    break;
            }

            return this._prepareArray(value);
        },

        _getOptionSelectorById: function (optionId) {
            return this.options.optionSelector.join(optionId);
         },

        _prepareArray: function (value) {
            value = value instanceof Array ? value : [value];
            return value.map(function (value) {
                return value.toString();
            });
        }
    });

    return $.mage.amprotDependency;
});
