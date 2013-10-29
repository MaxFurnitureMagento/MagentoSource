<?php
class Celebros_Conversionpro_Block_Catalog_Product_List_Upsell extends Mage_Catalog_Block_Product_List_Upsell
{
	
	/**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 4;
	
	protected $_items;

    protected $_itemCollection;
	
	/**
	 * Get crosssell items
	 *
	 * @return array
	 */
	public function getItemCollection()
	{
		if (!Mage::helper('conversionpro')->isActiveEngine()
			|| !Mage::getStoreConfigFlag('conversionpro/crosssell_settings/upsell_enabled')) {
			
			return parent::getItemCollection();
		}
		
		$items = $this->_items;
		if (is_null($items)) {
		
			reset($this->_itemCollection);
		
			$lastAdded = null;
		
			//This code path covers the 2 cases - product page and shopping cart
			if($this->getProduct()!=null){
				$lastAdded = $this->getProduct()->getId();
			}
			else{
				$cartProductIds = $this->_getCartProductIds();
				$lastAdded = null;
				for($i=count($cartProductIds) -1; $i >=0 ; $i--){
					$id =  $cartProductIds[$i];
					$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
					if(empty($parentIds)){
						$lastAdded = $id;
						break;
					}
				}
			}
		


			$crossSellIds = Mage::helper('conversionpro')->getSalespersonCrossSellApi()->getRecommendationsIds($lastAdded);

			$this->_maxItemCount = Mage::getStoreConfig('conversionpro/crosssell_settings/upsell_limit');
			
			$this->_itemCollection = $this->_getCollection()
				->addAttributeToFilter('entity_id', array('in' => $crossSellIds,));
		}

		return $this->_itemCollection;
	}
	
	/**
	 * Get crosssell products collection
	 */
	protected function _getCollection()
	{
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->setStoreId(Mage::app()->getStore()->getId())
			->addStoreFilter()
			->setPageSize($this->_maxItemCount);
		$this->_addProductAttributesAndPrices($collection);
	
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
	
		return $collection;
	}

}