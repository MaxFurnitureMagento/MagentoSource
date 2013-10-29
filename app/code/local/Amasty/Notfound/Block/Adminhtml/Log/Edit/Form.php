<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Notfound_Block_Adminhtml_Log_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
        //create form structure
        $form = new Varien_Data_Form(array(
          'id'      => 'edit_form',
          'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))), 
          'method'  => 'post',
         ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        $hlp   = Mage::helper('amnotfound');
        $model = Mage::registry('amnotfound_log');
        
        $fldMain = $form->addFieldset('main', array('legend'=> $hlp->__('General Information')));
        
        $fldMain->addField('url', 'label', array(
          'label'     => $hlp->__('From'),
          'name'      => 'url',
        ));         
        
        $fldMain->addField('page', 'text', array(
          'label'     => $hlp->__('To'),
          'name'      => 'page',
          'required'  => true,
          'note'      => 'e.g. the-right-page.html',
        )); 
               
        //set form values
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data) {
            $form->setValues($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        elseif ($model) {
            $form->setValues($model->getData());
        }
        
        return parent::_prepareForm();
  }
}