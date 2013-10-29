<?php
class Celebros_Conversionpro_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
    /**
     * Retrieve current layer product collection
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function getProductCollection()
    {
		if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
			if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
				$collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
			} else {
				$answerId = Mage::helper('conversionpro')->getAnswerIdByCategoryId($this->getCurrentCategory()->getId());
				//@todo switch to our helper.
				$collection = Mage::helper('catalogsearch')->getEngine()->getResultCollection();
				$collection->setStoreId($this->getCurrentCategory()->getStoreId());
				
				//We have two options here that correspond to the two methods of using nav2search - query strings and answer ids.
				if (Mage::helper('conversionpro')->getCelebrosConfigData('nav_to_search_settings/nav_to_search_search_by') == 'answer_id') {
					//This option adds a filter with the category\'s answer id.
					$collection->addFqFilter(array('category' => $answerId));
					//If we're adding an answer id, and not changing the query string, we'll need to add the default query string
					// to the collection's base parameters for the call to Quiser's API.
					$collection->setGeneralDefaultQuery();
				} else {
					//This option adds a search query string with the name of the category.
					$query = Mage::helper('conversionpro')->getCategoryRewriteQuery($this->getCurrentCategory());
					$collection->addSearchParam(null, $query);
				}
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
	 *
	 * @todo find out why this is necessary.
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
