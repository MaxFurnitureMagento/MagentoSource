<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

class Mxp_Menuadmin_Block_Adminhtml_Menuadmin_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('menuadmin_form', array('legend'=>Mage::helper('menuadmin')->__('Item information')));

      $fieldset->addField('pid', 'select', array(
          'label'     => Mage::helper('menuadmin')->__('Children of'),
          'name'      => 'pid',
          'values'    => Mage::helper('menuadmin')->getSelectcat(),
      ));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('menuadmin')->__('Label'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('menuadmin')->__('Type'),
          'name'      => 'type',
          'values'    => Mage::getSingleton('menuadmin/type')->getOptionArray(),
      ));

      $fieldset->addField('link', 'text', array(
          'label'     => Mage::helper('menuadmin')->__('Link'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'link',
      ));

      $fieldset->addField('cssclass', 'text', array(
          'label'     => Mage::helper('menuadmin')->__('Css class'),
          'required'  => false,
          'name'      => 'cssclass',
      ));

      $fieldset->addField('target', 'select', array(
          'label'     => Mage::helper('menuadmin')->__('Target'),
          'name'      => 'target',
          'values'    => Mage::getSingleton('menuadmin/target')->getOptionArray(),
      ));

      $fieldset->addField('position', 'text', array(
          'label'     => Mage::helper('menuadmin')->__('Position'),
          'required'  => false,
          'name'      => 'position',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('menuadmin')->__('Status'),
          'name'      => 'status',
          'values'    => Mage::getSingleton('menuadmin/status')->getOptionArray(),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getMenuadminData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMenuadminData());
          Mage::getSingleton('adminhtml/session')->setMenuadminData(null);
      } elseif ( Mage::registry('menuadmin_data') ) {
          $form->setValues(Mage::registry('menuadmin_data')->getData());
      }
      return parent::_prepareForm();
  }
}