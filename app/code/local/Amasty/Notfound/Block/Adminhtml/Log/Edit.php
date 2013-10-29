<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/
class Amasty_Notfound_Block_Adminhtml_Log_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'amnotfound';
        $this->_controller = 'adminhtml_log';
        
        $this->_removeButton('reset');  
        $this->_removeButton('delete');  
    }

    public function getHeaderText()
    {
        return Mage::helper('amnotfound')->__('Create Redirect');
    }
}