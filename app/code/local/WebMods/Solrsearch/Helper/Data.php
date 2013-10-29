<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
	const QUERY_VAR_NAME = 'q';
	const FILTER_QUERY_VAR_NAME = 'fq';
	public function getQueryParamName()
    {
        return self::QUERY_VAR_NAME;
    }
	public function getFilterQueryParamName()
    {
        return self::FILTER_QUERY_VAR_NAME;
    }
	public function getResultUrl($query = null,$filterQuery = null)
    {
        return $this->_getUrl('solrsearch', array(
            '_query' => array(self::QUERY_VAR_NAME => $query, self::FILTER_QUERY_VAR_NAME=>$filterQuery),
            '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
        ));
    }
	
    public function getSettings($key){
    	$value = '';
    	$settings = Mage::getStoreConfig('webmods_solrsearch/settings', Mage::app()->getStore()->getId());
    	
    	if (isset($settings[$key])) {
    		$value = $settings[$key];
    	}
    	return $value;
    }
 
    /**
     * Retrieve HTML escaped search query
     *
     * @return string
     */
    public function getEscapedQueryText()
    {
    	$solrModel = Mage::getModel('solrsearch/solr');
    	$queryText = $solrModel->getParams('q');
    	if( isset($solrData['responseHeader']['params']['q']) && !empty($solrData['responseHeader']['params']['q']) ) {
    		if ($queryText != $solrData['responseHeader']['params']['q']) {
    			$queryText = $solrData['responseHeader']['params']['q'];
    		}
    	}
    	return $this->escapeHtml($queryText);
    }
    /**
     * Get parameters value
     * @return array
     */
	public function getParams() {
 		$queryString = $_SERVER['QUERY_STRING'];
 		$params = array();
		parse_str($queryString, $params);
		
		if (isset($_POST)) {
			$params = array_merge($params, $_POST);
		}

		return $params;
    }
    
    /**
     * Get parameter value
     * @param $key
     * @return mixed
     */
    public function getParam($key) {
    	$params = $this->getParams();
    	$returnValue = '';
    	if (!empty($key) && isset($params[$key]) && !empty($params[$key])) {
    		$returnValue = $params[$key];
    	}
    
    	return $returnValue;
    }
}