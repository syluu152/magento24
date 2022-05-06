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

    $('#product-addtocart-button').on('click', function (e) {
        e.preventDefault();
        //pause button add to cart
        var id_product = $('input[name="product"]').val();
        var url = urlBuilder.build('advanced_front/Advanced/CheckMultiAllow');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                id_item: id_product
            },
            success: function (response) {
                // if (response && response.attributeValue == 0) {
                //     if (response.isCartEmpty === false) {
                //         $('#modal-content').modal('openModal');
                //     } else {
                //         jQuery('form[id="product_addtocart_form"]').submit();
                //     }
                // } else {
                //     jQuery('form[id="product_addtocart_form"]').submit();
                // }
                if (response && response.attributeValue == 0 && response.isCartEmpty === false) {
                    $('#modal-content').modal('openModal');
                } else {
                    $('form[id="product_addtocart_form"]').submit();
                    //submit form
                }
            }
        });
    });

    $('#go-to-checkout').on('click', function () {
        window.location.href = urlBuilder.build('checkout');
    });

    $('#clear-cart').on('click', function () {
        var url = urlBuilder.build('advanced_front/Advanced/ClearCart');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('#loader').show();
            },
            success: function (response) {
                if (response && response.success === true) {
                    //update minicart
                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    customerData.reload(sections, true);
                    //end update minicart
                    location.reload();
                }
            }
        });
    });
});
