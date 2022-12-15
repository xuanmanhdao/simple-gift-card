<?php
namespace Mageplaza\SimpleGiftCard\Model;
class GiftCard extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mageplaza\SimpleGiftCard\Model\ResourceModel\GiftCard::class);
    }

    public function testABCD($value){
        return "Day la ham test ABCD o model GiftCard. Ban truyen vao value: $value";
    }


}
