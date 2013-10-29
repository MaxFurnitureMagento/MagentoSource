<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Adminhtml_SolrsearchController extends Mage_Adminhtml_Controller_Action
{
	public $logFields = array();

	public $allowCategoryIds = array();
	
	public $ultility = null;
	
	public $itemsPerCommit = 50;

	protected function _initAction() {
		$this->loadLayout()
		->_setActiveMenu('solrsearch/indexes')
		->_addBreadcrumb(Mage::helper('adminhtml')->__('Solr Bridge Indexes'), Mage::helper('adminhtml')->__('Solr Bridge Indexes'));

		return $this;
	}
	/**
	 * Return write connection object
	 * @return unknown
	 */
	public function getWriteConnection()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		return $connection;
	}
	/**
	 * Return log table name
	 * @return string
	 */
	public function getLogTable()
	{
		$resource = Mage::getSingleton('core/resource');
		$logtable = $resource->getTableName('solrsearch/logs');
		return $logtable;
	}
	/**
	 * Index action
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
		
		$this->ultility = Mage::getModel('solrsearch/ultility');

		$this->_title($this->__('Solr Bridge Indexes'))
		->_title($this->__('Solr Bridge Indexes'))
		// Highlight the current menu
		->_setActiveMenu('solrsearch/indexes');
		
		$ping = Mage::getResourceModel('solrsearch/solr')->pingSolrCore();
		
		if (!$ping) {
			Mage::getSingleton("core/session")->addWarning('Solr Server Url is empty or Magento store and Solr index not yet mapped. Please go to System > Configuration > Solr Bridge > Basic Settings');
		}

		$this->renderLayout();
	}
	/**
	 * This is the start point for process data indexing
	 */
	public function processAction() {
		$this->ultility = Mage::getModel('solrsearch/ultility');
		
		$startTime = time();

		$errors = array();

		//get current page
		$page = 1;
		if( isset($_POST['page']) && is_numeric($_POST['page'])) {
			$page = $_POST['page'];
		}

		//get solr core
		$solrcore = 'english';
		if ( isset($_POST['core']) && !empty($_POST['core'])) {
			$solrcore = $_POST['core'];
		}

		//get current website id
		$websiteid = array();
		if ( isset($_POST['website']) && !empty($_POST['website'])) {
			$websiteid = explode(',', $_POST['website']);
		}

		//get total pages
		$totalPages = 1;
		if ( isset($_POST['totalpage']) && is_numeric($_POST['totalpage'])) {
			$totalPages = $_POST['totalpage'];
		}

		//get stores ids
		$stores = '';
		$storesArr = array();
		if ( isset($_POST['stores']) && !empty($_POST['stores'])) {
			$stores = $_POST['stores'];
			$storesArr = explode(',', $stores);
		}

		//get total of products
		$productCount = 1;
		if ( isset($_POST['productcount']) && is_numeric($_POST['productcount'])) {
			$productCount = $_POST['productcount'];
		}

		//get total number of solr documents
		$numDocs = 0;
		if ( isset($_POST['numDocs']) && is_numeric($_POST['numDocs']) ) {
			$numDocs = $_POST['numDocs'];
		}
		//get action
		$action = 'NEW';
		if (isset($_POST['action']) && !empty($_POST['action'])) {
			$action = $_POST['action'];
		}
		
		//Items per page
		$itemsPerCommit = 50;
		$itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
		if(intval($itemsPerCommitConfig) > 0) $itemsPerCommit = $itemsPerCommitConfig;
		$this->itemsPerCommit = $itemsPerCommit;

		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);

		//is category name searchable
		$solr_include_category_in_search = Mage::getStoreConfig('webmods_solrsearch/settings/solr_search_in_category', 0);
		//use category for facets
		$use_category_as_facet = Mage::getStoreConfig('webmods_solrsearch/settings/use_category_as_facet', 0);

		if (empty($solrcore) || empty($solr_server_url)){
			$errors[] = Mage::helper('solrsearch')->__('Solr Server Url is empty or Magento store and Solr index not yet mapped.');
		}
		//Solr data update url
		$Url = trim($solr_server_url,'/').'/'.$solrcore.'/update/json?commit=true&wt=json';
		//Solr get one doc url
		$start = intval($page) - 1;
		//print_r($_POST);

		$SolrQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&rows=1&wt=json';

		//Solr get all docs url
		$getExistingSolrDocsQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows='.$_POST['productcount'].'&wt=json';
		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';

		//get product collection


		$collection = null;//$this->loadProductCollection($page);
		 
		if($action == 'UPDATE') {
			if ($productCount == $numDocs && $page == 1) {
				//empty solr index

				$storeMappingString = Mage::getStoreConfig('webmods_solrsearch_indexes/'.$solrcore.'/stores', 0);

				$storeMappingString = trim($storeMappingString, ',');
				
				$connection = $this->getWriteConnection();
				$logtable = $this->getLogTable();
				
				if (!empty($storeMappingString)) {
					$results = $connection->query("DELETE FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND store_id IN({$storeMappingString});");
				}

				$results = $connection->query("DELETE FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND store_id IN({$storeMappingString});");

				$this->doRequest($clearnSolrIndexUrl);
				$returnData = array();
				$returnData['page'] = $page;
				$returnData['documents'] = 0;
				$returnData['continueprocess'] = 'yes';
				$returnData['nextpage'] = 1;
				$returnData['percent'] = 0;
				$this->getResponse()->setHeader("Content-Type", "application/json", true);
					
				$returnData['estimatedtime'] = 0;
				$returnData['remainedtime'] = 0;
				$returnData['numdocs'] = 0;
				$returnData['action'] = 'NEW';
				echo json_encode($returnData);
				exit;
			}
			$updateParams = array(
					'existing_solr_docs_query_url' => $getExistingSolrDocsQueryUrl.'&rows='.$_POST['numDocs'],
					'stores' => explode(',', $stores),
					'solr_update_url' => $Url,
					'solr_query_url' => $SolrQueryUrl,
					'page' => $page
			);
			$numberOfDocuments = $this->processUpdateSolrIndex($collection, $updateParams);
		}else {
			$newParams = array(
					'existing_solr_docs_query_url' => $getExistingSolrDocsQueryUrl.'&rows='.$_POST['numDocs'],
					'stores' => explode(',', $stores),
					'solr_update_url' => $Url,
					'solr_query_url' => $SolrQueryUrl,
					'page' => $page
			);
			$numberOfDocuments = $this->processNewSolrIndex($collection, $newParams);
		}

		//Log index fields
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$logtable = $resource->getTableName('solrsearch/logs');

		$returnData = array();
		$returnData['page'] = $page;
		$returnData['documents'] = $numberOfDocuments;
		$returnData['continueprocess'] = (is_numeric($numberOfDocuments) && $numberOfDocuments < $productCount)?'yes':'no';
		$returnData['nextpage'] = $page + 1;
		$returnData['action'] = (is_numeric($numberOfDocuments) && $numberOfDocuments > 0)?'UPDATE':'NEW';
		$returnData['percent'] = round(($numberOfDocuments*100)/$productCount);
		$returnData['numdocs'] = $numberOfDocuments;
		$this->getResponse()->setHeader("Content-Type", "application/json", true);

		$endTime = time();

		if (!isset($_POST['estimatedtime']) || $_POST['estimatedtime'] < 1) {
			$seconds = $endTime - $startTime;
		}else{
			$seconds = $_POST['estimatedtime'];
		}
		$returnData['estimatedtime'] = $seconds;
		$returnData['remainedtime'] = $this->calculateRemainTime($productCount, $numberOfDocuments, $seconds, $itemsPerCommit);

		echo json_encode($returnData);
		exit;
	}
	/**
	 * Calculate remain time for indexing process
	 * @param int $totalMagentoProducts
	 * @param int $totalSolrDocuments
	 * @param int $time
	 * @param int $itemsPerCommit
	 * @return string
	 */
	public function calculateRemainTime($totalMagentoProducts, $totalSolrDocuments, $time, $itemsPerCommit){
		$remainProducts = $totalMagentoProducts - $totalSolrDocuments;
		$remainSeconds = ($remainProducts * $time) / $itemsPerCommit;

		//$init = $remainSeconds;
		$hours = floor($remainSeconds/60/60);
		if ($hours > 0) {
			$minutes = ($remainSeconds/60) - ($hours*60);
			return $hours.':'. ceil($minutes) .' (h:m)';
		}
		$minutes = ceil($remainSeconds/60);
		if ($minutes > 0) {
			return $minutes.' minute(s)';
		}

		return $remainSeconds.'second(s)';
	}
	/**
	 * Start data indexing if there are already solr existing documents
	 * @param string $collection
	 * @param array $params
	 * @return number
	 */
	public function processUpdateSolrIndex($collection = NULL, $params = array()) {

		$numberOfIndexedDocuments = 0;

		foreach ($params['stores'] as $storeid) {
			$storeObject = Mage::getModel('core/store')->load($storeid);
				
			$collection = $this->loadUpdateProductCollection($params['page'], $storeid, $this->itemsPerCommit);
				
			$jsonData = $this->parseJsonData($collection, $storeObject);
			$returnNoOfDocuments = $this->postJsonData($jsonData, $params['solr_update_url'], $params['existing_solr_docs_query_url']);
			$numberOfIndexedDocuments = $returnNoOfDocuments;
		}
		return $numberOfIndexedDocuments;
	}
	/**
	 * Start index for first time
	 * @param string $collection
	 * @param array $params
	 * @return number
	 */
	public function processNewSolrIndex($collection = NULL, $params = array()) {
		$numberOfIndexedDocuments = 0;

		foreach ($params['stores'] as $storeid) {
			$storeObject = Mage::getModel('core/store')->load($storeid);
				
			$collection = $this->loadProductCollection($params['page'], $storeid, $this->itemsPerCommit);
				
			$jsonData = $this->parseJsonData($collection, $storeObject);
			$returnNoOfDocuments = $this->postJsonData($jsonData, $params['solr_update_url'], $params['existing_solr_docs_query_url']);
			$numberOfIndexedDocuments = $returnNoOfDocuments;
		}
		return $numberOfIndexedDocuments;
	}

	/**
	 * Parse product collection into json
	 * @param unknown_type $collection
	 * @param unknown_type $store
	 */
	public function parseJsonData($collection, $store) {
		$jsonDataArray = $this->ultility->parseJsonData($collection, $store);
		$jsonData = $jsonDataArray['jsondata'];
		return $jsonData;
	}
	/**
	 * Post json data to Solr for indexing
	 * @param json $jsonData
	 * @param string $updateurl
	 * @param string $queryurl
	 * @return int
	 */
	public function postJsonData($jsonData, $updateurl, $queryurl){
		//echo 'yes function called';
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			//Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
				return 0;
			}
				
			$postFields = array('stream.body'=>$jsonData);
				
			$output = $this->doRequest($updateurl, $postFields);
				
			$returnData = $this->doRequest($queryurl, array());

			if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
				if (is_array($returnData['response']['docs'])) {
					foreach ($returnData['response']['docs'] as $doc) {
						$this->logProductId($doc['products_id'], $doc['store_id']);
					}
				}
				return $returnData['response']['numFound'];
			}else {
				return 0;
			}
		}
	}
	/**
	 * Send request to Solr by curl
	 * @param string $url
	 * @param array $postFields
	 * @return array
	 */
	public function doRequest($url, $postFields = null){
		$output = Mage::getResourceModel('solrsearch/solr')->doRequest($url, $postFields);
		return $output;
	}
	/**
	 * General product thumnail
	 * @param unknown $product
	 */
	public function generateThumb($product){
		return $this->ultility->generateThumb($product);
	}
	
	/**
	 * Empty the whole index
	 */
	public function emptyindexAction() {
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);

		//get solr core
		$solrcore = 'english';
		if ( isset($_POST['core']) && !empty($_POST['core'])) {
			$solrcore = $_POST['core'];
		}

		$storeMappingString = Mage::getStoreConfig('webmods_solrsearch_indexes/'.$solrcore.'/stores', 0);

		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';

		$output = $this->doRequest($clearnSolrIndexUrl);

		$this->getResponse()->setHeader("Content-Type", "application/json", true);
		
		$connection = $this->getWriteConnection();
		$logtable = $this->getLogTable();

		while(true) {
			$SolrQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=1&wt=json';
			$queryOutput = $this->doRequest($SolrQueryUrl);
			if(is_array($queryOutput) && isset($queryOutput['response']) && isset($queryOutput['response']['numFound']) && intval($queryOutput['response']['numFound']) < 1) {
				$storeMappingString = trim($storeMappingString, ',');
				if (!empty($storeMappingString)) {
					$results = $connection->query("DELETE FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND store_id IN({$storeMappingString});");
				}
				break;
			}
		}

		echo 'true';
		exit;
	}
	/**
	 * Log any product which already indexed to solr into log table
	 * @param int $id
	 * @param int $store_id
	 */
	public function logProductId($id, $store_id){
		return $this->ultility->logProductId($id, $store_id);
	}

	/**
	 * Load product collection
	 * @param int $page
	 * @param int $store_id
	 * @param int $itemsPerPage
	 */
	public function loadProductCollection($page = 1, $store_id, $itemsPerPage){
		return $this->ultility->getProductCollectionByStoreId($store_id, $page, $itemsPerPage);
	}
	/**
	 * Load product collection for update data index
	 * @param int $page
	 * @param int $store_id
	 * @param int $itemsPerPage
	 */
	public function loadUpdateProductCollection($page = 1, $store_id, $itemsPerPage) {	
		return $this->ultility->getProductCollectionForUpdate($store_id, $page, $itemsPerPage);
	}
}
?>