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

class Mxp_Menuadmin_Block_Adminhtml_Menuadmin extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_menuadmin';
		$this->_blockGroup = 'menuadmin';
		$this->_headerText = Mage::helper('menuadmin')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('menuadmin')->__('Add Item');
		parent::__construct();
	}


}