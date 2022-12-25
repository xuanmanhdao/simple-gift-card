<?php

namespace Mageplaza\SimpleGiftCard\Model\ResourceModel;

class GiftCardHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_customerEntityTable;

    public $_eventManager;

    protected $_request;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface         $eventManager,
        \Magento\Framework\App\RequestInterface           $request
    )
    {
        $this->_eventManager = $eventManager;
        $this->_request = $request;
        parent::__construct($context);

        $this->_customerEntityTable = $this->getTable('customer_entity');
    }

    protected function _construct()
    {
//        $this->_init('mageplaza_giftcard_history', 'history_id');
        $this->_init('giftcard_history', 'history_id');
    }

    public function setBalanceCustomer($totalBalance, $currentCustomerId)
    {
        $tableName = $this->_customerEntityTable;
        $data = ["giftcard_balance" => $totalBalance];
        $condition = ['entity_id = ?' => (int)$currentCustomerId];
        $this->getConnection()->update($tableName, $data, $condition);
    }
}
