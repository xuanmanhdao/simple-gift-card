<?php

namespace Mageplaza\SimpleGiftCard\Controller\GiftCardHistory;

use Magento\Framework\App\Action\Context;
use Mageplaza\SimpleGiftCard\Model\GiftCardFactory;

class Redeem extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;

    protected $_giftCardFactory;

    protected $_connection;

    protected $_giftCardCollectionFactory;

    protected $_jsonFactory;

    protected $_giftCardHistoryFactory;

    protected $_customerFactory;

    protected $_customerRepoInterface;


    protected $_customer;

    protected $_customerData;

    protected $_giftCardHistoryResource;

    public function __construct(Context                                                                  $context,
                                GiftCardFactory                                                          $giftCardFactory,
                                \Magento\Customer\Model\Session                                          $customerSession,
                                \Magento\Framework\App\ResourceConnection                                $resource,
                                \Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
                                \Magento\Framework\Controller\Result\JsonFactory                         $jsonFactory,
                                \Mageplaza\SimpleGiftCard\Model\GiftCardHistoryFactory                   $giftCardHistoryFactory,
                                \Magento\Customer\Model\CustomerFactory                                  $customerFactory,
                                \Magento\Customer\Api\CustomerRepositoryInterface                        $customerRepoInterface,

                                \Magento\Customer\Model\Customer                                         $customer,
                                \Magento\Customer\Model\Data\Customer                                    $customerData,
                                \Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory            $giftCardHistoryResource

    )
    {
        $this->_customerSession = $customerSession;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_connection = $resource->getConnection();
        $this->_giftCardCollectionFactory = $giftCardCollectionFactory;

        $this->_jsonFactory = $jsonFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customerRepoInterface = $customerRepoInterface;
        parent::__construct($context);

        $this->_customer = $customer;
        $this->_customerData = $customerData;
        $this->_giftCardHistoryResource = $giftCardHistoryResource;
    }

    public function getGiftCardByCode($code)
    {
//        $query = $this->_connection->fetchAll("SELECT * FROM mageplaza_simple_gift_card where code='$code'");
//        return $query;

//        $giftCard = $this->_giftCardCollectionFactory->create()->addFieldToFilter('code', $code);

        $giftCard = $this->_giftCardFactory->create()->load($code, 'code');
        return $giftCard;
    }

    public function execute()
    {

//        $currentCustomerId = $this->_customerSession->getCustomer()->getId();
//        $customer = $this->_customerRepoInterface->getById($currentCustomerId);
//        $customerData = $customer->getDataModel();
//        $customerData->setCustomAttribute('giftcard_balance', 30);
//        $customer->updateData($customerData);
//        $customer->save();
//        die('122');
        $currentCustomer = $this->_customerSession->isLoggedIn();
        if (!$currentCustomer) {
            return $this->_redirect('customer/account/login/');
        } else {
            $objectRequest = $this->getRequest()->getPost();
            $valueCode = trim(ucwords($objectRequest->code));

            $dataGiftCard = $this->getGiftCardByCode($valueCode);
            $giftCardID = $dataGiftCard->getId();

            if ($giftCardID) {
                $amountCurrent = $dataGiftCard->getBalance() - $dataGiftCard->getAmountUsed();
                if ($amountCurrent == 0) {
                    $resultJson = $this->_jsonFactory->create();
                    return $resultJson->setData(['status' => 400, 'message' => 'Amount current of gift card is 0']);
                } elseif ($amountCurrent > 0) {
                    try {

                        $currentCustomerId = $this->_customerSession->getCustomer()->getId();
                        $action = "Redeem";

                        $modelGiftCardHistory = $this->_giftCardHistoryFactory->create();
                        $dataGiftCardHistory = [
                            'giftcard_id' => $giftCardID,
                            'customer_id' => $currentCustomerId,
                            'amount' => $amountCurrent,
                            'action' => $action,
                        ];
                        $modelGiftCardHistory->addData($dataGiftCardHistory);
                        $modelGiftCardHistory->save();

                        $dataGiftCard->setAmountUsed($dataGiftCard->getBalance());
                        $dataGiftCard->save();

                        $modelCustomer = $this->_customerFactory->create()->load($currentCustomerId);
                        $balanceCurrentOfCustomer = $modelCustomer->getGiftcardBalance();

                        $totalBalance = $balanceCurrentOfCustomer + $amountCurrent;

                        $this->_giftCardHistoryResource->setBalanceCustomer($totalBalance, $currentCustomerId);

                        $resultJson = $this->_jsonFactory->create();
                        return $resultJson->setData(['status' => 200, 'message' => 'Redeem success!']);
                    } catch (\Exception $e) {
                        $resultJson = $this->_jsonFactory->create();
                        return $resultJson->setData(['status' => 400, 'message' => "An error occurred while adding new! $e"]);
                    }
                } else {
                    $resultJson = $this->_jsonFactory->create();
                    return $resultJson->setData(['status' => 400, 'message' => 'Amount current of gift card is not 0 or >0']);
                }
            } else {
                $resultJson = $this->_jsonFactory->create();
                return $resultJson->setData(['status' => 400, 'message' => 'Gift Card not exist!']);
            }
        }
    }
}
