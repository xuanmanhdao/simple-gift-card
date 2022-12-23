<?php

namespace Mageplaza\SimpleGiftCard\Block\Adminhtml\Code;

use Mageplaza\SimpleGiftCard\Model\GiftCard;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry           $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array                                 $data = []
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'giftcard_id'; // khai báo id
        $this->_blockGroup = 'Mageplaza_SimpleGiftCard';  //Tên module của bạn
        $this->_controller = 'adminhtml_code';  // đường dẫn theo cấu trúc folder trong controller
        /*
         * Khi các bạn gán các tham số _blockGroup và _controller Magento2 sẽ tự động xây dựng  cho các bạn một lớp Form. Theo đường dẫn
            Mageplaza/Giftcard/Block/Adminhtml/Code/Edit/Form.php
            Vì thế chúng ta sẽ cần phải tạo file này và đinh nghĩa cho nó.
         * */
        parent::_construct();

//        Custom
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->update('reset', 'label', __('Reset Form'));
    }

    /**
     * Retrieve text for header element depending on loaded gift card
     *
     * @return string
     */
    public function getHeaderText()
    {
        $posts = $this->_coreRegistry->registry('mageplaza_simple_gift_card');
        if ($posts->getId()) {
            $postsTitle = $this->escapeHtml($posts->getTitle());
            return __("Edit Gift Card '%1'", $postsTitle);
        } else {
            return __('Add Gift Card');
        }
    }

    public function getFormActionUrl()
    {
        /** @var GiftCard $giftCard */
        $giftCard = $this->_coreRegistry->registry('mageplaza_simple_gift_card');
        if ($giftCard->getId()) {
            $ar = ['id' => $giftCard->getId()];

            return $this->getUrl('*/*/save', $ar);
        }

        return parent::getFormActionUrl();
    }


}

