<?php

namespace Mageplaza\SimpleGiftCard\Controller\Adminhtml\Code;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\SimpleGiftCard\Model\GiftCardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class Save extends Action
{
    protected $resultPageFactory;
    protected $giftCardFactory;

    public function __construct(
        Context         $context,
        PageFactory     $resultPageFactory,
        GiftCardFactory $giftCardFactory,
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->giftCardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        try {
            $paramID = (int)$this->getRequest()->getParam('id');
//            dd($paramID);
            if (!$paramID) {
                $objectRequest = $this->getRequest()->getPost();
//            $data = (array)$objectRequest;
                if ($objectRequest) {
                    $codeLength = $objectRequest->code_length;
                    function getGiftCode($valueCodeLength)
                    {
                        $characters = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
                        $randomString = '';

                        for ($i = 0; $i < $valueCodeLength; $i++) {
                            $index = rand(0, strlen($characters) - 1);
                            $randomString .= $characters[$index];
                        }

                        return $randomString;
                    }

                    $giftCode = getGiftCode($codeLength);

                    $create_from = 'admin';
                    $amount_used = 0;

                    $model = $this->giftCardFactory->create();
                    $model->setData('code', $giftCode);
                    $model->setData('balance', $objectRequest->balance);
                    $model->setData('amount_used', $amount_used);
                    $model->setData('create_from', $create_from);
                    $model->save();

                    $this->messageManager->addSuccessMessage(__("Data Saved Successfully."));
                }
            } else {
                $model = $this->giftCardFactory->create();
                $dataByID = $model->load($paramID);
                if ($dataByID->getData('giftcard_id')) {
                    $objectRequest = $this->getRequest()->getPost();
                    $dataByID->setBalance($objectRequest->balance)->save();
                    $this->messageManager->addSuccessMessage(__("Data Changed Successfully."));
                } else {
                    $this->messageManager->addErrorMessage(__("Data Changed Error."));
                }
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
}
