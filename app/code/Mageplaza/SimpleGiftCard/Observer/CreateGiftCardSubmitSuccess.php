<?php

namespace Mageplaza\SimpleGiftCard\Observer;


class CreateGiftCardSubmitSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $_scopeConfig;

    protected $_giftCardFactory;

    protected $_giftCardHistoryFactory;

    protected $_customerSession;

    protected $_redirect;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory        $giftCardFactory,
        \Mageplaza\SimpleGiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session                        $customerSession,
        \Magento\Framework\App\Response\RedirectInterface      $redirect
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_customerSession = $customerSession;
        $this->_redirect = $redirect;
    }

    public function createCodeGiftCard()
    {
        $codeLength = $this->_scopeConfig->getValue(
            'gift_card/code/code_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        $characters = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < $codeLength; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public
    function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentCustomer = $this->_customerSession->isLoggedIn();
        if (!$currentCustomer) {
//            /** @var \Magento\Framework\App\Action\Action $controller */
//            $controller = $observer->getControllerAction();
//            return $this->_redirect->redirect($controller->getResponse(), 'customer/account/login');
            return $this;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderID = $observer->getData('order')['increment_id'];
            $customerID = $observer->getData('order')['customer_id'];
            $totalQuantityOrdered = $observer->getData('order')['total_qty_ordered'];

            $allAttributeProductOrdered = $observer->getData('order')->getAllVisibleItems();
            $giftCardAmount = '';
            foreach ($allAttributeProductOrdered as $item) {
                $productId = $item->getProductId();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                $giftCardAmount = $product->getData('gift_card_amount');
            }

            $create_from = $orderID;
            $balance = $giftCardAmount;
            $amount_used = 0;

            for ($i = 0; $i < $totalQuantityOrdered; $i++) {
                try {
                    $codeGiftCard = $this->createCodeGiftCard();
                    $modelGiftCard = $this->_giftCardFactory->create();
                    $modelGiftCard->setData('code', $codeGiftCard);
                    $modelGiftCard->setData('balance', $balance);
                    $modelGiftCard->setData('amount_used', $amount_used);
                    $modelGiftCard->setData('create_from', $create_from);
                    $modelGiftCard->save();

                    $lastInsrtedIdGiftCard = $modelGiftCard->getId();
                    $currentCustomerId = $customerID;
                    $action = "Create";
                    $amountCurrent = $giftCardAmount;
                    $modelGiftCardHistory = $this->_giftCardHistoryFactory->create();
                    $modelGiftCardHistory->setData('giftcard_id', $lastInsrtedIdGiftCard);
                    $modelGiftCardHistory->setData('customer_id', $currentCustomerId);
                    $modelGiftCardHistory->setData('amount', $amountCurrent);
                    $modelGiftCardHistory->setData('action', $action);
                    $modelGiftCardHistory->save();
                } catch (\Exception $e) {
                    return $e . getMessage();
                }
            }
            return $this;
        }
    }
}
