<?php
class Celebros_Conversionpro_Model_Observer
{
    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
		//First reset the registry variable so that you won't get an error when trying to re-assign a value.
		Mage::unregister('current_layer');

		//If Conversionpro's disabler is activated, we'll run the default Magento search no matter what.
		$status = Mage::getSingleton('conversionpro/session')->getConversionproDisabler();
		if ($status && $status == true) {
			return;
		}
		
		//Now, check if conversionpro is enabled, and if the chosen category isn't blacklisted for nav2search.
		if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
			Mage::register('current_layer', Mage::getSingleton('conversionpro/catalog_layer'));
			return;
        }

		//If Conversionpro didn't work out, check if Solr is available and if so then use it.
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if (in_array('Enterprise_Search', $modules)) {
			if (Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation()) {
				//We're not registering the solr search layer, because there's a separate enterprise observer for that.
				//Mage::register('current_layer', Mage::getSingleton('enterprise_search/catalog_layer'));
				return;
			}
		}
		//If all else fails, revert to the default search engine's layer.
		Mage::register('current_layer', Mage::getSingleton('catalog/layer'));
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
		Mage::unregister('current_layer');
		
		//If Conversionpro's disabler is activated, we'll run the default Magento search no matter what.
		$status = Mage::getSingleton('conversionpro/session')->getConversionproDisabler();
		if ($status && $status == true) {
			return;
		}
		
		if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation(false)) {
            Mage::register('current_layer', Mage::getSingleton('conversionpro/search_layer'));
        } else {
			$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
			if (in_array('Enterprise_Search', $modules)) {
				if (Mage::helper('enterprise_search')->getIsEngineAvailableForNavigation(false)) {
					//We're not registering the solr search layer, because there's a separate enterprise observer for that.
					//Mage::register('current_layer', Mage::getSingleton('enterprise_search/search_layer'));
				} else {
					Mage::register('current_layer', Mage::getSingleton('catalogsearch/layer'));
				}
			} else {
				Mage::register('current_layer', Mage::getSingleton('catalogsearch/layer'));
			}
		}
    }
	
	/**
	 * Instantiate analytics block after layout has been rendered, 
	 *  so that it'll contain the current log handle & session id.
	 */
	public function initAnalytics(Varien_Event_Observer $observer)
	{
        $_block = $observer->getBlock();
        $_type = $_block->getType();

        if ($_type == 'catalogsearch/result' || $_type == 'catalogsearch/advanced_result' || $_type == 'catalog/category_view') {
            if (Mage::helper('conversionpro')->hasSearchResults()) { 
				$layout = Mage::getSingleton('core/layout');
				
				$block = $layout
					->createBlock('conversionpro/analytics_view')
					->setTemplate('conversionpro/analytics/tracking.search.phtml');

				$layout->getBlock('before_body_end')->append($block);
			}
        }
	}
}
