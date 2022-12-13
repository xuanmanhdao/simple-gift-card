<?php
namespace Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCard;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';

    protected function _construct()
    {
        $this->_init(\Mageplaza\SimpleGiftCard\Model\GiftCard::class, \Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCard::class);
    }

}
