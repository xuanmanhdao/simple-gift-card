<?php

namespace Mageplaza\SimpleGiftCard\Block\GiftCardHistory\Registry;

use Magento\Framework\View\Element\Template;
use Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory\Collection as GiftCardHistoryCollection;

class ListGiftCard extends \Magento\Framework\View\Element\Template
{
    protected $_giftCardHistoryCollectionFactory;
    protected $_customerSession;

    protected $_giftCardFactory;

    protected $_scopeConfig;

    protected $_localeCurrency;

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface                              $scopeConfig,
        \Magento\Framework\Locale\CurrencyInterface                                     $localeCurrency,
        \Magento\Store\Model\StoreManagerInterface                                      $storeManager,
        \Magento\Customer\Model\Session                                                 $customerSession,
        \Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory\CollectionFactory $giftCardHistoryCollectionFactory,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory                                 $giftCardFactory,
        Template\Context                                                                $context,
        array                                                                           $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->_giftCardHistoryCollectionFactory = $giftCardHistoryCollectionFactory;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_localeCurrency = $localeCurrency;
        $this->_storeManager = $storeManager;
    }

    public function getListGiftCardHistory()
    {
        $currentCustomerId = $this->_customerSession->getCustomer()->getId();

//        $giftCardHistoryId = $this->getRequest()->getParams();

        $giftCardHistoryCollection = $this->_giftCardHistoryCollectionFactory->create()->addFieldToFilter('customer_id', $currentCustomerId);
        $data = $giftCardHistoryCollection->getData();
        return $data;
    }

    public function getCodeById($giftCardId)
    {
        $giftCard = $this->_giftCardFactory->create();
        $giftCard->load($giftCardId);
        return $giftCard;
    }

    public function checkConfiguration(): bool
    {
        $result = false;
        $valueConfigurationRedeem = $this->_scopeConfig->getValue(
            'gift_card/general/allow_redeem_gift_card_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        $valueConfigurationEnable = $this->_scopeConfig->getValue(
            'gift_card/general/gift_card_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        if ($valueConfigurationRedeem == 1 && $valueConfigurationEnable == 1) {
            $result = true;
        }
        return $result;
    }

    public function formatCurrency($currencyCurrent)
    {
        $store = $this->_storeManager->getStore();
//        dd($store->getBaseCurrencyCode());
        $currency = $this->_localeCurrency->getCurrency($store->getBaseCurrencyCode());
        $convertCurrency = $currency->toCurrency(sprintf("%f", $currencyCurrent));
        return $convertCurrency;
    }

    public function getBalanceOfCustomer()
    {
        $currentCustomerBalance = $this->_customerSession->getCustomer()->getGiftcardBalance();
        return $currentCustomerBalance;
    }


    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
