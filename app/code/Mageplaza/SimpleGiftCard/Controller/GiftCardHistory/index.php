<?php

namespace Mageplaza\SimpleGiftCard\Controller\GiftCardHistory;
class Index extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;

    protected $_customerSession;

    /**      * @param \Magento\Framework\App\Action\Context $context */
    public function __construct(\Magento\Framework\App\Action\Context      $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Customer\Model\Session            $customerSession)
    {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
//        $currentCustomer = $this->_customerSession->getCustomer()->getId();

        $currentCustomer = $this->_customerSession->isLoggedIn();

        if (!$currentCustomer) {
            return $this->_redirect('customer/account/login/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('List Gift Registry'));
        return $resultPage;
    }

    /*public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }*/

}

