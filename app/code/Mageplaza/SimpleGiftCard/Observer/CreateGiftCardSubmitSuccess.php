<?php

namespace Mageplaza\SimpleGiftCard\Observer;


class CreateGiftCardSubmitSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $_scopeConfig;

    protected $_giftCardFactory;

    protected $_giftCardHistoryFactory;

    protected $_customerSession;

    protected $_redirect;

    protected $_productFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory        $giftCardFactory,
        \Mageplaza\SimpleGiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session                        $customerSession,
        \Magento\Framework\App\Response\RedirectInterface      $redirect,
        \Magento\Catalog\Model\ProductFactory                  $productFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_customerSession = $customerSession;
        $this->_redirect = $redirect;
        $this->_productFactory = $productFactory;
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
        if ($currentCustomer) {
            $orderID = $observer->getData('order')->getIncrementId();
            $customerID = $observer->getData('order')->getCustomerId();
            $create_from = $orderID;
            $amount_used = 0;
            $allAttributeProductOrdered = $observer->getData('order')->getAllVisibleItems();
            foreach ($allAttributeProductOrdered as $item) {
                $isVirtual = $item->getIsVirtual();

                if ($isVirtual == 1) {
//                    $productId = $item->getProductId();
//                    $product = $this->_productFactory->create()->load($productId);
//                    $giftCardAmount = $product->getData('gift_card_amount');

                    $product = $item->getProduct();
                    $giftCardAmount = $product->getData('gift_card_amount');

                    if ($giftCardAmount) {
                        $balance = $giftCardAmount;
                        $quantityOrdered = $item->getQtyordered();
                        for ($i = 0; $i < $quantityOrdered; $i++) {
                            try {
                                $codeGiftCard = $this->createCodeGiftCard();
                                $modelGiftCard = $this->_giftCardFactory->create();
                                $dataGiftCard = [
                                    'code' => $codeGiftCard,
                                    'balance' => $balance,
                                    'amount_used' => $amount_used,
                                    'create_from' => $create_from,
                                ];
                                $modelGiftCard->addData($dataGiftCard);
                                $modelGiftCard->save();

                                $lastInsertedIdGiftCard = $modelGiftCard->getId();
                                $currentCustomerId = $customerID;
                                $action = "Create $orderID";
                                $amountCurrent = $giftCardAmount;

                                $modelGiftCardHistory = $this->_giftCardHistoryFactory->create();
                                $dataGiftCardHistory = [
                                    'giftcard_id' => $lastInsertedIdGiftCard,
                                    'customer_id' => $currentCustomerId,
                                    'amount' => $amountCurrent,
                                    'action' => $action,
                                ];
                                $modelGiftCardHistory->addData($dataGiftCardHistory);
                                $modelGiftCardHistory->save();
                            } catch (\Exception $e) {
                                return $e . getMessage();
                            }
                        }
                    }

                }

            }
            return $this;
        }
    }
}
