<?php
class Celebros_Conversionpro_Model_Product extends Celebros_Conversionpro_Model_Api_QwiserProduct
{
	/**
	 * Initialize resources
	 */
	protected function _construct()
	{
		$this->_init('conversionpro/product');
	}

	/**
	 * Retrive product by SKU
	 * 
	 * @param string $productSku
	 * @return Celebros_Conversionpro_Model_Api_QwiserProduct
	 */
	public function load($productSku){
		$products = Mage::Helper("conversionpro")->getSalespersonApi()->results->Products->Items;
		foreach ($products as $product)
		{
			if($product->Sku = $productSku){
				return $product;
			}
		}
	}
	/**
	 * Retrive QwiserSearchResult
	 * 
	 * @return Celebros_Conversionpro_Model_Api_QwiserSearchResult
	 */
	protected function getQwiserSearchResults(){
    	if(Mage::helper('conversionpro')->getSalespersonApi()->results)
    		return Mage::helper('conversionpro')->getSalespersonApi()->results;
    }
    
	/**
	* Retrieve Store Id of the product
	*
	* @return int
	*/
	public function getStoreId()
	{
		if (key_exists(Mage::Helper('conversionpro/mapping')->getMapping('store_id'),$this->Field)) {
			return $this->Field(Mage::Helper('conversionpro/mapping')->getMapping('store_id'));
		}
		return Mage::app()->getStore()->getId();
	}

	/**
	 * Get product url model
	 *
	 * @return Mage_Catalog_Model_Product_Url
	 */
	public function getUrlModel()
	{
		if ($this->_urlModel === null) {
			$this->_urlModel = Mage::getSingleton('catalog/product_url');
		}
		return $this->_urlModel;
	}


	/**
	 * Get product name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Field(Mage::Helper('conversionpro/mapping')->getMapping('title'));
	}

	/**
	 * Get product status
	 *
	 * @return int
	 */
	public function getStatus()
	{
		return $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('status')] == 'Enabled';
	}

	public function getInStock()
	{
		return key_exists(Mage::Helper('conversionpro/mapping')->getMapping('is_in_stock'), $this->Field) && (int)$this->Field[Mage::Helper('conversionpro/mapping')->getMapping('is_in_stock')] == 0 ? true : false;
	}
	
	public function isSaleable()
	{
		return key_exists(Mage::Helper('conversionpro/mapping')->getMapping('is_salable'), $this->Field) && (int)$this->Field[Mage::Helper('conversionpro/mapping')->getMapping('is_salable')] == 0 ? false : true;
	}

	public function getSku()
	{
		return key_exists(Mage::Helper('conversionpro/mapping')->getMapping('sku'),$this->Field) ? $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('sku')] : false;
	}
	
	/**
	 * Retrive Product Id
	 * @return string
	 */
	public function getId() {
		return $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('id')];
	}

	/**
	 * Retrieve assigned category Ids
	 *
	 * @return array
	 */
	public function getCategory()
	{
		return key_exists(Mage::Helper('conversionpro/mapping')->getMapping('category'),$this->Field) ? $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('category')] : false;
	}

	/**
	 * Retrieve product websites identifiers
	 *
	 * @return array
	 */
	public function getWebsiteIds()
	{
		if (strpos($this->Field[Mage::Helper('conversionpro/mapping')->getMapping('websites')],',')){
			$websitesIds = explode(",", $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('websites')]);
			return $websitesIds;
		}
		else {
			return array($this->Field[Mage::Helper('conversionpro/mapping')->getMapping('websites')]);
		}
	}

	/**
	 * Get all sore ids where product is presented
	 *
	 * @return array
	 */
	public function getStoreIds()
	{
		if (strpos($this->Field[Mage::Helper('conversionpro/mapping')->getMapping('store_id')],',')){
			$storeIds = explode(",", $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('store_id')]);
			return $storeIds;
		}
		else {
			return array($this->Field[Mage::Helper('conversionpro/mapping')->getMapping('store_id')]);
		}
	}

	/**
	 * Retrive the Rating precent
	 * 
	 * @return string
	 */
	public function getRatingSummary()
	{
		return $this->Field[$this->Field[Mage::Helper('conversionpro/mapping')->getMapping('rating_summary')]]; //_getData('rating_summary');
	}
	/**
	 * Retrieve product found in
	 *
	 * @return category with link
	 */
	public function getAvailableInCategories()
	{
		if(key_exists(Mage::Helper('conversionpro/mapping')->getMapping('category'), $this->Field) && $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('category')] != ''){
			$categories = strpos($this->Field[Mage::Helper('conversionpro/mapping')->getMapping('category')],',') 
									? explode(',',$this->Field[Mage::Helper('conversionpro/mapping')->getMapping('category')]) 
									: $this->Field[Mage::Helper('conversionpro/mapping')->getMapping('category')];
			if (is_array($categories)){
				$urls = array();
				foreach ($categories as $category){
					$urlParams = array();
					$urlParams['_current']  = false;
					$urlParams['_escape']   = true;
					$urlParams['_use_rewrite']   = false;
					$urlParams['_query']    = array(
		        	'q' => $category,
					);
					$urls[$category] = Mage::getUrl('*/*/index', $urlParams);
				}
				return $urls;
			}
			else {
				$urlParams = array();
				$urlParams['_current']  = false;
				$urlParams['_escape']   = true;
				$urlParams['_use_rewrite']   = false;
				$urlParams['_query']    = array(
	        	'q' => $categories,
				);
				return array($categories => Mage::getUrl('*/*/index', $urlParams));
			}
		}
	}
}
