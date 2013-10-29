<?php
class Celebros_Conversionpro_Block_Checkout_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
	/**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 4;
	
	/**
	 * Get crosssell items
	 *
	 * @return array
	 */
	public function getItems()
	{
		if (!Mage::helper('conversionpro')->isActiveEngine()
			|| !Mage::getStoreConfigFlag('conversionpro/crosssell_settings/crosssell_enabled')) {
			
			return parent::getItems();
		}
		
		$items = $this->getData('items');
		if (is_null($items)) {
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

			$this->_maxItemCount = Mage::getStoreConfig('conversionpro/crosssell_settings/crosssell_limit');
			
			$items = $this->_getCollection()
				->addAttributeToFilter('entity_id', array('in' => $crossSellIds,));
		}

		$this->setData('items', $items);
		$this->_itemCollection = $items;
		return $items;
	}
}