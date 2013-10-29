<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
 
class Amasty_Banners_Model_Rule extends Mage_Rule_Model_Rule
{
    const POS_ABOVE_CART    = 0;
    const POS_SIDEBAR_RIGHT = 1;
    const POS_SIDEBAR_LEFT  = 2;
    const POS_PROD_PAGE     = 3;
    const POS_CATEGORY_PAGE = 4;
    const POS_CATEGORY_PAGE_BOTTOM = 5;
	const POS_PROD_PAGE_BOTTOM = 6;
    const POS_PROD_PAGE_BELOW_CART = 7;
    const POS_CHECKOUT_BELOW_TOTAL = 8;

    const POS_HOME_ABOVE_MENU = 20;

    
    /*
     * Display Types
     */
    const TYPE_IMAGE = 'image';
    const TYPE_CMS = 'cms';
    const TYPE_HTML = 'html';
    const TYPE_PRODUCTS = 'products';
    
    /*
     * Products List Options
     */
    const SHOW_PRODUCTS_NO = 0;
    const SHOW_PRODUCTS_YES = 1;
     
    public function _construct()
    {
        parent::_construct();
        $this->_init('ambanners/rule');
    }
    
    public function getConditionsInstance()
    {
        return Mage::getModel('ambanners/rule_condition_combine');
    }
    
    public function massChangeStatus($ids, $status)
    {
        return $this->getResource()->massChangeStatus($ids, $status);
    }
    
    /**
     * Initialize rule model data from array
     *
     * @param   array $rule
     * @return  Mage_SalesRule_Model_Rule
     */
     
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        
        return $this;
    }  
    
    /**
     * Get array of attributes for product where banner should be shown 
     * @return array
     */
    public function getAttributesAsArray()
    {
    	$array = array();
    	$attributes = $this->getData('attributes');
    	if (!empty($attributes)) {
    		$array = unserialize($attributes);
    	}
    	return $array;
    } 
    
    public function match($rate)
    {
        return false;
    }
   
    
    protected function _afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized())
        );
        if (count($ruleProductAttributes)) {
            $this->getResource()->saveAttributes($this->getId(), $ruleProductAttributes);
        } 
        
        return parent::_afterSave(); 
    } 
    
    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = array();
        
        $pattern = '~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s';
        $matches = array();
        if (preg_match_all($pattern, $serializedString, $matches)){
            foreach ($matches[1] as $attributeCode) {
                $result[] = $attributeCode;
            }
        }
        return $result;
    }    
    
    public function getProducts($ruleId)
    {
        return $this->getResource()->getProducts($ruleId);    
    }
    
    public function assignProducts($productIds)
    {
        $this->getResource()->assignProducts($this->getId(), $productIds);
        return $this;         
    }
    
    
}