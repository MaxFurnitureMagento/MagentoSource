<?php
/**
 * @category SolrBridge
 * @package Webmods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_AjaxController extends Mage_Core_Controller_Front_Action
{
	protected $ultility = null;
	
	public function queryAction()
	{
		$queryText = Mage::helper('solrsearch')->getParam('q');
		
		$cachedKey = 'solrbridge_solrsearch_autocomplete_keyword_'.Mage::app()->getStore()->getId().'_'.Mage::app()->getStore()->getWebsiteId().'_'.sha1($queryText);
		
		if (false !== ($returnData = Mage::app()->getCache()->load($cachedKey))) {
			$returnData = unserialize($returnData);
		} else {
			
			$returnData = $this->doRequest($queryText);
				
			if (!isset($returnData['response']['numFound']) || intval($returnData['response']['numFound']) < 1){
				if (isset($returnData['spellcheck']['suggestions']['collation']) && !empty($returnData['spellcheck']['suggestions']['collation'])) {
					$_GET['mm'] = '0%';
					$returnData = $this->doRequest($returnData['spellcheck']['suggestions']['collation']);
					
				}
					
			}
				
			$returnData['keywordssuggestions'] = array();
				
			if (isset($returnData['responseHeader']['params']['q'])) {
					
				if (isset($returnData['spellcheck']['suggestions'])){
					foreach ($returnData['spellcheck']['suggestions'] as $key=>$suggestion) {
						if (isset($suggestion['suggestion']) && is_array($suggestion['suggestion']) && count($suggestion['suggestion'])) {
							foreach ($suggestion['suggestion'] as $word) {
								if (!in_array(trim($word, ','), $returnData['keywordssuggestions'])) {
									$returnData['keywordssuggestions'][] = trim($word, ',');
								}
							}
						}else if ($key == 'collation') {
							if (!in_array(trim($suggestion, ','), $returnData['keywordssuggestions'])) {
								$returnData['keywordssuggestions'][] = trim($suggestion, ',');
							}
						}
					}
				}
			}
			
			Mage::app()->getCache()->save(serialize($returnData), $cachedKey);
			
			
		}
		
		$this->getResponse()->setHeader("Content-Type", "text/javascript", true);
		
		$jsonp_callback = isset($_GET['json_wrf']) ? $_GET['json_wrf'] : null;
		
		if (isset($_GET['timestamp'])) {
			$returnData['responseHeader']['params']['timestamp'] = $_GET['timestamp'];
		}
		
		echo $jsonp_callback.'('.json_encode($returnData).')';
		exit;
	}
	
	protected function doRequest($queryText){
		$solr_server_url = Mage::getResourceModel('solrsearch/solr')->getSolrServerUrl();
		
		$solr_core = Mage::helper('solrsearch')->getSettings('solr_index');
		
		$servlet = $this->getRequest()->getParam('r');
		
		$url = trim($solr_server_url,'/').'/'.$solr_core.'/'.$servlet.'?q='.urlencode(strtolower(trim($queryText)));
		
		unset($_GET['q']);
		
		$trimmedText = trim(trim($queryText,'"'));
		
		$boostText = (strrpos($trimmedText, ':') > -1)?'"'.$trimmedText.'"':$trimmedText;
			
		$boostString = 'name_boost:'.$boostText.'^80 category_boost:'.$boostText.'^60';
		
		$url .= '&bq='.urlencode($boostString);
		
		$spellCheckQuery = '"'.strtolower(trim(trim($queryText,'"'))).'"';
		
		$url .= '&spellcheck.q='.urlencode($spellCheckQuery);
		
		if(isset($_GET['facet_field'])){
			$facetFieldsArray = explode(',',$_GET['facet_field']);
			foreach ($facetFieldsArray as $facetField){
				$url .= '&facet.field='.$facetField;
			}
		}
		
		$solrParams = array_merge($_GET, $this->getAutocompleteSettings());
		
		unset($solrParams['facet_field']);
		
		$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $solrParams, 'array');
		
		return $returnData;
	}
	
	/**
	 * Default solr params
	 * @return array
	 */
	public function getAutocompleteSettings()
	{
		$settings = array(
				'mm' => '100%',
				'json.nl' => 'map',
				'rows' => 5,
				'qf' => 'textSearch',
				'spellcheck' => 'true',
				'spellcheck.collate' => 'true',
				//'spellcheck.accuracy' => '0.1',
				'facet' => 'true',
				'facet.limit' => 3,
				'defType' => 'edismax',
				'spellcheck.count' => 2,
				'fl' => 'name_varchar,products_id,price_decimal,special_price_decimal,image_varchar,url_path_varchar',
				'fq' => '(store_id:"'.Mage::app()->getStore()->getId().'") AND (website_id:"'.Mage::app()->getStore()->getWebsiteId().'") AND (product_status:"1")',
		);
	
		return $settings;
	}
	
	public function fullqueryAction() {
		$solrModel = Mage::getModel('solrsearch/solr');
		$store = Mage::app()->getStore();
		$solrData = $solrModel->getFullQuery($store);
		print_r($solrData);
		exit;
	}
}