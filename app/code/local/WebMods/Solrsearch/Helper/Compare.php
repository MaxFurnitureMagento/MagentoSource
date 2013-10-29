<?php
class WebMods_Solrsearch_Helper_Compare extends Mage_Catalog_Helper_Product_Compare{
	public $currentUrl = null;
	protected function _getUrlParams($product)
    {
    	return array(
            'product' => $product->getId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($this->currentUrl)
        );
    }
	public function getAddUrl($product)
    {
        return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
    }
    public function setCurrentUrl($url){
    	$this->currentUrl = $url;
    }
}