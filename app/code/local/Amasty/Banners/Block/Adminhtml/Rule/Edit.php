<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Banners_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'ambanners';
        $this->_controller = 'adminhtml_rule';
        
        $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('salesrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
        $this->_formScripts[] = "function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";

		$this->_formScripts[] = " function showOptions(sel) {
            new Ajax.Request('" . $this->getUrl('*/*/options', array('isAjax'=>true)) ."', {
                parameters: {code : sel.value},
                onSuccess: function(transport) {
                    sel.up('tr').next('tr').down('td').next('td').update(transport.responseText);
                }
            });
        }"; 
    }
   

    public function getHeaderText()
    {
        $header = Mage::helper('ambanners')->__('New Banner');
        $model = Mage::registry('ambanners_rule');
        if ($model->getId()){
            $header = Mage::helper('ambanners')->__('Edit Banner `%s`', $model->getRuleName());
        }
        return $header;
    }
}