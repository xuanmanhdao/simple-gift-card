<?php

namespace Mageplaza\SimpleGiftCard\Observer;

use Magento\Framework\Event\Observer;

class CheckShoppingCartObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
    )
    {
        $this->_checkoutSession = $checkoutSession;
    }


    public function execute(Observer $observer)
    {
//        dd($observer);
//        dd($quote);
        $quote=$this->_checkoutSession->getQuote();
        $quote->collectTotals();

//        dd($quote->collectTotals());
    }
}
