<?php 
class WebMods_Solrsearch_Model_Collection extends WebMods_Solrsearch_Model_Resource_Collection_Abstract
{
	public function __construct($resource=null)
	{
		parent::__construct($resource);
	} 
	
	/**
     * Specify category filter for product collection
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
        $this->_productLimitationFilters['category_id'] = $category->getId();
        if ($category->getIsAnchor()) {
            unset($this->_productLimitationFilters['category_is_anchor']);
        } else {
            $this->_productLimitationFilters['category_is_anchor'] = 1;
        }
		
        
        if ($this->getStoreId() == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            //FIXME: apply no store
        	//$this->_applyZeroStoreProductLimitations();
        } else {
            //FIXME: apply stores
        	//$this->_applyProductLimitations();
        }

        return $this;
    }

	
	
	/**
     * Fetch collection data
     * This function execute an sql query and return an array of rows
     * @param   Zend_Db_Select $select
     * @return  array
     */
    protected function _fetchAll($select)
    {
    	
    	if ($this->_canUseCache()) {
            $data = $this->_loadCache($select);
            if ($data) {
                $data = unserialize($data);
            } else {
                $data = $this->getConnection()->fetchAll($select, $this->_bindParams);
                $this->_saveCache($data, $select);
            }
        } else {
            $data = $this->getConnection()->fetchAll($select, $this->_bindParams);
        }
       print_r($data);
       return $data;
       
    }
}
?>