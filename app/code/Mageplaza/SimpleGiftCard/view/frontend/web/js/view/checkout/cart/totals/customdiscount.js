define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, Component, quote, priceUtils) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mageplaza_SimpleGiftCard/checkout/cart/totals/customdiscount'
            },
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },
            getCustomDiscount: function () {
                var total = quote;
                var discountAmount = 0;
                $.each(total.totals().total_segments, function (index, value) {
                    if (value.code === 'mageplaza_coupon_code_custom') {
                        discountAmount = value.value;
                    }
                });
                return this.getFormattedPrice(discountAmount);
            },
            isDisplayedCustomdiscount: function () {
                if (this.getCustomDiscount() === this.getFormattedPrice(0)) {
                    return false;
                }
                return true;
            },
            getHTML: function () {
                return '<h2>Hello1</h2>';
            }
        });
    }
);
