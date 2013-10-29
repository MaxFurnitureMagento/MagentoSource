<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Notfound_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    protected $_title     = 'Not found pages';
    protected $_modelName = 'log';
    
	public function indexAction() 
	{
	    try {
            $result = Mage::getModel('amnotfound/' . $this->_modelName)->collect();
            if (is_array($result)){ // add errors to the session
                foreach ($result as $err){
	               Mage::getSingleton('adminhtml/session')->addError($err);                    
                }
            }
	    }
        catch (Exception $e) {
            print_r($e->getMessage()); // todo
        }
        
        $this->loadLayout(); 
        $this->_setActiveMenu('report/amnotfound');
        $this->_addBreadcrumb($this->__('Reports'), $this->__($this->_title)); 
        $this->_title($this->__($this->_title))
             ->_title($this->__('Reports'))
             ->_title($this->__('Errors and Missing Pages'))
        ;         
        $this->_addContent($this->getLayout()->createBlock('amnotfound/adminhtml_' . $this->_modelName)); 	    
            $this->renderLayout();
	}
	
    public function clearAction() 
	{
	    Mage::getModel('amnotfound/' . $this->_modelName)->clear();
	    Mage::getSingleton('adminhtml/session')->addSuccess(
	       $this->__('%s have been cleared', $this->__($this->_title)
	    )); 
	    $this->_redirect('*/*/'); 
	}
}