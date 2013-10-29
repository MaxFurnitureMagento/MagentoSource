<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Banners_Helper_Data */
        $hlp = Mage::helper('ambanners');
        
        $fieldset = $form->addFieldset('general', array('legend'=> $hlp->__('General')));
        $fieldset->addField('rule_name', 'text', array(
            'label'     => $hlp->__('Name'),
            'required'  => true,
            'name'      => 'rule_name',
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'label'     => $hlp->__('Status'),
            'title'     => $hlp->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $hlp->getStatuses(), 
        ));
        
		$fieldset->addField('stores', 'multiselect', array(
            'label'     => $hlp->__('Stores'),
            'name'      => 'stores[]',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            'note'      => $hlp->__('Leave empty to show the banner in all stores'), 
        ));  
		
		$fieldset->addField('cust_groups', 'multiselect', array(
            'name'      => 'cust_groups[]',
            'label'     => $hlp->__('Customer Groups'),
            'values'    => $hlp->getAllGroups(),
            'note'      => $hlp->__('Leave empty to show the banner for all groups'),
        ));
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => $hlp->__('From Date'),
            'title'  => $hlp->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' =>  Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
        ));
        
        $fieldset->addField('from_time', 'text', array(
            'name'   => 'from_time',
            'label'  => $hlp->__('From Time'),
            'title'  => $hlp->__('From Time'),
        	'note'      => $hlp->__('In format 15:32'),
        ));
        
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => $hlp->__('To Date'),
            'title'  => $hlp->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' =>  Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
		$fieldset->addField('to_time', 'text', array(
            'name'   => 'to_time',
            'label'  => $hlp->__('To Time'),
            'title'  => $hlp->__('To Time'),
        	'note'      => $hlp->__('In format 19:32'),
        ));
        
		$fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => $hlp->__('Priority'),
        ));
        
        $data = Mage::registry('ambanners_rule')->getData();
        $data['is_active'] = '1';
        
        if (isset($data['from_date'])) {
	        $dateFrom = explode(" ", $data['from_date']);
	        $dateTo = explode(" ", $data['to_date']);
	        
	        if ($dateFrom[1] != '00:00:00') {
		        $data['from_time'] = $dateFrom[1];
	        }
	        
	        if ($dateTo[1] != '00:00:00') {
		        $data['to_time'] = $dateTo[1];
	        }
        }
        $form->setValues($data);
        
        return parent::_prepareForm();
    }
}