<?php

namespace Mageplaza\SimpleGiftCard\Plugin;


class CheckoutCouponApply
{

    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $coupon, $result)
    {
//        dd($coupon->getQuote());
        return $coupon->getQuote()->getCouponCode();
    }
}
