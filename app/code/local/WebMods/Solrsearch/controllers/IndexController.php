<?php
/**
 * @category SolrBridge
 * @package Webmods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction() {
    	Mage::getSingleton('core/session')->setSolrFilterQuery(array());
		
		$layout = $this->getLayout();
    	$this->loadLayout();
    	
    	$resultBlock = $layout->getBlock('searchresult');
    	
    	$facetsBlock = $layout->getBlock('searchfaces');
		
    	$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $solrModel->buildRequestUrl($store);
    	$solrData = $solrModel->doRequest($url, $store);
    	
    	$resultBlock->setData('solrdata', $solrData);
    	$facetsBlock->setData('solrdata', $solrData);
    	
    	$queryText = $solrModel->getParams('q');
		if( isset($solrData['responseHeader']['params']['q']) && !empty($solrData['responseHeader']['params']['q']) ) {
        	if ($queryText != $solrData['responseHeader']['params']['q']) {
        		$queryText = $solrData['responseHeader']['params']['q'];
        	}
        }
    	
    	$facetsBlock->setData('querytext', $queryText);
    	
    	if (Mage::helper('solrsearch')->getSettings('allow_multiple_filter') > 0)
    	{
    		$this->saveLayerData($solrData, $queryText);
    	}
    	
    	$params = $this->getRequest()->getParams();
    	$filterQuery = (array)Mage::getSingleton('core/session')->getSolrFilterQuery();
    	if (isset($params['fq'])){
    		$filterQuery[] = $params['fq'];
    	}
    	if (isset($params['clear']) && $params['clear'] == 'yes') $filterQuery = array();
    	Mage::getSingleton('core/session')->setSolrFilterQuery(array_unique($filterQuery));
    	$this->renderLayout();
    }
    
    /**
     * Save facet data in session for multiple selection
     */
    protected function saveLayerData($solrData, $queryText)
    {
    	
    	$key = sha1('solrbridge_solrsearch_'.$queryText);
    	
    	$originalSolrData = Mage::getSingleton('core/session')->getOriginSolrFacetData();
    	
    	if (!isset($originalSolrData) || !isset($originalSolrData[$key])) {
    		$data = array($key => $solrData);
    		 
    		Mage::getSingleton('core/session')->setOriginSolrFacetData($data);
    	}
    }
}