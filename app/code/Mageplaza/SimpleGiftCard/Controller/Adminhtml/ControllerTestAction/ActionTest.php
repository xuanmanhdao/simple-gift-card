<?php

namespace Mageplaza\SimpleGiftCard\Controller\Adminhtml\ControllerTestAction;

use Magento\Framework\App\Action\Context;

class ActionTest extends \Magento\Framework\App\Action\Action
{
    protected $_giftCardFactory;

    public function __construct(
        Context                                         $context,
        \Mageplaza\SimpleGiftCard\Model\GiftCardFactory $giftCardFactory
    )
    {
        $this->_giftCardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $giftCard = $this->_giftCardFactory->create();
            echo "</br>" . "Hello admin! LOL";
        $data = [
            'code' => "NayNAyNay",
            'balance' => 100000.0000,
            'amount_used' => 42.000,
            'create_from' => 'admin',
        ];

//        Store data
        try {
            $giftCard->addData($data)->save();
            echo "</br>" . $giftCard->testABCD('Hello my friend. Ban da save thanh cong!');
        } catch (\Exception $e) {
            echo "</br>".$e->getMessage() . "Not saved! SOS";
        }

////        Edit data
//        try {
//            $dataByID = $giftCard->load(11);
//            if ($dataByID->getData('giftcard_id')) {
//                $dataByID->setCode('ZXCVBNM')->save();
//                echo "</br>" . $giftCard->testABCD('Hello my friend. Ban da edit thanh cong!');
//            } else {
//                echo "</br>" . $giftCard->testABCD('Hello my friend. ID of gift card not exist!');
//            }
//        } catch (\Exception $e) {
//            echo "</br>" .$e->getMessage(). "Not edited! SOS";
//        }

//        Delete data
//        try {
//            $dataByID = $giftCard->load(6);
//            echo $dataByID->getData('giftcard_id');
//
//            if ($dataByID->getData('giftcard_id')) {
//                $dataByID->delete();
//                echo "</br>" . $giftCard->testABCD('Hello my friend. Ban da delete thanh cong!');
//            } else {
//                echo "</br>" . $giftCard->testABCD('Hello my friend. ID of gift card want to delete not exist!');
//            }
//        } catch (\Exception $e) {
//            echo "</br>" .$e->getMessage().  "Not edited! SOS";
//        }

    }
}
