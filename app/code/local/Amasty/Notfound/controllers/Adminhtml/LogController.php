<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Notfound_Adminhtml_LogController extends Amasty_Notfound_Controller_Abstract
{
    protected $_title     = 'Not found pages';
    protected $_modelName = 'log'; 
    
    
    public function editAction() 
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('amnotfound/' . $this->_modelName)->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amnotfound')->__('Record does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		else {
		    $model->setPage($this->getUrlPath($model));
		}
		
		Mage::register('amnotfound_' . $this->_modelName, $model);

		
		$this->loadLayout();
		$this->_setActiveMenu('report/amnotfound');
        $this->_addContent($this->getLayout()->createBlock('amnotfound/adminhtml_' . $this->_modelName . '_edit'));
		$this->renderLayout();
	}  
    
	public function saveAction() 
	{
	    $id     = $this->getRequest()->getParam('id',0);
	    $model  = Mage::getModel('amnotfound/' . $this->_modelName)->load($id);
	    $data = $this->getRequest()->getPost();
	    
		if ($id && $data && $model->getId()) {
			
			try {
			    $url = $this->getUrlPath($model);
			    
			    $rewrite = Mage::getModel('core/url_rewrite')->load($id);
                $rewrite->setIdPath('am-'.md5($url))
                    ->setTargetPath($data['page'])
                    ->setOptions('RP')
                    ->setStoreId($model->getStoreId())
                    ->setIsSystem(0)
                    ->setDescription(Mage::helper('amnotfound')->__('Generated for a wrong URL `%s`', $url))
                    ->setRequestPath($url)
                    ->save();
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('amnotfound')->__('Redirect has been successfully saved. See Catalog > Url Rewrites');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                $this->_redirect('*/*');
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }	
            return;
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amnotfound')->__('Unable to find a record to save'));
        $this->_redirect('*/*');
	}
	
	protected function getUrlPath($model)
	{
        $base = Mage::app()->getStore($model->getStoreId())->getBaseUrl();
        $base = substr($base, 7); // remove http://;
        return str_replace($base, '', $model->getUrl());	    
	}
    
}