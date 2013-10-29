<?php
/**
 * Celebros Qwiser - Magento Extension
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish correct extension functionality. 
 * If you wish to customize it, please contact Celebros.
 *
 * @category    Celebros
 * @package     Celebros_Conversionpro
 * @author		Celebros - Pavel Feldman (email: MagentoSupport@celebros.com)
 *
 */
class Celebros_Conversionpro_Block_Analytics_View extends Mage_Catalog_Block_Layer_View
{
	const CATALOG_CATEGORY_ATTRIBUTE_ENTITY_TYPE = '9';
	const CATALOG_PRODUCT_ATTRIBUTE_ENTITY_TYPE = '10';
	
	/**
	 * Sets parameters for tempalte
	 *
	 * @return Celebros_Conversionpro_Block_Analytics_View
	 */
	protected function _prepareLayout()
	{
		//running simulated search, to have the log handle down the page.
		//Mage::helper('conversionpro')->getCurrentLayer()->getProductCollection()->getFacetedData('');

		$this->setCustomerId(Mage::getStoreConfig('conversionpro/anlx_settings/cid'));
		$this->setHost(Mage::getStoreConfig('conversionpro/anlx_settings/host'));
		
		$product = $this->getProduct();
		//Set product click tracking params
		if(isset($product)){
			$this->setProductSku($product->getSku());
			$this->setProductName(str_replace("'", "\'", $product->getName()));
			$this->setProductPrice($product->getFinalPrice());
			$webSessionId = isset($_SESSION['core']['visitor_data']['session_id']) ? $_SESSION['core']['visitor_data']['session_id'] : session_id();
			$this->setWebsessionId($webSessionId);		
		}
		//Set search tracking params
		else {
			$pageReferrer = Mage::getModel('core/url')->getBaseUrl() . $_SERVER['PHP_SELF'];
			$this->setPageReferrer($pageReferrer);
			//$this->setQwiserSearchSessionId(Mage::getSingleton('conversionpro/session')->getSearchSessionId());
			$this->setQwiserSearchSessionId($this->_generateGUID());
			$webSessionId = isset($_SESSION['core']['visitor_data']['session_id']) ? $_SESSION['core']['visitor_data']['session_id'] : session_id();
			$this->setWebsessionId($webSessionId);
			if (Mage::Helper('conversionpro')->hasSearchResults()) {
				$this->setQwiserSearchLogHandle(Mage::Helper('conversionpro')->getSearchResults()->GetLogHandle());
			}
		}
		
		return parent::_prepareLayout();
	}
	
	protected function _generateGUID()
	{
		global $SERVER_ADDR;

		// get the current ip, and convert it to its positive long value
		$long_ip = ip2long($SERVER_ADDR);
		if($long_ip < 0) $long_ip += pow(2,32);

		// get the current microtime and make sure it's a positive long value
		$time = microtime();
		if($time < 0)
		{
			$time += pow(2,32);
		}

		// put those strings together
		$combined = $long_ip . $time;

		// md5 it and throw in some dashes for easy checking
		$guid = md5($combined);
		$guid = substr($guid, 0, 8) . "-" .
		substr($guid, 8, 4) . "-" .
		substr($guid, 12, 4) . "-" .
		substr($guid, 16, 4) . "-" .
		substr($guid, 20);

		return $guid;
	}
	
	/**
	 * Retrieve current product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	public function getProduct()
	{
		return Mage::registry('product');
	}
}