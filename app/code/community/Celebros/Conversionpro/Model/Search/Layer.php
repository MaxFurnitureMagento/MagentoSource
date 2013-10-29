<?php
class Celebros_Conversionpro_Model_Search_Layer extends Mage_CatalogSearch_Model_Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function getProductCollection()
    {
        if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
			if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
				$collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
			} else {
				//@todo switch to our helper.
				$collection = Mage::helper('catalogsearch')->getEngine()->getResultCollection();
				$collection->setStoreId($this->getCurrentCategory()->getStoreId());
				$this->prepareProductCollection($collection);
				$this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
			}

			return $collection;
		} else {
			return parent::getProductCollection();
		}
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
			$additionalTags = array_merge($additionalTags, array(
				Mage_Catalog_Model_Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
			));
		}

        return parent::getStateTags($additionalTags);
    }
}
