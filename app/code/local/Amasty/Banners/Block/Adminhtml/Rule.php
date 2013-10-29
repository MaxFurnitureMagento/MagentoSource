<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'ambanners';
        $this->_headerText = Mage::helper('ambanners')->__('Banners');
        $this->_addButtonLabel = Mage::helper('ambanners')->__('Add Banner');
        parent::__construct();
    }
}