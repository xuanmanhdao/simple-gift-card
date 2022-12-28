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

//    protected $_checkoutSession;
    private $_helperEmail;

    protected $_quoteFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory        $giftCardFactory,
        \Mageplaza\SimpleGiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session                        $customerSession,
        \Magento\Framework\App\Response\RedirectInterface      $redirect,
        \Magento\Catalog\Model\ProductFactory                  $productFactory,
//        \Magento\Checkout\Model\Session                        $checkoutSession,
        \Magento\Quote\Model\QuoteFactory                      $quoteFactory,
        \Mageplaza\SimpleGiftCard\Helper\Email                 $helperEmail,

    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_customerSession = $customerSession;
        $this->_redirect = $redirect;
        $this->_productFactory = $productFactory;
//        $this->_checkoutSession = $checkoutSession;
        $this->_quoteFactory = $quoteFactory;
        $this->_helperEmail = $helperEmail;
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

    public function checkCodeOfMeOrNotAndUpdateAmountUsed($quoteID, $discountAmount): bool
    {
        $result = false;
        try {
            $modelQuote = $this->_quoteFactory->create()->load($quoteID);
            $valueCouponCodeCustom = $modelQuote->getCouponCodeCustom();
            $giftCard = $this->_giftCardFactory->create()->load($valueCouponCodeCustom, 'code');
            if ($giftCard->getId()) {
//                $giftCard->setData('amount_used',$discountAmount)->save();
                $giftCard->setAmountUsed(-$discountAmount)->save();

                $currentCustomerId = $this->_customerSession->getCustomer()->getId();
                $action = "Used for order";

                $modelGiftCardHistory = $this->_giftCardHistoryFactory->create();
                $dataGiftCardHistory = [
                    'giftcard_id' => $giftCard->getId(),
                    'customer_id' => $currentCustomerId,
                    'amount' => -$discountAmount,
                    'action' => $action,
                ];
                $modelGiftCardHistory->addData($dataGiftCardHistory);
                $modelGiftCardHistory->save();
                $result = true;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        return $result;
    }


    public
    function execute(\Magento\Framework\Event\Observer $observer)
    {

//        dd($observer);
        $currentCustomer = $this->_customerSession->isLoggedIn();
        if ($currentCustomer) {
            $orderID = $observer->getData('order')->getIncrementId();
            $customerID = $observer->getData('order')->getCustomerId();

            $quoteId = $observer->getData('order')->getQuoteId();
            $discountAmount = $observer->getData('order')->getDiscountAmount();

//            Update gift card and create gift card history
            $this->checkCodeOfMeOrNotAndUpdateAmountUsed($quoteId, $discountAmount);
            ///


            $create_from = $orderID;
            $amount_used = 0;
            $allAttributeProductOrdered = $observer->getData('order')->getAllVisibleItems();

            $orderGiftCardID = '';
            $createAt = '';
            $arrayGiftCardName = [];
            $arrayGiftCardCode = [];
            $arrayGiftCardPrice = [];
            $arrayGiftCardBalance = [];


            $isVirtualFlag = false;
            $isGiftCardCustomFlag = false;
//            array_push($arrayTest, $isVirtual);
            foreach ($allAttributeProductOrdered as $item) {
                $isVirtual = $item->getIsVirtual();
//                dd($item->getOrderId());
                if ($isVirtual == 1) {
                    $isVirtualFlag = true;

                    $product = $item->getProduct();
                    $giftCardAmount = $product->getData('gift_card_amount');

                    if ($giftCardAmount > 0) {
                        $isGiftCardCustomFlag = true;

                        $orderGiftCardID = $item->getOrderId();
                        $createAt = $item->getCreatedAt();
                        $balance = $giftCardAmount;


//                        $arrayGiftCardName[] = $giftCardName;
//                        $arrayGiftCardPrice[] = $giftCardPrice;
//                        $arrayGiftCardBalance[] = $balance;

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

                                $giftCardName = $item->getName();
                                $giftCardPrice = $product->getData('price');

                                array_push($arrayGiftCardName, $giftCardName);
                                array_push($arrayGiftCardCode, $codeGiftCard);
                                array_push($arrayGiftCardPrice, $giftCardPrice);
                                array_push($arrayGiftCardBalance, $balance);
                            } catch (\Exception $e) {
                                return $e->getMessage();
                            }
                        }
                    }

                }
            }

            if ($isVirtualFlag && $isGiftCardCustomFlag) {
                $this->_helperEmail->sendEmail($orderGiftCardID, $createAt, $arrayGiftCardName, $arrayGiftCardCode, $arrayGiftCardPrice, $arrayGiftCardBalance);
            }
            return $this;
        }
    }
}
