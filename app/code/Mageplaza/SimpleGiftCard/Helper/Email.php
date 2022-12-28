<?php

namespace Mageplaza\SimpleGiftCard\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

//use Mageplaza\SimpleGiftCard\Model\Mail\TransportBuilder as TransportBuilderCustom;

class Email extends AbstractHelper
{
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;

    public function __construct(
        Context               $context,
        TransportBuilder      $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface        $state
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        parent::__construct($context);
    }

    public function sendEmail()
    {
        $templateId = 'newsletter_order_success_confirm_custom_email_template'; // template id
        $fromEmail = 'owner@domain.com';  // sender Email id
        $fromName = 'Admin';             // sender Name
        $toEmail = 'customer@email.com'; // receiver email id

        try {
            // template variables pass here
            $templateVars = [
                'incrementId' => 'test',
                'createdAt' => 'test1',
                'emailCustomerNote' => 'test2',
                'valueGiftCard'=>'test3',
                'priceGiftCard'=>'test4',
                'nameGiftCard'=>'test5'
            ];

            $storeId = $this->storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}
