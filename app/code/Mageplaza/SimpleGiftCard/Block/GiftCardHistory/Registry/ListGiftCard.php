<?php

namespace Mageplaza\SimpleGiftCard\Block\GiftCardHistory\Registry;

use Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory\Collection as GiftCardHistoryCollection;

class ListGiftCard extends \Magento\Framework\View\Element\Template
{
    protected $_giftCardHistoryCollectionFactory;
    protected $_customerSession;

    protected $_giftCardFactory;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context                               $context,
        \Magento\Customer\Model\Session $customerSession,
        \Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory\CollectionFactory $giftCardHistoryCollectionFactory,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory $giftCardFactory,
        array                                                                          $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_giftCardHistoryCollectionFactory = $giftCardHistoryCollectionFactory;
        $this->_giftCardFactory = $giftCardFactory;
    }

    public function getListGiftCardHistory()
    {
       $currentCustomerId= $this->_customerSession->getCustomer()->getId();

//        $giftCardHistoryId = $this->getRequest()->getParams();

        $giftCardHistoryCollection = $this->_giftCardHistoryCollectionFactory->create()->addFieldToFilter('customer_id', $currentCustomerId);
        $data = $giftCardHistoryCollection->getData();
        return $data;
    }

    public function getCodeById($giftCardId){
        $giftCard = $this->_giftCardFactory->create();
        $giftCard->load($giftCardId);
        return $giftCard;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
