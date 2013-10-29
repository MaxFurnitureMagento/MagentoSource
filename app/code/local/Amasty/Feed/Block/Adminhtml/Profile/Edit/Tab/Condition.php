<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/ 
class Amasty_Feed_Block_Adminhtml_Profile_Edit_Tab_Condition extends Amasty_Feed_Block_Adminhtml_Widget_Edit_Tab_Dynamic
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amfeed/feed/condition.phtml');
        $this->_fields  = array('attr', 'op', 'val');
        $this->_model   = 'amfeed_profile';        
    } 

    public function getOperations()
    {
        return Mage::helper('amfeed')->getOperations();
    } 
    
    public function getAttributes()
    {
        return Mage::helper('amfeed')->getAttributes();
    }
    
    public function getProductTypes()
    {
        return Mage::helper('amfeed')->getProductTypes();
    }
}