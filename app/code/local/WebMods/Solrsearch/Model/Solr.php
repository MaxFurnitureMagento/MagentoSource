<?php
/**
 * @category SolrBridge
 * @package Solrbridge_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Model_Solr extends Mage_Core_Model_Abstract {
	//queryText
	public $queryText = '';
	//Page per items
	protected $_rows = 9;
	//start off set
	protected $_start = 0;	
	//Field list
	protected $_fieldList = 'products_id,name_varchar,store_id,website_id,price_decimal';
	//Query field - which field search for
	protected $_queryField = 'textSearch';
	//Search OP
	protected $_mm = '100%';
	//Boost query field
	protected $_boostQuery = '';
	//Filter query
	protected $_filterQuery = '';
	//Facet fields
	protected $_facetFields = array();
	//Boost fields
	protected $_boostfields = array();
	
	protected $ultility = null;
	
	protected $_resourceModel = null;
	
	protected function _construct()
    {
        $this->_init('solrsearch/solr');
        
        $this->_resourceModel = Mage::getResourceModel('solrsearch/solr');
        
        $this->ultility = Mage::getModel('solrsearch/ultility');
        $this->initConfiguredFields();
        $this->initPagingData();
    }
    
    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product
     */
    protected function _getResource()
    {
    	return parent::_getResource();
    }
    
    protected function getRows() {
    	return $this->_rows;
    }
	protected function setRows($rows) {
    	$this->_rows = $rows;
    }
    
	protected function getStart() {
    	return $this->_start;
    }
	protected function setStart($start) {
    	$this->_start = $start;
    }
    
	protected function getFieldList() {
    	return $this->_fieldList;
    }
	protected function setFieldList($fieldList) {
		$fieldListString = $this->_fieldList;
		if (is_array($fieldList)) {
			$fieldListString = @implode(',', $fieldList);
		}else {
			$fieldListString = $fieldList;
		}
    	$this->_fieldList = $fieldListString;
    }
    
	protected function getQueryField() {
    	return $this->_queryField;
    }
	protected function setQueryField($queryField) {
		if (is_array($queryField)) {
			$queryField = @implode(',', $queryField);
		}
    	$this->_queryField = $queryField;
    }
    
    protected function getBoostQuery($rebuild=false){
    	if (!$rebuild) {
    		return $this->_boostQuery;
    	}else{
    		$q = $this->queryText;
    		$boostString = '';
    		foreach($this->_boostfields as $attribute){
			   	if(isset($attribute['attribute_code']) && !empty($attribute['attribute_code']) && $attribute['weight'] > 0){
			    	$boostString .= $attribute['attribute_code'].':'.(empty($attribute['value'])?$q:$attribute['value']).''."^".$attribute['weight']." ";
			   	}
    		}
    		return $boostString;
    	}
    }
    
	protected function setBoostQuery($boostQueryArray){
    	$this->_boostQuery = $boostQueryArray;
    }
    
	protected function getSearchOp() {
    	return $this->_mm;
    }
	protected function setSearchOp($op) {
    	$this->_mm = $op;
    }
    
	protected function getFilterQuery() {
    	return $this->_filterQuery;
    }
	protected function setFilterQuery($filterQuery) {
    	$this->_filterQuery = $filterQuery;
    }
    
	protected function getFacetFields() {
    	return $this->_facetFields;
    }
	protected function setFacetFields($facetFields) {
    	$this->_facetFields = $facetFields;
    }
    
    protected function getFacetFieldString()
    {
    	//Facet fields
    	$facetFieldsStr = '';
    	$facetFieldsArr = $this->getFacetFields();
    	foreach ($facetFieldsArr as $fieldKey) {
    		$facetFieldsStr .= 'facet.field='.$fieldKey.'&';
    	}
    	$facetFieldsStr = trim($facetFieldsStr,'&').'&facet.field=price_decimal&facet.range=price_decimal&f.price_decimal.facet.range.start=0.0&f.price_decimal.facet.range.end=10000.0&f.price_decimal.facet.range.gap=100';
    	
    	return $facetFieldsStr;
    }
    
    protected function getFilterQueryString(){
    	$filterQuery = Mage::getSingleton('core/session')->getSolrFilterQuery();
    	if ($this->getStandardFilterQuery()) {
    		$filterQuery = $this->getStandardFilterQuery();
    	}
    	 
    	$filterQuery = array_merge($filterQuery, array(
    			'store_id' => array(Mage::app()->getStore()->getId()),
    			'website_id' => array(Mage::app()->getStore()->getWebsiteId()),
    			'product_status' => array(1)
    	));
    		
    	$filterQueryArray = array();
    	foreach($filterQuery as $key=>$filterItem){
    		if(count($filterItem) > 0){
    			$query = '';
    			foreach($filterItem as $value){
    				if ($key == 'price_decimal') {
    					$query .= $key.':['.urlencode(trim($value)).']+OR+';
    				}else if($key == 'price'){
    					$query .= $key.'_decimal:['.urlencode(trim($value)).']+OR+';
    				}else{
    					if ($key == 'price_facet') {
    						$query .= 'price_decimal:['.urlencode(trim($value)).']+OR+';
    					}else{
    						$query .= $key.':%22'.urlencode(trim(addslashes($value))).'%22+OR+';
    					}
    				}
    			}
    	
    			$query = trim($query, '+OR+');
    	
    			$filterQueryArray[] = $query;
    		}
    	}
    	
    	$filterQueryString = '';
    	
    	if(count($filterQueryArray) > 0) {
    		if(count($filterQueryArray) < 2) {
    			$filterQueryString .= $filterQueryArray[0];
    		}else{
    			$filterQueryString .= '%28'.@implode('%29+AND+%28', $filterQueryArray).'%29';
    		}
    	}
    	
    	return $filterQueryString;
    }
    
    public function doRequest($url, $store, $args = array()) {
    	$arguments = array(
			'json.nl' => 'map',
			'rows' => $this->getRows(),
			'start' => $this->getStart(),
			'fl' => $this->getFieldList(),
			'qf' => $this->getQueryField(),
			'spellcheck' => 'true',
			'spellcheck.collate' => 'true',
			'facet' => 'true',
			'facet.mincount' => 1,
			'timestamp' => time(),
			'mm' => '100%',
			'defType'=> 'edismax',
    		'stats' => 'true',
    		'stats.field' => 'price_decimal',
			'wt'=> 'json',			
		);
		
		//Facet fields
		$facetFieldsStr = $this->getFacetFieldString();
		if (!empty($facetFieldsStr)) {
			$url .= '&'.$facetFieldsStr;
		}
		//filter query
		$filterQueryString = $this->getFilterQueryString();
		if (!empty($filterQueryString)) {
			$url .= '&fq='.$filterQueryString;
		}
    	//boost query
		$boostQueryString = $this->getBoostQuery();
		if (!empty($boostQueryString)) {
			$url .= '&bq='.urlencode($boostQueryString);
		}
		
		$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $arguments, 'array');
		
    	if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
    	
			return $returnData;
			
		}else{
			
			if (isset($returnData['spellcheck']['suggestions']['collation'])) {
				
				
				$queryText = strtolower($returnData['spellcheck']['suggestions']['collation']);
				
				$queryText = str_replace(':', '', $queryText);
					
				if (empty($queryText)) 
				{
					$queryText = $this->getParams('q');
				}
				$this->queryText = $queryText;
					
				$url = $this->buildRequestUrl($store,true,$queryText);
				
					
				$arguments['mm'] = '0%';
				
				//Facet fields
				$facetFieldsStr = $this->getFacetFieldString();
				if (!empty($facetFieldsStr)) {
					$url .= '&'.$facetFieldsStr;
				}
				//filter query
				$filterQueryString = $this->getFilterQueryString();
				if (!empty($filterQueryString)) {
					$url .= '&fq='.$filterQueryString;
				}
		    	//boost query
				$boostQueryString = $this->getBoostQuery(true);
				if (!empty($boostQueryString)) {
					$url .= '&bq='.urlencode($boostQueryString);
				}
				
				$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $arguments, 'array');
				
			}
		}
		
		return $returnData;
    }
    
    public function buildRequestUrl($store, $hasCore=true, $query=""){
    	$solr_server_url = Mage::helper('solrsearch')->getSettings('solr_server_url');
		$solr_index = Mage::helper('solrsearch')->getSettings('solr_index');
		//Get all params
		$params = $this->getParams();
		
		$q = "*:*";
		
    	if (!empty($query)) {
			$q = $query;
		}else {	
			if(isset($params['q'])){
				$q = trim($params['q'],':');
			}
		}
		if ($hasCore){
			$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q='.urlencode(strtolower(trim($q)));
		}else{
			$url = trim($solr_server_url,'/').'/select/?q='.urlencode(strtolower(trim($q)));
		}
		
		$url .= '&spellcheck.q='.urlencode($this->prepareBoostText($q));
		
		return $url;
    }
    
    public function getParams($key = "") {
    	$params = Mage::helper('solrsearch')->getParams();
				
		if (!empty($key) && isset($params[$key]) && !empty($params[$key])) {
			return $params[$key];
		}else if (empty($key)){
			return $params;
		}else{
			return false;
		}
    }
    
    protected function initPagingData()
    {
    	$currentPage = 1;
    	$page = $this->getParams('p');
    	if(!empty($page) && is_numeric($page)){
    		$currentPage = $page;
    	}
    	$itemsPerPage= 32;
    	$itemsPerPageSettings = Mage::helper('solrsearch')->getSettings('items_per_page');
    	if (!empty($itemsPerPageSettings) && is_numeric($itemsPerPageSettings)) {
    		$itemsPerPage = $itemsPerPageSettings;
    	}
    	$start = $itemsPerPage * ($currentPage - 1);
    	$this->setStart($start);
    	$this->setRows($itemsPerPage);
    }    
    protected function initConfiguredFields(){
    	$q = $this->getParams('q');
    	$boostFieldsArr = array();
    	$boostFields = array();
    	$facetFields = array();
    	
    	$boostWeights = $this->getSearchWeights(); //get static field weight mapping
    	
		$attributesInfo = $this->ultility->getProductAttributeCollection();
		
    	foreach($attributesInfo as $attribute){
		   if(isset($attribute['solr_search_field_weight']) && !empty($attribute['solr_search_field_weight']) && $attribute['solr_search_field_weight'] > 0){
		   		$boostText = $this->prepareBoostText($q);
		    	$boostFields[$attribute['attribute_code']] = $attribute['attribute_code'].'_boost:'.$boostText.'^'.$attribute['solr_search_field_weight'];
		   		$boostFieldsArr[] = array('attribute_code'=>$attribute['attribute_code'].'_boost','weight'=>$attribute['solr_search_field_weight'],'value'=>'');
		   }
		   if (isset($attribute['solr_search_field_boost']) && !empty($attribute['solr_search_field_boost'])) {
		   		$boostValues = explode("\n", $attribute['solr_search_field_boost']);
		   		$boostString = "";
		   		foreach ($boostValues as $boostValue) {
		   			$pair = explode('|', trim($boostValue));
		   			if (isset($pair[0]) && !empty($pair[0]) && isset($pair[1]) && !empty($pair[1])) {
		   				
		   				$boostText = $this->prepareBoostText($pair[0]);
		   				
		   				$boostString .= $attribute['attribute_code'].'_boost:'.$boostText.'^'.$boostWeights[$pair[1]]." ";
		   				$boostFieldsArr[] = array('attribute_code'=>$attribute['attribute_code'].'_boost','weight'=>$attribute['solr_search_field_weight'],'value'=>$pair[0]);
		   			}
		   		}
		   		$boostFields[$attribute['attribute_code']] = trim($boostString);
		   }
		   
		   if (isset($attribute['is_filterable_in_search']) && $attribute['is_filterable_in_search'] > 0) {
		   		$facetFields[] = $attribute['attribute_code'].'_facet';
		   }
		   
		}
		
		if (count($boostFields)) {
			$boostFieldsString = @implode(" ", $boostFields);
			$this->setBoostQuery($boostFieldsString);
			$this->_boostfields = $boostFieldsArr;
		}else {
			$boostText =  $this->prepareBoostText($q);
			$boostFieldsString = 'name_boost:'.$boostText.'^80 category_boost:'.$boostText.'^60';
			
			$this->setBoostQuery($boostFieldsString);
			$this->_boostfields = array(
				array('attribute_code'=>'name_boost', 'weight'=>80, 'value'=>''),
				array('attribute_code'=>'category_boost', 'weight'=>60, 'value'=>'')
			);
		}
		
		
		$use_category_as_facet = Mage::helper('solrsearch')->getSettings('use_category_as_facet');
		
    	if ($use_category_as_facet > 0) {
    		$display_category_as_hierachy = Mage::helper('solrsearch')->getSettings('display_category_as_hierachy');
    		if ($display_category_as_hierachy > 0) {
    			$facetFields[] = 'category_path';
    		}else{
    			$facetFields[] = 'category_facet';
    		}
						
		}
		
    	if (count($facetFields)) {
			$this->setFacetFields($facetFields);
		}
    }
    
    protected function prepareBoostText($q){
    	$boostText =  (strrpos(trim($q,':'), ':') > -1)?'"'.trim($q,':').'"':trim($q,':');
    	return $boostText;
    }
    
    protected function getSearchWeights() {
    	$weights = array();
		$index = 1;
		foreach (range(10, 200, 10) as $number) {
		    $weights[$index] = $number;
		    $index++;
		}
		return $weights;
    }
    
    public function getFullQuery($store) {
    	$solr_server_url = Mage::helper('solrsearch')->getSettings('solr_server_url');
		$solr_index = Mage::helper('solrsearch')->getSettings('solr_index');
		
		$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q=*:*&wt=json';
		
		$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($url);
		return $returnData;
    }
    /**
     * Get filter query array from url
     * @return array
     */
    public function getStandardFilterQuery(){
    	$params = $this->getParams();
    	if (isset($params['fq']) && is_array($params['fq'])) {
    		$filterQuery = array();
    		foreach ($params['fq'] as $key=>$values) {
    			if (!empty($key) && !is_array($values) && !empty($values)) {
    				$filterQuery[$key.'_facet'] = array($values);
    			}else if(!empty($key) && is_array($values)){
    				$filterQuery[$key.'_facet'] = $values;
    			}
    		}
    		return $filterQuery;
    	}
		return array();
    }
}