<?php

namespace Mageplaza\SimpleGiftCard\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\SimpleGiftCard\Model\GiftCardFactory;

/**
 * Class GiftCard
 * @package Mageplaza\SimpleGiftCard\Controller\Adminhtml
 */
abstract class GiftCard extends Action
{
    /** Authorization level of a basic admin session */
//    const ADMIN_RESOURCE = 'Mageplaza_SimpleGiftCard::mageplaza_manage_code_crud';

    /**
     * GiftCard Factory
     *
     * @var GiftCardFactory
     */
    public $giftCardFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * GiftCard constructor.
     *
     * @param GiftCardFactory $giftCardFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        GiftCardFactory $giftCardFactory,
        Registry        $coreRegistry,
        Context         $context
    )
    {
        $this->giftCardFactory = $giftCardFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * @param bool $register
     * @param bool $isSave
     *
     * @return bool|\Mageplaza\SimpleGiftCard\Model\GiftCard
     */
    protected function initGiftCard($register = false, $isSave = false)
    {
        $giftCardId = (int)$this->getRequest()->getParam('id');
//        $duplicate = $this->getRequest()->getParam('post')['duplicate'] ?? null;

        /** @var \Mageplaza\SimpleGiftCard\Model\GiftFactory $giftCard */
        $giftCard = $this->giftCardFactory->create();
        if ($giftCardId && !$isSave) {
            $giftCard->load($giftCardId);
            if (!$giftCard->getId()) {
//                $this->messageManager->addErrorMessage(__('This gift card ID not exists.'));
                $this->messageManager->addError(__('This gift card ID not exists.'));
                return false;

                /*$this->messageManager->addError(__('This gift card ID not exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('backend/code/index');*/

            }
        }


        if ($register) {
            $this->coreRegistry->register('mageplaza_simple_gift_card', $giftCard);
        }

        return $giftCard;
    }
}
