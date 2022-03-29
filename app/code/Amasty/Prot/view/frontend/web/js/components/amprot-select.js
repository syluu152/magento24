/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

/**
 * Default Select Module logic
 * @return widget
 */

define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('mage.amprotSelect', {
        options: {
            id: null,
            type: null,
            activeClass: '-active',
            selectedClass: '-selected',
            blockSelector: '[data-amprot-js="select"]',
            optionsSelector: '[data-amprot-js="options"]',
            placeholderSelector: '[data-amprot-js="placeholder"]',
            inputSelector: '[data-amprot-js="input"]',
            itemSelector: '[data-amprot-js="item"]',
            selectOriginSelector: '[data-amprot-js="select-origin_"]',
            tagsSelector: '[data-amprot-js="tags"]',
        },

        /**
         * Create Amasty Select Widget
         * @private
         */
        _create: function () {
            this._initListeners();
        },

        /**
         * Set event listeners
         * @private
         */
        _initListeners: function () {
            var self = this,
                options = self.options;

            this.element.find(options.placeholderSelector).on('click', function () {
                switch (options.type) {
                    case 'multiple':
                        self._toggle(!self.element.hasClass(options.activeClass));
                        break;
                    case 'default':
                        self._toggle();
                        break;
                }
            });

            this.element.find(options.optionsSelector).on('click', function(event) {
                switch (options.type) {
                    case 'multiple':
                        self._multiSelectOptionToggle(this, event);
                        self._changeSelect(self.element);
                        self._generateTag(self.element);
                        break;
                    case 'default':
                        self._selectOption(this, event);
                        self._toggle(false);
                        break;
                }
            });

            this.element.next(options.tagsSelector).on('click', function (event) {
                if (event.target.type === 'button') {
                    self._removeTag(event.target.parentNode);
                }
            });

            $(document).on('click', function(event) {
                self._hideGlobal(event);
            });
        },

        /**
         * Toggle select options,
         * using _changeSelect method to set a value into an origin select
         * @private
         */
        _selectOption: function (element, event) {
            var options = this.options,
                text = '';

            $(element).children()
                      .not(event.target)
                      .removeClass(options.selectedClass);

            text = $(element).find(event.target)
                             .addClass(options.selectedClass)
                             .html();

            this.element.find(options.placeholderSelector)
                        .addClass(options.selectedClass)
                        .html(text);

            this._changeSelect($(element));
        },

        /**
         * Toggle selected class of the multiSelect options
         * @private
         */
        _multiSelectOptionToggle: function (element, event) {
            $(element).find(event.target).toggleClass(this.options.selectedClass);
        },

        /**
         * Get element data-value attribute
         * @returns {Array}
         */
        getValue: function (element) {
            var result = [];

            element.each(function (index, item) {
                result.push(item.dataset.amprotValue);
            });

            return result;
        },

        /**
         * Set an option value into the origin select
         * @private
         */
        _changeSelect: function (element) {
            var options = this.options,
                selector = options.selectOriginSelector.slice(0, -2) + options.id + options.selectOriginSelector.slice(-2);

            $(selector).val(this.getValue(element.find('.' + options.selectedClass))).trigger('change');
        },

        /**
         * Toggle element active class
         * @private
         */
        _toggle: function (state) {
            this.element.toggleClass(this.options.activeClass, state);
        },

        /**
         * Clone selected options and make them as tags
         * @private
         */
        _generateTag: function (element) {
            var options = this.options;

            options.tagsBlock = this.element.siblings(options.tagsSelector);
            this._clearHtml(options.tagsBlock);

            element.find('.' + options.selectedClass).each(function () {
                options.tagsBlock.append($(this)
                                 .clone()
                                 .append($('<button>', {
                                     class: 'amprot-close-button',
                                     type: 'button'
                                 })));
            });
        },

        /**
         * Remove tag by value from DOM and trigger changeSelect method
         * @private
         */
        _removeTag: function (element) {
            var value = element.dataset.amprotValue;

            this.element.find('[data-amprot-value="' + value + '"]').removeClass(this.options.selectedClass);
            this._changeSelect(this.element);
            $(element).remove();
        },

        /**
         * Clear element inner html
         * @private
         */
        _clearHtml: function (element) {
            element.html('');
        },

        /**
         * Remove active class from all elements except the target element
         * @private
         */
        _hideGlobal: function (event) {
            this.element.not($(event.target)
                        .closest(this.options.blockSelector))
                        .removeClass(this.options.activeClass);
        }
    });

    return $.mage.amprotSelect;
});
