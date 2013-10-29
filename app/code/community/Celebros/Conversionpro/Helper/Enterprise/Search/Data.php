<?php

class Celebros_Conversionpro_Helper_Enterprise_Search_Data extends Enterprise_Search_Helper_Data
{
	/**
     * Check if search engine can be used for catalog navigation
     *
     * @param   bool $isCatalog - define if checking availability for catalog navigation or search result navigation
     * @return  bool
     */
    public function getIsEngineAvailableForNavigation($isCatalog = true)
    {
		if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation($isCatalog)) {
			return false;
		}
		return parent::getIsEngineAvailableForNavigation($isCatalog);
    }
	
	/**
     * Return true if third party search engine is used
     *
     * @return bool
     */
    public function isThirdPartSearchEngine()
    {
        if (Mage::helper('conversionpro')->getIsEngineAvailable()) {
			return false;
		}
		
		return parent::isThirdPartSearchEngine();
    }
	
	/**
     * Check if enterprise engine is available
     *
     * @return bool
     */
    public function isActiveEngine()
    {
        if (Mage::helper('conversionpro')->getIsEngineAvailable()) {
			return false;
		}
		
		return parent::isActiveEngine();
    }
}