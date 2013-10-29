<?php
ini_set('memory_limit', '2040M');
/**
 * @category SolrBridge
 * @package Solrbridge_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
require_once 'abstract.php';

class Solrsearch_Shell_Indexer extends Mage_Shell_Abstract{
	
	public $ultility = null;
	
    public $itemsPerCommit = 100;
    
    public $checkInStock = FALSE;
    
    public $totalPages = 0;
    
    public $totalMagentoProducts = 0;
    
    public $totalSolrDocuments = 0;
    
    public $totalFetchedProducts = 0;
    
    public $totalMagentoProductsNeedToUpdate = 0;
    
    public $page = 1;
    
    public $storeids = array();
    
    public $websiteids = array();

    public $solrServerUrl = 'http://localhost:8080/solr/';
    
    public $solrCore = 'english';
    
    public $logFields = array();
    
    public $percent = 0;
    
    public $time = 0;
    
    public $allowCategoryIds = array();
    
    
    public function __construct() {
        parent::__construct();
        
        $this->ultility = Mage::getModel('solrsearch/ultility');
        
        $itemsPerCommitConfig = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_commit', 0);
        if( intval($itemsPerCommitConfig) > 0 )
        {
            $this->itemsPerCommit = $itemsPerCommitConfig;
        }
        
        $checkInstockConfig = Mage::getStoreConfig('webmods_solrsearch/settings/check_instock', 0);
        if( intval($checkInstockConfig) > 0 )
        {
            $this->checkInStock = $checkInstockConfig;
        }
        
        $solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
        $this->solrServerUrl = $solr_server_url;
        
    }

    /*
     * Run script
     */
    public function run() {
        if ($this->getArg('info')) {
            $this->printInfo();
        }
        else if ($this->getArg('updateindex')) {
            $solrcore = $this->getArg('updateindex');//get value
            $availableCores = array_keys($this->ultility->getAvailableCores());
            if (in_array($solrcore, $availableCores)) 
            {
                //run indexing for core
                $this->runIndexingByCore($solrcore);
            }
            else
            {
                echo 'core not found, please run: php solrbridge_indexer.php -info for hints'. "\n";
            }
            
        }
        else if ($this->getArg('truncate'))
        {
            $solrcore = $this->getArg('truncate');//get value
            $availableCores = array_keys($this->ultility->getAvailableCores());
            if (in_array($solrcore, $availableCores)) 
            {
                //run indexing for core
                $this->truncateSolrCore($solrcore);
            }
            else
            {
                echo 'core not found, please run: php solrbridge_indexer.php -info for hints'. "\n";
            }
        }
        else 
        {
            echo 'run indexing all cores'; // not yet implemented will do it in the furture
        }
    }
    /**
     * Run data indexing by solrcore
     * @param string $coreName
     */
    public function runIndexingByCore( $coreName = 'english') {
        $this->solrCore = $coreName;
        echo $this->ultility->writeLog('Start indexing process for core ('.$coreName.')');
        
        $availableCores = $this->ultility->getAvailableCores();
        $coreInfoArray = $availableCores[$coreName];
        
        //We need store ids which map to this core
        $storeIds = explode(',', trim($coreInfoArray['stores'], ','));
        $this->storeids = $storeIds;
        
        //We need website ids which map to this core
        $websitesIds = array();     
        $productCount = 0;
        
        $this->percent = 0;
        
        foreach ($storeIds as $store_id) {
            if (!empty($store_id) && $store_id > 0) {
    			$store = Mage::getModel('core/store')->load($store_id);
                $collectionMetaData = $this->ultility->getCollectionMetaDataByStoreId($store_id);
    			$productCount += $collectionMetaData['productCount'];
    			$websitesIds[] = $store->getWebsiteId();
            }
		}
        
        //We need a total magento products belong to this core
        $this->totalMagentoProducts = $productCount;
        echo $this->ultility->writeLog('Magento product count : '.$this->totalMagentoProducts);
        //We need to get how many solr documents existing
        $this->totalSolrDocuments = $this->getTotalDocumentsByCore($coreName);
        //$this->totalFetchedProducts = $this->totalSolrDocuments;
        
        $this->totalMagentoProductsNeedToUpdate = $this->totalMagentoProducts - $this->totalSolrDocuments;
        
        echo $this->ultility->writeLog('Existing solr documents : '.$this->totalSolrDocuments);
        
        if ($this->totalSolrDocuments >= $this->totalMagentoProducts) {
        	echo $this->ultility->writeLog('There is no new products to update');
        	return true;
        }
        
        //We need website ids
        $this->websiteids = $websitesIds;
        
        if ($this->totalSolrDocuments > 0 && $this->totalMagentoProducts > $this->totalSolrDocuments) // update solr index data
        {
            $this->updateindexSolrDataByCore($coreName);
        }
        else 
        {
            $this->reindexSolrDataByCore($coreName);
        }
    }
    
    /**
     * Update solr data
     * @param string $core
     * @param array $storeids
     * @param array $websiteids
     * @param int $totalMagentoProducts
     * @param int $totalExistingSolrDocuments
     */
    public function updateindexSolrDataByCore($core) {
        //Start time
        $starttime = time();
        
        $numberOfIndexedDocuments = 0;
        
        $storeids = $this->storeids;
        
        $storeObjectArray = array();
        
        $storeNameArray = array();
        
        foreach ($storeids as $storeid) {
            $storeObject = Mage::getModel('core/store')->load($storeid);
            $storeObjectArray[$storeid] = $storeObject;
            $storeNameArray[] = $storeObject->getName();
        }
        
        $message = 'There are '.count($storeObjectArray).' ('.@implode(',', $storeNameArray).') mapped';
		echo $this->ultility->writeLog($message);
		
		foreach ($storeObjectArray as $storeid => $storeObject) {
			
			$collectionMetaData = $this->ultility->getCollectionMetaDataByStoreIdForUpdate($storeid);
			$totalMagentoProductsByStore = $collectionMetaData['productCount'];
				
			$totalFetchedProductsByStore = 0;
				
			$this->page = 1;

		    //while ($this->totalMagentoProductsNeedToUpdate > $this->totalFetchedProducts){
		    while ($totalMagentoProductsByStore > $totalFetchedProductsByStore)
		    {
		    	$this->calculateMemoryUsage();
    			//Fetching products from Magento Database
    			$productCollection = $this->ultility->getProductCollectionForUpdate($storeid, $this->page, $this->itemsPerCommit);
    			echo $this->ultility->writeLog('Fetched '.$this->itemsPerCommit.' products from Magento database');
    			
    			//Parse json data from product collection
    			$dataArray = $this->ultility->parseJsonData($productCollection, $storeObject);
    			
    			$jsonData = $dataArray['jsondata'];
    			
    			$this->totalFetchedProducts = $this->totalFetchedProducts + $dataArray['fetchedProducts'];
    			$totalFetchedProductsByStore = $totalFetchedProductsByStore + $dataArray['fetchedProducts'];
    			
    			if ($this->totalFetchedProducts >= $this->totalMagentoProductsNeedToUpdate) {
    				$this->totalFetchedProducts = $this->totalMagentoProductsNeedToUpdate;
    			}
    			
    			$this->percent = number_format(($this->totalFetchedProducts * 100) / $this->totalMagentoProductsNeedToUpdate, 2);
    			
    			//Post json data to Solr
    			$numberOfIndexedDocuments = $this->postJsonData($jsonData, $storeObject);
    			
                $endtime = time();
		    
                if ($this->time < 1) {
                    $this->time = ($endtime - $starttime);
                }
                
                if ($this->totalSolrDocuments >= $this->totalMagentoProducts) {
                	break;
                }
    			
    			if ($numberOfIndexedDocuments > 0) {
    				$this->totalSolrDocuments = $numberOfIndexedDocuments;
    				
    			    echo $this->ultility->writeLog('Solr indexed '.$this->totalSolrDocuments.'/'.$this->totalMagentoProducts . ' products successfully');
    			    echo $this->ultility->writeLog('Posted '.$this->totalFetchedProducts.'/'.$this->totalMagentoProductsNeedToUpdate.'/'.$this->totalMagentoProducts . ' products to Solr');
    			    $this->page = $this->page + 1;
    			    
    			    
    			    //$percent = intval($this->totalSolrDocuments) * 100 / intval($this->totalMagentoProducts);

    			    echo $this->ultility->writeLog('Progress: '.$this->percent.'%');
    			    echo $this->ultility->writeLog('Estimate remain time: '.$this->calculateRemainTime());
    			    echo $this->ultility->writeLog('Current store: '.$storeObject->getName().'(' . $totalFetchedProductsByStore .'/' . $totalMagentoProductsByStore . ' products)');
    			    echo $this->ultility->writeLog('Continue...');
    			    echo $this->ultility->writeLog('------------------------------------------------');
    			     
    			    continue;
    			}else{
    			   $this->page = $this->page + 1;
    			   echo $this->ultility->writeLog('Posted '.$this->totalFetchedProducts.'/'.$this->totalMagentoProductsNeedToUpdate.'/'.$this->totalMagentoProducts . ' products to Solr');
    			   echo $this->ultility->writeLog('Progress: '.$this->percent.'%');
    			   echo $this->ultility->writeLog('Estimate remain time: '.$this->calculateRemainTime());
    			   echo $this->ultility->writeLog('Current store: '.$storeObject->getName().'(' . $totalFetchedProductsByStore .'/' . $totalMagentoProductsByStore . ' products)');
    			   echo $this->ultility->writeLog('Continue...');
    			   echo $this->ultility->writeLog('------------------------------------------------');
    			   continue;
    			}
			}
		    
		}
		echo $this->ultility->writeLog('Finished: '.$this->percent.'%');
		return $numberOfIndexedDocuments;
    }
    
    public function calculateRemainTime(){
        $remainProducts = $this->totalMagentoProducts - $this->totalFetchedProducts;
        $remainSeconds = ($remainProducts * $this->time) / $this->itemsPerCommit;
        
        echo $this->ultility->writeLog('Remain products: '.$remainProducts);
        echo $this->ultility->writeLog('Time per commit: '.$this->time);

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
    
    public function calculateMemoryUsage()
    {
    	//Memory usage in byte
    	$memoryUsage = memory_get_usage();
    	//Convert to MB
    	$memoryUsage = $memoryUsage/1024/1024;
    	
    	$currentMemoryLimitStr = ini_get('memory_limit');
    	
    	$currentMemoryLimit = 2048;
    	
    	if ( -1 !== ($position = strpos($currentMemoryLimitStr, 'M')) ){
    		$currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
    	}elseif ( -1 !== ($position = strpos($currentMemoryLimitStr, 'G')) )
    	{
    		$currentMemoryLimit = substr($currentMemoryLimitStr, 0, $position);
    		$currentMemoryLimit = $currentMemoryLimit / 1024;
    	}
    	
    	if (($currentMemoryLimit - $memoryUsage) < 100) {
    		$currentMemoryLimit = $currentMemoryLimit + 100;
    		ini_set('memory_limit', $currentMemoryLimit);
    	}

    	ini_set('max_execution_time', 18000);
    	echo $this->ultility->writeLog('Memory limit:'.$currentMemoryLimit.'M');
    	echo $this->ultility->writeLog('Memory usage:'.number_format($memoryUsage).'M');
    }
    
    /**
     * Process New Indexing
     * @param string $core
     * @param array $storeids
     * @param array $websiteids
     * @param int $totalMagentoProducts
     * @param int $totalExistingSolrDocuments
     */
    public function reindexSolrDataByCore($core) {
        //Start time
        
        $starttime = time();
        
        $numberOfIndexedDocuments = 0;
        
        $storeids = $this->storeids;
        
        $storeObjectArray = array();
        
        $storeNameArray = array();
        
        foreach ($storeids as $storeid) {
            $storeObject = Mage::getModel('core/store')->load($storeid);
            $storeObjectArray[$storeid] = $storeObject;
            $storeNameArray[] = $storeObject->getName();
        }
        
        $message = 'There are '.count($storeObjectArray).' ('.@implode(',', $storeNameArray).') mapped';
		echo $this->ultility->writeLog($message);
		
		foreach ($storeObjectArray as $storeid => $storeObject) {
			
			$collectionMetaData = $this->ultility->getCollectionMetaDataByStoreId($storeid);
			$totalMagentoProductsByStore = $collectionMetaData['productCount'];
			
			$totalFetchedProductsByStore = 0;
			
			$this->page = 1;
			
			//while ($this->totalMagentoProducts > $this->totalFetchedProducts){
			while ($totalMagentoProductsByStore > $totalFetchedProductsByStore){
				$this->calculateMemoryUsage();
    			//Fetching products from Magento Database
    			$productCollection = $this->ultility->getProductCollectionByStoreId($storeObject->getId(), $this->page, $this->itemsPerCommit);
    			echo $this->ultility->writeLog('Fetched '.$this->itemsPerCommit.' products from Magento database');
    			
    			//Parse json data from product collection
    			$dataArray = $this->ultility->parseJsonData($productCollection, $storeObject);
    			
    			$jsonData = $dataArray['jsondata'];
    			
    			$this->totalFetchedProducts = $this->totalFetchedProducts + $dataArray['fetchedProducts'];
    			$totalFetchedProductsByStore = $totalFetchedProductsByStore + $dataArray['fetchedProducts'];
    			
    			if ($this->totalFetchedProducts > $this->totalMagentoProducts) {
    				$this->totalFetchedProducts = $this->totalMagentoProducts;
    			}
    			
    			$this->percent = number_format(($this->totalFetchedProducts * 100) / $this->totalMagentoProducts, 2);
    			
    			//Post json data to Solr
    			$numberOfIndexedDocuments = $this->postJsonData($jsonData, $storeObject);
    			
    			
                $endtime = time();
                if ($this->time < 1) {
                    $this->time = ($endtime - $starttime);
                }
    			
    			if ($numberOfIndexedDocuments > 0) {
    				$this->totalSolrDocuments = $numberOfIndexedDocuments;
    				
    			    echo $this->ultility->writeLog('Solr indexed '.$this->totalSolrDocuments.'/'.$this->totalMagentoProducts . ' products successfully');
    			    echo $this->ultility->writeLog('Posted '.$this->totalFetchedProducts.'/'.$this->totalMagentoProducts . ' products to Solr');
    			    $this->page = $this->page + 1;
    			    
    			    echo $this->ultility->writeLog('Progress: '.$this->percent.'%');
    			    
    			    echo $this->ultility->writeLog('Estimate remain time: '.$this->calculateRemainTime());
    			    
    			    echo $this->ultility->writeLog('Current store: '.$storeObject->getName().'(' . $totalFetchedProductsByStore .'/' . $totalMagentoProductsByStore . ' products)');
    			    
    			    echo $this->ultility->writeLog('Continue...');
    			    
    			    echo $this->ultility->writeLog('------------------------------------------------');
    			     
    			    continue;
    			}else{
    			    $this->page = $this->page + 1;
    			    echo $this->ultility->writeLog('Posted '.$this->totalFetchedProducts.'/'.$this->totalMagentoProducts . ' products to Solr');
    			    echo $this->ultility->writeLog('Progress: '.$this->percent.'%');
    			    echo $this->ultility->writeLog('Estimate remain time: '.$this->calculateRemainTime());
    			    echo $this->ultility->writeLog('Current store: '.$storeObject->getName().'(' . $totalFetchedProductsByStore .'/' . $totalMagentoProductsByStore . ' products)');
    			    echo $this->ultility->writeLog('Continue...');
    			    echo $this->ultility->writeLog('------------------------------------------------');
    			    continue;
    			}
			}
		    
		}
		echo $this->ultility->writeLog('Finished: '.$this->percent.'%');
		return $numberOfIndexedDocuments;
    }
    
    /**
     * Get total existing solr documents
     * @param unknown_type $coreName
     */
    public function getTotalDocumentsByCore( $coreName = 'english' ) {
        $solrLukeArray = Mage::getResourceModel('solrsearch/solr')->getSolrLuke($coreName);
        
        $totalDocuments = $solrLukeArray['response']['numFound'];
        
        return $totalDocuments;
    }
    
    
    public function printInfo() {
        $cores = $this->ultility->getAvailableCores();
        foreach ($cores as $key=>$core) {
            /* @var $process Mage_Index_Model_Process */
            echo sprintf('%-30s', $key);
            echo $core['label'] . "\n";
        }
    }
     
     public function truncateSolrCore($solrcore = 'english') {
		$solr_server_url = $this->solrServerUrl;
		
		$storeMappingString = Mage::getStoreConfig('webmods_solrsearch_indexes/'.$solrcore.'/stores', 0);
		
		$totalSolrDocuments = $this->getTotalDocumentsByCore($solrcore);
		
		//Solr delete all docs from index
		$clearnSolrIndexUrl = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';
		
		$output = Mage::getResourceModel('solrsearch/solr')->doRequest($clearnSolrIndexUrl);
		
		while(true) {
			$SolrQueryUrl = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=1&wt=json';
			$queryOutput = Mage::getResourceModel('solrsearch/solr')->doRequest($SolrQueryUrl);
			if(is_array($queryOutput) && isset($queryOutput['response']) && isset($queryOutput['response']['numFound']) && intval($queryOutput['response']['numFound']) < 1) {
				$storeMappingString = trim($storeMappingString, ',');
				if (!empty($storeMappingString)) {
					$resource = Mage::getSingleton('core/resource');
					$connection = $resource->getConnection('core_write');
					$logtable = $resource->getTableName('solrsearch/logs');
					
					$results = $connection->query("DELETE FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND store_id IN({$storeMappingString});");
				}
				$message = 'Truncate '.$totalSolrDocuments.' documents from core ('.$solrcore.') successfully';
				echo $this->ultility->writeLog($message);
				echo $message."\n";
				break;
			}
		}
		exit;
	 }
     
    /**
     * Post solrdata to solr
     * @param string $jsonData
     * @return int
     */
    public function postJsonData($jsonData){
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
			    echo $this->ultility->writeLog('Empty json data at page '.$this->page, 3);
				return 0;
			}
			$updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrCore.'/update/json?wt=json';
			/*
			if ($this->totalMagentoProducts <= 5000) {
			    $updateurl = trim($this->solrServerUrl,'/').'/'.$this->solrCore.'/update/json?commit=true&wt=json';
			}
			*/
			$postFields = array('stream.body'=>$jsonData);
			
			echo $this->ultility->writeLog('Started posting json of '.$this->itemsPerCommit.' products to Solr');
			
			$output = Mage::getResourceModel('solrsearch/solr')->doRequest($updateurl, $postFields);
			
			//Solr get all docs url
		    $getExistingSolrDocsQueryUrl = trim($this->solrServerUrl,'/').'/'.$this->solrCore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows='.$this->totalSolrDocuments.'&wt=json';
			
		    $returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($getExistingSolrDocsQueryUrl);
		    
		    if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
		    	if (is_array($returnData['response']['docs'])) {
		    		foreach ($returnData['response']['docs'] as $doc) {
		    			$this->ultility->logProductId($doc['products_id'], $doc['store_id']);
		    		}
		    	}
		    	return $returnData['response']['numFound'];
		    }else {
				return 0;
			}
		}
	}
}

$shell = new Solrsearch_Shell_Indexer();
$shell->run();