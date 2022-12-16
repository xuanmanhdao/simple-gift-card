<?php
namespace Mageplaza\SimpleGiftCard\Model;
class GiftCardHistory extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCardHistory::class);
    }
}
