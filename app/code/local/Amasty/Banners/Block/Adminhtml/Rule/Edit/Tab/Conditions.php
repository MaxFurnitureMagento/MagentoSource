<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('ambanners')->__('Cart Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('ambanners')->__('Cart Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('ambanners_rule');

        $form = new Varien_Data_Form();
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/adminhtml_rule/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('rule_conditions_fieldset', array(
            'legend'=>Mage::helper('ambanners')->__('Apply the rule only if the following cart conditions are met')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('ambanners')->__('Cart Conditions'),
            'title' => Mage::helper('ambanners')->__('Cart Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
