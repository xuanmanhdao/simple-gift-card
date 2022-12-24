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

    private $_actionFlag;

    private $_redirect;

    public function __construct(
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory      $giftCardFactory,
        \Magento\Checkout\Model\Session                      $checkoutSession,
        \Magento\Framework\ObjectManagerInterface            $objectManager,
        \Magento\Framework\Message\ManagerInterface          $messageManager,
        \Magento\Checkout\Model\Cart                         $cart,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\App\ActionFlag                    $actionFlag,
        \Magento\Framework\App\Response\RedirectInterface    $redirect,

    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
        $this->_cart = $cart;
        $this->_resultRedirectFactory = $redirectFactory;
        $this->_actionFlag = $actionFlag;
        $this->_redirect = $redirect;
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
        $controllerAction = $observer->getEvent()->getControllerAction();
        $currentActionName = $controllerAction->getRequest()->getActionName();
        $valueCodeCustomerApply = $observer->getControllerAction()->getRequest()->getParam('coupon_code');

//        $result = $observer->getEvent()->getResult();
        if ($this->checkIsCodeCustomOrNot($valueCodeCustomerApply)) {
            try {
                $quote = $this->_checkoutSession->getQuote()->collectTotals();
                $quote->setCouponCode($valueCodeCustomerApply)->save();
                $this->_messageManager->addSuccessMessage("You used coupon code $valueCodeCustomerApply");
                $controllerAction->getResponse()->setRedirect($this->_redirect->getRefererUrl());
//                $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->_actionFlag->set($currentActionName, \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

//                $this->_redirect->redirect($controllerAction->getResponse(), $this->_redirect->getRefererUrl());
                $this->_redirect->redirect($controllerAction->getResponse(),$this->_redirect->getRefererUrl());
                return $this;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->_messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
    }

    /* public function setDiscount($observer)
   {
       $quote = $observer->getEvent()->getQuote();
       $quoteid = $quote->getId();
       $discountAmount = 10;
       if ($quoteid) {
           if ($discountAmount > 0) {
               $total = $quote->getBaseSubtotal();
               $quote->setSubtotal(0);
               $quote->setBaseSubtotal(0);

               $quote->setSubtotalWithDiscount(0);
               $quote->setBaseSubtotalWithDiscount(0);

               $quote->setGrandTotal(0);
               $quote->setBaseGrandTotal(0);


               $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
               foreach ($quote->getAllAddresses() as $address) {

                   $address->setSubtotal(0);
                   $address->setBaseSubtotal(0);

                   $address->setGrandTotal(0);
                   $address->setBaseGrandTotal(0);

                   $address->collectTotals();

                   $quote->setSubtotal((float)$quote->getSubtotal() + $address->getSubtotal());
                   $quote->setBaseSubtotal((float)$quote->getBaseSubtotal() + $address->getBaseSubtotal());

                   $quote->setSubtotalWithDiscount(
                       (float)$quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                   );
                   $quote->setBaseSubtotalWithDiscount(
                       (float)$quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                   );

                   $quote->setGrandTotal((float)$quote->getGrandTotal() + $address->getGrandTotal());
                   $quote->setBaseGrandTotal((float)$quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

                   $quote->save();

                   $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                       ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                       ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                       ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                       ->save();


                   if ($address->getAddressType() == $canAddItems) {
                       //echo $address->setDiscountAmount; exit;
                       $address->setSubtotalWithDiscount((float)$address->getSubtotalWithDiscount() - $discountAmount);
                       $address->setGrandTotal((float)$address->getGrandTotal() - $discountAmount);
                       $address->setBaseSubtotalWithDiscount((float)$address->getBaseSubtotalWithDiscount() - $discountAmount);
                       $address->setBaseGrandTotal((float)$address->getBaseGrandTotal() - $discountAmount);
                       if ($address->getDiscountDescription()) {
                           $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                           $address->setDiscountDescription($address->getDiscountDescription() . ', Custom Discount');
                           $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                       } else {
                           $address->setDiscountAmount(-($discountAmount));
                           $address->setDiscountDescription('Custom Discount');
                           $address->setBaseDiscountAmount(-($discountAmount));
                       }
                       $address->save();
                   }//end: if
               } //end: foreach
               //echo $quote->getGrandTotal();

               foreach ($quote->getAllItems() as $item) {
                   //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                   $rat = $item->getPriceInclTax() / $total;
                   $ratdisc = $discountAmount * $rat;
                   $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                   $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
               }
           }
       }
   }*/
}
