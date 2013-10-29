<?php

class Celebros_Conversionpro_Helper_Catalogsearch_Data extends Mage_CatalogSearch_Helper_Data
{
    /**
     * Get current search engine resource model
     *
     * @return object
     */
    public function getEngine()
    {
        if (Mage::helper('conversionpro')->isActiveEngine()) {
			return Mage::helper('conversionpro')->getSearchEngine();
		}
		
		return parent::getEngine();
    }
}