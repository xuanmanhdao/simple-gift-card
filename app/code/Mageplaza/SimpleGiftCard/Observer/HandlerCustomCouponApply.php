<?php

namespace Mageplaza\SimpleGiftCard\Observer;

use Magento\Framework\Event\Observer;

class HandlerCustomCouponApply implements \Magento\Framework\Event\ObserverInterface
{
    protected $_giftCardFactory;

    protected $_checkoutSession;

    protected $_objectManager;

    protected $_messageManager;

    protected $_cart;

    protected $_resultRedirectFactory;

    public function __construct(
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory      $giftCardFactory,
        \Magento\Checkout\Model\Session                      $checkoutSession,
        \Magento\Framework\ObjectManagerInterface            $objectManager,
        \Magento\Framework\Message\ManagerInterface          $messageManager,
        \Magento\Checkout\Model\Cart                         $cart,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory

    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
        $this->_cart = $cart;
        $this->_resultRedirectFactory = $redirectFactory;
    }

    public function checkIsCodeCustomOrNot($valueCode): bool
    {
        $result = false;
        $giftCard = $this->_giftCardFactory->create()->load($valueCode, 'code');
        if ($giftCard->getId()) {
            $result = true;
        }
        return $result;
    }

    public function execute(Observer $observer)
    {
        $valueCodeCustomerApply = $observer->getControllerAction()->getRequest()->getParam('coupon_code');
        if ($this->checkIsCodeCustomOrNot($valueCodeCustomerApply)) {
            $cartQuote = $this->_cart->getQuote();
            $oldCouponCode = $cartQuote->getCouponCode() ?? '';

            $codeLength = strlen($valueCodeCustomerApply);
            if (!$codeLength && !strlen($oldCouponCode)) {
                dd(123);
//                return $this->_goBack();
            }
            $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
            $this->_checkoutSession->getQuote()->setCouponCode($valueCodeCustomerApply)->save();
            $this->_messageManager->addSuccessMessage(
                __(
                    'You used coupon code "%1".',
                    $escaper->escapeHtml($valueCodeCustomerApply)
                )
            );
            return $this;
        }
    }
}
