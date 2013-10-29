<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAllGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('salesrule')->__('NOT LOGGED IN')));
        } 
        
        return $customerGroups;
    }

    public function getStatuses()
    {
        return array(
            '0' => $this->__('Inactive'),
            '1' => $this->__('Active'),
        );       
    }

    public function getPosition()
    {
        $a = array(
            Amasty_Banners_Model_Rule::POS_ABOVE_CART => $this->__('Above cart'),
            Amasty_Banners_Model_Rule::POS_SIDEBAR_RIGHT => $this->__('Sidebar-Right'),
            Amasty_Banners_Model_Rule::POS_SIDEBAR_LEFT => $this->__('Sidebar-Left'),
            Amasty_Banners_Model_Rule::POS_PROD_PAGE  => $this->__('Product page (Top)'),
			Amasty_Banners_Model_Rule::POS_PROD_PAGE_BOTTOM => $this->__('Product Page (Bottom)'),
			Amasty_Banners_Model_Rule::POS_PROD_PAGE_BELOW_CART => $this->__('Product page (Below Cart Button)'),
            Amasty_Banners_Model_Rule::POS_CATEGORY_PAGE => $this->__('Category page (Top)'),
            Amasty_Banners_Model_Rule::POS_CATEGORY_PAGE_BOTTOM => $this->__('Category page (Bottom)'),            
            Amasty_Banners_Model_Rule::POS_CHECKOUT_BELOW_TOTAL => $this->__('Checkout page (Below Cart Total)'),
            Amasty_Banners_Model_Rule::POS_HOME_ABOVE_MENU => $this->__('Home page above menu')
		
        );
        return $a;       
    }
    
	public function showProductsListOptions()
    {
    	return array(
    		Amasty_Banners_Model_Rule::SHOW_PRODUCTS_NO => $this->__('No'),
    		Amasty_Banners_Model_Rule::SHOW_PRODUCTS_YES => $this->__('Yes'),
    	);
    }
    
    public function getBannerTypes()
    {
    	return array(
    		Amasty_Banners_Model_Rule::TYPE_IMAGE => $this->__('Image'),
    		Amasty_Banners_Model_Rule::TYPE_CMS => $this->__('CMS Block'),
    		Amasty_Banners_Model_Rule::TYPE_HTML => $this->__('HTML text'),
    	);
    }
    
    public function getBlock()
    {
        $a = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
        );
        return $a;       
    }
}