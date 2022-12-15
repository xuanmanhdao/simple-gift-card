<?php

namespace Mageplaza\SimpleGiftCard\Block\Adminhtml\Code\Edit\Tab;

class Code extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

//    public $collection;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context            $context,
        \Magento\Framework\Registry                        $registry,
        \Magento\Framework\Data\FormFactory                $formFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array                                              $data = []
    )
    {

        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        /** @var $model \Mageplaza\SimpleGiftCard\Model\GiftCard */
        $model = $this->_coreRegistry->registry('mageplaza_simple_gift_card');
//        dd($model->getData());

        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gift Card Information')]);
        if (!$model->getId()) {
            $fieldset->addField('code_length', 'text', [
                'name' => 'code_length',
                'label' => __('Code Length'),
                'title' => __('Code Length'),
                'value' => $this->_scopeConfig->getValue(
                    'gift_card/code/code_length',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                ),
                'class' => 'integer required-entry validate-greater-than-zero validate-length maximum-length-12 no-whitespace',
            ]);
            $fieldset->addField('balance', 'text', [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'class' => 'required-entry validate-zero-or-greater no-whitespace',
                'required' => true
            ]);
        } else {
            $fieldset->addField('code', 'text', [
                'name' => 'code',
                'label' => __('Code'),
                'title' => __('Code'),
                'readonly' => true,
                'required' => true
            ]);
            $fieldset->addField('balance', 'text', [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'class' => 'required-entry validate-zero-or-greater no-whitespace',
                'required' => true
            ]);
            $fieldset->addField('create_from', 'text', [
                'name' => 'create_from',
                'label' => __('Create From'),
                'title' => __('Create From'),
                'readonly' => true,
                'required' => true
            ]);
        }


        if ($model->getId()) {
            $data = $model->getData();
            $form->setValues($data);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return __('Gift card information');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
