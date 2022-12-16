<?php
namespace Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection{
    protected $_idFieldName = 'history_id';
    protected function _construct()
    {
        $this->_init('Mageplaza\SimpleGiftCard\Model\GiftCardHistory', 'Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory');
    }
}
