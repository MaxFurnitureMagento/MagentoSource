<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Container extends Mage_Catalog_Block_Product_Abstract
{    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ambanners/container.phtml');
    } 

    public function getCurrentPosition()
    {
        $pos = $this->getPosition();
        
        $hlp = Mage::helper('ambanners');
        $possibleValues = $hlp->getPosition();
        if (empty($possibleValues[$pos])){
            sort($possibleValues);
            echo $hlp->__('Error: Invalid value for the banner position: `%s`.', $pos);
            echo $hlp->__('You can use the following integer values: %s.', implode(',', $possibleValues));
            exit;
        }
        
        return $pos;
    }
    
    protected function _getBannersCollection()
    {
        $now = Mage::getModel('core/date')->date();
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $storeId =  Mage::app()->getStore()->getStoreId();
        
        
        $nullValue = array('');
        $dateNull = array('null' => 'true');
       
        $collection = Mage::getModel('ambanners/rule')
            ->getCollection()
            ->addFieldToFilter('banner_position', $this->getCurrentPosition())            
            ->addFieldToFilter('from_date', array($dateNull, array("lteq" => $now)))
            ->addFieldToFilter('to_date', array($dateNull, array("gteq" => $now)))
            ->addFieldToFilter('cust_groups', array($nullValue, array("like"=>"%,".$groupId.",%")))
            ->addFieldToFilter('stores', array($nullValue, array("like"=>"%,".$storeId.",%")))
            ->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'asc');
            
        $currentCategory = '';
        
        /*
         * Category Banners
         */
        $pos = array(Amasty_Banners_Model_Rule::POS_SIDEBAR_LEFT, Amasty_Banners_Model_Rule::POS_SIDEBAR_RIGHT, Amasty_Banners_Model_Rule::POS_CATEGORY_PAGE, Amasty_Banners_Model_Rule::POS_CATEGORY_PAGE_BOTTOM);
    	if (in_array($this->getCurrentPosition(), $pos)	&& Mage::registry('current_category')) {
            $currentCategory = Mage::registry('current_category')->getId(); 
            $collection->addFieldToFilter('cats', array($nullValue, array("like"=>"%,".$currentCategory.",%")));
            if (isset($_GET['debug'])) {
                echo $collection->getSelect();
            }
        }
        
        return $collection; 
    }
    
    /**
     * Perform internal banner validation
     * @param Amasty_Banners_Model_Rule $banner
     * @return boolean
     */
    public function bannerValidate($banner)
    {
    	$pos = array(Amasty_Banners_Model_Rule::POS_SIDEBAR_LEFT, Amasty_Banners_Model_Rule::POS_SIDEBAR_RIGHT, Amasty_Banners_Model_Rule::POS_PROD_PAGE);
    	if ($product = Mage::registry('current_product')) {
            if (in_array($this->getCurrentPosition(), $pos)) {
                
                $sku = $product->getSku(); 
                
                /*
                 * Filter By SKU
                 */
                $allowed = $banner->getShowOnProducts();
                if (!empty($allowed)) {            	
                    $skus = explode(",", trim($allowed));
                    if (!in_array($sku, $skus)) {
                        return false;
                    }
                }
                
                /*
                 * Filter By Category
                 */
                $cats = $banner->getCats();                 
                if (!empty($cats)) {            	
                    $cats = explode(",", trim($cats));
                    //$catid = $product->getCategory()->getId();
                    $categoryIds = $product->getCategoryIds();
                    $hasCategory = false;
                            $hasValue = false;
                    if (is_array($categoryIds) and count($categoryIds) > 1) {
                    
                        foreach ($categoryIds as $catid){
                            
                            foreach ($cats as $id) {
                                if ($id != '') {
                                    $hasValue = true || $hasValue;
                                    if ($id == $catid) {
                                        $hasCategory = true || $hasCategory;
                                    }
                                }
                            }
                            
                        }
                       
                    }
                    
                    
                    
                     if ($hasValue && !$hasCategory) {
                                return false;
                            }                            
                        
                    
                    

                    
                    
                }
                
                /*
                 * Filter By Product Attributes
                 */
                $productOptions = $banner->getAttributesAsArray();
                
                if (count($productOptions) > 0) {
                    /*
                     * Use AND or OR logic 
                     */
                    $logic = 'AND';
                    $matches = 0;
                    $total = 0;
                    foreach ($productOptions as $attributeCode => $options) {
                        if ($product->getData($attributeCode)) {
                                $attrCodes = explode(",",$product->getData($attributeCode));
                            if (array_intersect($attrCodes, $options)) {

                                $matches++;
                            }
                            $total++;
                        }
                    }
                    
        
                    if ($logic == 'AND') {
                        return ($total == $matches && $total != 0);
                    }
                    if ($logic == 'OR') {
                        return $matches>0;
                    }
                }
            }
        } else {            
            if (count($banner->getAttributesAsArray()) > 0) {
                return false;
            }
        }
        return true;
    }
    
    public function getBanners()
    {
        $cart = Mage::getSingleton('checkout/session');
        $results = array();
        
        $collection = $this->_getBannersCollection();
        $address = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        
        $collection->load(); 
        foreach ($collection as $rule){
            $rule->afterLoad(); 
        }
        /*
         * Holds banner products
         */
        $bannerProducts = array();

       
        foreach ($collection as $banner) {  
            
            if (!$this->bannerValidate($banner)) {
            	continue;
            }                

            if (!$banner->validate($address))
                continue;

            if ($banner->getBannerType() == Amasty_Banners_Model_Rule::TYPE_IMAGE) {    
            	$banner->setBannerImg(Mage::getBaseUrl('media') . 'ambanners/' . $banner->getBannerImg());
            }
            if ($banner->getBannerType() == Amasty_Banners_Model_Rule::TYPE_CMS) {            
	            $blockName = $banner->getCmsBlock();
	            if ($blockName){
	                $banner->setBlockHtml(
	                    $this->getLayout()
	                        ->createBlock('cms/block')
	                        ->setBlockId($blockName)
	                        ->toHtml()
	                );
	            }  
            }
            if ($banner->getShowProducts() == Amasty_Banners_Model_Rule::SHOW_PRODUCTS_YES) {
            	
            	$productIds = Mage::getModel('ambanners/rule')
            		->getResource()
            		->getProducts($banner->getRuleId());

            		
				$layer = Mage::getSingleton('catalog/layer');
				$currentCategory = $layer->getCurrentCategory(); 
		        $categoryId = Mage::app()->getStore()->getRootCategoryId();
		        
		        if ($categoryId) {
		        	$category = Mage::getModel('catalog/category')->load($categoryId);
		        	if ($category) { 
		        		$layer->setCurrentCategory($category);
		        	}
		        }
		        
		        $collection = $category->getProductCollection();        
		        $layer->prepareProductCollection($collection);
    
		        $collection
		        	->addStoreFilter()
		        	->addAttributeToFilter('entity_id', array('in' => array_values($productIds)));
		        	
		        /*
		         * Restore Category To its current State
		         */	
				$layer->setCurrentCategory($currentCategory);            		 
            	$bannerProducts[$banner->getRuleId()] = $collection->load();
            }
            $results[] = $banner;     
        }
        if ($bannerProducts) {
			Mage::register('ambanners_banner_products', $bannerProducts, true);
        }  
        return $results;
    }    
}

