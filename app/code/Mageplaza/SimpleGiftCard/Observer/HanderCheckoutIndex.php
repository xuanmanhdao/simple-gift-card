<?php

namespace Mageplaza\SimpleGiftCard\Observer;

use Magento\Framework\Event\Observer;

class HanderCheckoutIndex implements \Magento\Framework\Event\ObserverInterface
{
    protected $_checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session          $checkoutSession,
    )
    {
        $this->_checkoutSession = $checkoutSession;
    }

    public function execute(Observer $observer)
    {
//        $quote = $this->_checkoutSession->getQuote();
//        $quote->collectTotals();
//        dd($quote);
    }
}
