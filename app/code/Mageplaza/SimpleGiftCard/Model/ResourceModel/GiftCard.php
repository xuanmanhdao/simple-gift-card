<?php
namespace Mageplaza\SimpleGiftCard\Model\ResourceModel;

class GiftCard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('mageplaza_simple_gift_card', 'giftcard_id');
    }

}
