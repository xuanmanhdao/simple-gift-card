<?php

namespace Mageplaza\SimpleGiftCard\Cron;

class SendEmail
{
    protected $_helperEmail;

    protected $_logger;


    public function __construct(
        \Mageplaza\SimpleGiftCard\Helper\Email $helperEmail,
        \Psr\Log\LoggerInterface               $logger
    )
    {
        $this->_logger = $logger;
        $this->_helperEmail = $helperEmail;
    }

    public function execute()
    {
        try {
//            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
//            $logger = new \Zend\Log\Logger();
//            $logger->addWriter($writer);
//            $logger->info(__METHOD__);
            $this->_helperEmail->sendEmailByCron();
            return $this;
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
