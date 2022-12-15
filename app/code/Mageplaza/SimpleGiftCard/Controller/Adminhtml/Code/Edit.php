<?php

namespace Mageplaza\SimpleGiftCard\Controller\Adminhtml\Code;


//class Edit extends \Magento\Backend\App\Action
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mageplaza\SimpleGiftCard\Model\GiftCardFactory;

class Edit extends \Mageplaza\SimpleGiftCard\Controller\Adminhtml\GiftCard
{

    protected $_resultPageFactory;

    public function __construct(GiftCardFactory                            $giftCardFactory,
                                Registry                                   $coreRegistry,
                                Context                                    $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($giftCardFactory, $coreRegistry, $context);
    }

    public function execute()
    {
        /** @var \Mageplaza\SimpleGiftCard\Model\GiftCard $giftCard */
        $giftCard = $this->initGiftCard();

        if (!$giftCard) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');
            return $resultRedirect;
        }


        $this->coreRegistry->register('mageplaza_simple_gift_card', $giftCard);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }


}
