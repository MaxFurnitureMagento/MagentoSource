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

class Mxp_Menuadmin_Block_Adminhtml_Menuadmin_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('menuadmin_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('menuadmin')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('menuadmin')->__('Item Information'),
          'title'     => Mage::helper('menuadmin')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('menuadmin/adminhtml_menuadmin_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}