<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */   
class Amasty_Notfound_Block_Adminhtml_Abstract extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_header    = 'Not Found Pages';
    protected $_modelName = 'log';
    
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_' . $this->_modelName;
        $this->_blockGroup = 'amnotfound';
        $this->_headerText = Mage::helper('amnotfound')->__($this->_header);
        $this->_removeButton('add'); 
        
        $confirm = "'". Mage::helper('amnotfound')->__('Are you sure?') . "'";
        $this->_addButton('truncate_log', array(
            'label'     => Mage::helper('core')->__('Clear Log'),
            'onclick'   => 'if (confirm('.$confirm.')) {setLocation(\'' . $this->getUrl('*/*/clear')  .'\')}',
            'class'     => 'delete',
        )); 
    }
}