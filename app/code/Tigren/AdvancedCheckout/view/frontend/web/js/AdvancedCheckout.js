/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>.All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'Magento_Customer/js/customer-data'
], function ($, modal, urlBuilder, customerData) {

    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: 'Pop-up title',
        buttons: [
            {
                text: $.mage.__('Close'),
                class: 'modal-close',
                click: function () {
                    this.closeModal();
                }
            }]
    };
    modal(options, $('#modal-content'));

    $('#product-addtocart-button').on('click', function () {
        var id_product = $('input[name="product"]').val();
        var submitting = JSON.parse(localStorage.getItem('submitting'));
        if (submitting === null) {
            localStorage.setItem('submitting', 1);
            getMultiAllow(id_product);
            $(this).prop('disabled', true);
        } else {
            alert('Submitting, please wait');
        }
        return false;
    });

    $('#go-to-checkout').on('click', function () {
        window.location.href = urlBuilder.build('checkout');
    });

    $('#clear-cart').on('click', function () {
        var url = urlBuilder.build('advanced_front/advanced/ClearCart');
        var sections = ['cart'];

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'text',
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                if (response != 'error') {
                    customerData.invalidate(sections);
                    $('#loader').hide();
                    location.reload();
                }
            }
        });
    });

    function getMultiAllow(id_product) {
        var url = urlBuilder.build('advanced_front/advanced/CheckMultiAllow');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'text',
            data: {
                id_item: id_product
            }
        }).done(function (result) {
            var localStore = JSON.parse(localStorage.getItem('mage-cache-storage'));

            var countItem;
            if (typeof (localStore['cart']) == 'undefined') {
                countItem = 1;
            } else {
                countItem = localStore['cart']['summary_count'];
            }

            if (result == '1') {
                $('#product_addtocart_form').submit().ajaxSuccess(function () {
                    clearSubmitting();
                });
            } else {
                if (!countItem) {
                    $('#product_addtocart_form').submit().ajaxSuccess(function () {
                        clearSubmitting();
                    });
                } else {
                    $('#modal-content').modal('openModal');
                    clearSubmitting();
                }
            }
            $('#product-addtocart-button').prop('disabled', false);
        });
    }

    function clearSubmitting() {
        var submitting = JSON.parse(localStorage.getItem('submitting'));
        if (submitting !== null) {
            localStorage.removeItem('submitting');
            console.log('cleared');
        }
    }
});
