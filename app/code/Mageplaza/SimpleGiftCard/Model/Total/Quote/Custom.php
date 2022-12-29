<?php

namespace Mageplaza\SimpleGiftCard\Model\Total\Quote;
/**
 * Class Custom
 * @package Mageplaza\SimpleGiftCard\Model\Total\Quote
 */
class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    protected $_giftCardFactory;

//    protected $_quoteFactory;

//    protected $_cart;

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory   $giftCardFactory,
//        \Magento\Checkout\Controller\Cart                 $cart
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->_giftCardFactory = $giftCardFactory;
//        $this->_cart = $cart;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */

// Version 1
    public function collect(
        \Magento\Quote\Model\Quote                          $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total            $total
    )
    {
        //Fix for discount applied twice
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }
        parent::collect($quote, $shippingAssignment, $total);
        /*
         * subtotal, discount_amount, base_discount_amount, discount_description, subtotal_with_discount
         */
//        $couponCode = $quote->getCouponCode();
        $couponCode = $quote->getCouponCodeCustom();

        $giftCard = $this->_giftCardFactory->create()->load($couponCode, 'code');
        if ($giftCard->getId()) {
            $amountEnabling = $giftCard->getBalance() - $giftCard->getAmountUsed();
            $baseDiscountAmountApply = (($total->getSubtotal() - $amountEnabling) < 0) ? $total->getSubtotal() : $amountEnabling;
            $discountAmount = $this->_priceCurrency->convert($baseDiscountAmountApply);

            $total->setData('coupon_code_custom', -$discountAmount);

            $total->addTotalAmount('customdiscount', -$discountAmount);
            $total->addBaseTotalAmount('customdiscount', -$baseDiscountAmountApply);

//            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscountAmountApply);
            $total->setBaseGrandTotal($total->getBaseSubtotal() - $baseDiscountAmountApply);

//            dd($total);
            $quote->setCustomDiscount(-$discountAmount);
//            $quote->setBaseGrandTotal($total->getBaseGrandTotal());
//            dd($quote);
            return $this;


            /*//            Dua vao core magento
                        $amountEnabling = $giftCard->getBalance() - $giftCard->getAmountUsed();
                        $baseDiscountAmountApply = (($total->getSubtotal() - $amountEnabling) < 0) ? $total->getSubtotal() : $amountEnabling;
                        $discountAmount = $this->_priceCurrency->convert($baseDiscountAmountApply);
                        $subTotalWithDiscount = $total->getSubtotal() - $discountAmount;
                        $baseSubTotalWithDiscount = $total->getBaseSubtotal() - $baseDiscountAmountApply;
                        $label = 'Coupon Code Custom';
                        $total->setDiscountDescription($label);
                        $total->setDiscountAmount($discountAmount);
                        $total->setBaseDiscountAmount($baseDiscountAmountApply);
                        $total->setSubtotalWithDiscount(($subTotalWithDiscount < 0) ? 0 : $subTotalWithDiscount);
                        $total->setBaseSubtotalWithDiscount(($baseSubTotalWithDiscount < 0) ? 0 : $baseSubTotalWithDiscount);
                        $total->setTotalAmount('discount', -$discountAmount);
                        $total->setBaseTotalAmount('discount', -$baseDiscountAmountApply);
                        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscountAmountApply);
                        $quote->setCustomDiscount(-$discountAmount);
                        return $this;*/
        }
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;

        $amount = $total->getData('coupon_code_custom');
//        dd($this->_cart);

//        $baseSubTotal = $quote->getBaseSubtotal();
//        $grandTotal = $quote->getGrandTotal();
//        $amount = $baseSubTotal - $grandTotal;
//        dd($quote);
        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => 'mageplaza_coupon_code_custom',
                'title' => $description && strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
