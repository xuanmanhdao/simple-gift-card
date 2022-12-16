<?php
namespace Mageplaza\SimpleGiftCard\Model\ResourceModel;

class GiftCardHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb{
    protected function _construct()
    {
        $this->_init('mageplaza_giftcard_history', 'history_id');
    }
}
