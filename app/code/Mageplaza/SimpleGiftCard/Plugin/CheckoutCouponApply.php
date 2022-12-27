<?php

namespace Mageplaza\SimpleGiftCard\Plugin;


class CheckoutCouponApply
{
    protected $_quoteFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
    )
    {
        $this->_quoteFactory = $quoteFactory;
    }

    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $coupon, $result)
    {

        $valueCouponCodeQuote = $coupon->getQuote()->getCouponCode();
        if (!$valueCouponCodeQuote) {
            $quoteID = $coupon->getQuote()->getId();
            $modelQuote = $this->_quoteFactory->create()->load($quoteID);
            $couponCode = $modelQuote->getCouponCodeCustom();
            return $couponCode;
        }
//        dd($valueCouponCodeQuote);
        return $valueCouponCodeQuote;
    }
}
