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

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory   $giftCardFactory,
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->_giftCardFactory = $giftCardFactory;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|bool
     */

    /*    public function collect(
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
            $label = 'My Custom Discount';
            $discountAmount = -10;
            $appliedCartDiscount = 0;
            if ($total->getDiscountDescription()) {
                // If a discount exists in cart and another discount is applied, the add both discounts.
                $appliedCartDiscount = $total->getDiscountAmount();
                $discountAmount = $total->getDiscountAmount() + $discountAmount;
                $label = $total->getDiscountDescription() . ', ' . $label;
            }

            $total->setDiscountDescription($label);
            $total->setDiscountAmount($discountAmount);
            $total->setBaseDiscountAmount($discountAmount);
            $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
            $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);

            if (isset($appliedCartDiscount)) {
                dd($this);
                $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
            } else {
                $total->addTotalAmount($this->getCode(), $discountAmount);
                $total->addBaseTotalAmount($this->getCode(), $discountAmount);
            }

            return $this;
        }*/


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

            $subTotalWithDiscount = $total->getSubtotal() - $discountAmount;
            $baseSubTotalWithDiscount = $total->getBaseSubtotal() - $baseDiscountAmountApply;

            $label = 'My Custom Discount Of ManhBauTroi';
            $total->setDiscountDescription($label);
            $total->setDiscountAmount($discountAmount);
            $total->setBaseDiscountAmount($baseDiscountAmountApply);
            $total->setSubtotalWithDiscount(($subTotalWithDiscount < 0) ? 0 : $subTotalWithDiscount);
            $total->setBaseSubtotalWithDiscount(($baseSubTotalWithDiscount < 0) ? 0 : $baseSubTotalWithDiscount);

//            $total->addTotalAmount($this->getCode(), -$discountAmount);
//            $total->addBaseTotalAmount($this->getCode(), -$discountAmount);

//            $total->addTotalAmount('customdiscount', -$discountAmount);
//            $total->addBaseTotalAmount('customdiscount', -$discountAmount);

            $total->setTotalAmount('discount', -$discountAmount);
            $total->setBaseTotalAmount('discount', -$baseDiscountAmountApply);

//            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscountAmountApply);
            $quote->setCustomDiscount(-$discountAmount);
            return $this;
        }
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();
//        dd($amount);

        // ONLY return 1 discount. Need to append existing
        //see app/code/Magento/Quote/Model/Quote/Address.php

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
