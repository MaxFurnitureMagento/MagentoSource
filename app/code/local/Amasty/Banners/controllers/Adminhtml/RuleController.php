<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Adminhtml_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected $_title     = 'Banner';
    protected $_modelName = 'rule';
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Promotions'))->_title($this->__($this->_title));     
        return $this;
    } 
    
    public function indexAction()
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('promo/ambanners/' . $this->_modelName . 's');
        $this->_addContent($this->getLayout()->createBlock('ambanners/adminhtml_' . $this->_modelName));         
         $this->renderLayout();
    }

    public function newAction()
    {
        $this->editAction();
    }
    
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('ambanners/' . $this->_modelName)->load($id);
        
        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ambanners')->__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        else {
            $this->prepareForEdit($model);
        }
        
        Mage::register('ambanners_' . $this->_modelName, $model);
        
        $this->loadLayout();
        
        $this->_setActiveMenu('promo/ambanners/' . $this->_modelName . 's');
        $this->_title($this->__('Edit'));
        
        $head = $this->getLayout()->getBlock('head');
        $head->setCanLoadExtJs(1);
        $head->setCanLoadRulesJs(1);
        
        $this->_addContent($this->getLayout()->createBlock('ambanners/adminhtml_' . $this->_modelName . '_edit'));
        $this->_addLeft($this->getLayout()->createBlock('ambanners/adminhtml_' . $this->_modelName . '_edit_tabs'));
        
        $this->
          _addJs(
			$this->getLayout()->createBlock('adminhtml/template')
                    ->setTemplate('ambanners/rule/js.phtml')
		)->renderLayout();
    }
    
	public function optionsAction()
    {
        $result = '<input id="attr_value" name="attr_value[]" value="" class="input-text" type="text" />';
        
        $code = $this->getRequest()->getParam('code');
        if (!$code){
            $this->getResponse()->setBody($result);
            return;
        }
        
        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($code);
        if (!$attribute){
            $this->getResponse()->setBody($result);
            return;            
        }

        if (!in_array($attribute->getFrontendInput(), array('select', 'multiselect')) ){
            $this->getResponse()->setBody($result);
            return;            
        }
        
        $options = $attribute->getFrontend()->getSelectOptions();
        //array_shift($options);  
        
        $result = '<select id="attr_value" name="attr_value[]" class="select">';
        foreach ($options as $option){
            $result .= '<option value="'.$option['value'].'">'.$option['label'].'</option>';      
        }
        $result .= '</select>';    
        
        $this->getResponse()->setBody($result);
    }      

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('ambanners/' . $this->_modelName);
        
        $data = $this->getRequest()->getPost();
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find an option to save'));
            $this->_redirect('*/adminhtml_filter/');
        }
        
       else {
        
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            
            unset($data['rule']);
            
            $path = Mage::getBaseDir('media') . DS . 'ambanners' . DS;
            $field = 'banner_img';
            if (!empty($_FILES[$field]['name'])) {
                try {  
                    $uploader = new Varien_File_Uploader($field);
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowRenameFiles(false);
                    
                    $fileName = $_FILES[$field]['name'];
                    $uploader->save($path, $fileName);
                    $data[$field] = $fileName;   
                } 
                catch(Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
                }  
            }
            
            try {
            	
            	/*
            	 *  Prepare dates and time for saving
            	 */ 
				$data = $this->_filterDates($data, array('from_date', 'to_date'));
				if (!empty($data['to_time'])) {
					$data['to_date'] = $data['to_date'] . ' ' . $data['to_time'];
				}
				
            	if (!empty($data['from_time'])) {
					$data['from_date'] = $data['from_date'] . ' ' . $data['from_time'];
				}
				
				
                $model->setData($data)->loadPost($data)->setId($id);
                $model->setFromDate($data['from_date']);
                $model->setToDate($data['to_date']);
                
                $this->prepareForSave($model);
                                                
                $model->save();
                
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $msg = Mage::helper('ambanners')->__($this->_title . ' has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }    
            return;
        }
    } 
    
    public function deleteAction()
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('ambanners/' . $this->_modelName)->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }
         
        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__($this->_title . ' has been successfully deleted'));
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }    
    
    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if (!$id) {
            $this->_getSession()->addError($this->__('Please select a rule to duplicate.'));
            return $this->_redirect('*/*');
        }
        
        try {
            $model  = Mage::getModel('ambanners/' . $this->_modelName)->load($id);
            if (!$model->getId()){
                $this->_getSession()->addError($this->__('Please select a rule to duplicate.'));
                return $this->_redirect('*/*');
            }

            $rule = clone $model;
            $rule->setIsActive(0);
            $rule->setId(null);
            $rule->save();
            
            $this->_getSession()->addSuccess(
                $this->__('The rule has been duplicated. Please feel free to activate it.')
            );
            return $this->_redirect('*/*/edit', array('id' => $rule->getId()));            
        } 
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect('*/*');
        } 
        
        //unreachable 
        return $this->_redirect('*/*'); 
    }       
        
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_modelName . 's');
        if (!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ambanners')->__('Please select records'));
             $this->_redirect('*/*/');
             return;
        }
        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('ambanners/' . $this->_modelName)->load($id);
                $model->delete();
                // TODO remove files
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($ids)
                )
            );
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');   
    }
    
    public function massActivateAction()
    {
        return $this->_modifyStatus(1);
    }
    
    public function massInactivateAction()
    {
        return $this->_modifyStatus(0);
    }     
    
    protected function _modifyStatus($status)
    {
        $ids = $this->getRequest()->getParam('rules');
        if ($ids && is_array($ids)){
            try {
                Mage::getModel('ambanners/' . $this->_modelName)->massChangeStatus($ids, $status);
                $message = $this->__('Total of %d record(s) have been updated.', count($ids));
                $this->_getSession()->addSuccess($message);
            } 
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        else {
            $this->_getSession()->addError($this->__('Please select rule(s).'));
        }
        
        return $this->_redirect('*/*');
    }     
    
    /**
     * Prepare model
     * @param Amasty_Banners_Model_Rule $model
     * @return boolean
     */
    public function prepareForSave($model)
    {
        $fields = array('stores', 'cust_groups', 'cats');
        foreach ($fields as $f){
            // convert data from array to string
            $val = $model->getData($f);
            $model->setData($f, '');
            if (is_array($val)){
            	// need commas to simplify sql query
            	$modelValue = '';
            	foreach ($val as $value) {
            		if ($value != '') {
            			$modelValue .= ',' . $value;
            		}
            	} 
            	if ($modelValue != '') {
                    $modelValue .= ',' . $value . ',';
            	}            	
				$model->setData($f, $modelValue);
            }
        }
        
		$ids = $model->getSelectedProducts();
		
        if (!is_null($ids)){
        	$ids = Mage::helper('adminhtml/js')->decodeGridSerializedInput($ids);
            $model->assignProducts($ids);                
		}
		
		$attributeCodes = $model->getData('attr_code');
		$attributeValues = $model->getData('attr_value');
		
		$validArray = array();
		
		foreach ($attributeValues as $index => $value) {
			if (isset($attributeCodes[$index]) && $attributeCodes[$index] != '') {
				if(!isset($validArray[$attributeCodes[$index]])) {
					$validArray[$attributeCodes[$index]] = array();
				}
				if ($value != '') {
					$validArray[$attributeCodes[$index]][] = $value;
				}
			}
		}
		$model->setData('attributes', serialize($validArray));
        
        return true;
    }
    
    public function prepareForEdit($model)
    {
        $fields = array('stores', 'cust_groups', 'cats');
        foreach ($fields as $f){
            $val = $model->getData($f);
            if (!is_array($val)){
                $model->setData($f, explode(',', $val));    
            }    
        }
        
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        return true;
    }
    
    public function newConditionHtmlAction()
    {
        $this->newConditions('conditions');
    }
    
    public function newConditions($prefix)
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        
        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('ambanners/rule'))
            ->setPrefix($prefix);
        
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);        
    }
    
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }       
    
    protected function _title($text = null, $resetIfExists = true)
    {
        if (Mage::helper('ambase')->isVersionLessThan(1,4)){
            return $this;
        }
        return parent::_title($text, $resetIfExists);
    }
}