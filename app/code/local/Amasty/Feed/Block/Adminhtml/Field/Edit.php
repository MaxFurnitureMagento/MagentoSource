<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/
class Amasty_Feed_Block_Adminhtml_Field_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'amfeed';
        $this->_controller = 'adminhtml_field';
    }

    public function getHeaderText()
    {
        return Mage::helper('amfeed')->__('Custom Field');
    }
}