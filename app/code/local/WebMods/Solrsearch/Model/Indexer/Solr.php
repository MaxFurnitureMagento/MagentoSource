<?php
class WebMods_Solrsearch_Model_Indexer_Solr extends Mage_Index_Model_Indexer_Abstract{
    protected $_matchedEntities = array();
    protected $_stats = array();
    public $batchDirectory = '';

    protected function _construct() {
        //$this->_init('solrsearch/indexer_solr');
        $this->batchDirectory = Mage::getBaseDir('var').'/solrbridge_processes/';
        if (!is_dir($this->batchDirectory)) {
        	mkdir($this->batchDirectory, 0777);
        }
        return parent::_construct();
    }
    
    public static function deleteProcess($processId){
    	unlink($processId);
    	return true;
    }
    
    public function addProcess($functionName, $args){
    	$path = '';
    	while (true) {
    		$path = $this->batchDirectory.'solr_bridge_process'.time();
    		if (file_exists($path) == false)
    			break;
    	}
    	
    	$fh = fopen($path, 'w');
    	fprintf($fh, $functionName ."\n");
    	foreach ($args as $k => $v) {
    		fprintf($fh, $k.'|'.$v."\n");
    	}
    	fclose($fh);
    	return TRUE;
    }

	public static function get_all()
	  {
	    $rows = array();
	    if (is_dir($this->batchDirectory)) {
	        if ($dh = opendir($this->batchDirectory)) {
	            while (($file = readdir($dh)) !== false) {
	                $path = $this->batchDirectory.$file;
	                if ( is_dir( $path ) == false )
	                {
	                    $item = array();
	                    $item['id'] = $path;
	                    $fh = fopen( $path, 'r' );
	                    if ( $fh )
	                    {
	                        $item['function'] = trim(fgets( $fh ));
	                        $item['args'] = array();
	                        while( ( $line = fgets( $fh ) ) != null )
	                        {
	                            $args = split( ':', trim($line) );
	                            $item['args'][$args[0]] = $args[1];
	                        }
	                        $rows []= $item;
	                        fclose( $fh );
	                    }
	                }
	            }
	            closedir($dh);
	        }
	    }
	    return $rows;
	  }
    

    /**
     * Retrieve Fulltext Search instance
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    protected function _getIndexer() {
        return Mage::getSingleton('solrsearch/solr');
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_CatalogSearch_Model_Resource_Indexer_Fulltext
     */
    protected function _getResource() {
        return Mage::getResourceSingleton('solrsearch/indexer_solr');
    }

    protected function _registerEvent(Mage_Index_Model_Event $event){
        return $event;
    }

    protected function _processEvent(Mage_Index_Model_Event $event){
        return $event;
    }

    public function getName(){
        return Mage::helper('solrsearch')->__('Solr Bridge Index');
    }
    public function getDescription(){
        return Mage::helper('solrsearch')->__('Solr Bridge Documents Indexer');
    }
	public function solr_index_commit_data($jsonData, $store){
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
		}else{
			// Now set some options (most are optional)
			//$Url = variable_get('yasova_solr_server_url', 'http://localhost:8080/ongthodiasolr/main/').'update/json?commit=true&wt=json';
			$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $store->getId());
        	$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $store->getId());
			
        	$Url = trim($solr_server_url,'/').'/'.$solr_index.'/update/json?commit=true&wt=json';
        	//die($jsonData);
        	//die(print_r(json_decode($jsonData)));
        	
			$postFields = array('stream.body'=>$jsonData);
			$ch = curl_init($Url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			$output = json_decode($output,true);
			curl_close($ch);
			
			if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0){
				$this->_stats[$store->getId()] = 'Store('.$store->getName().'): '.$this->_stats[$store->getId()].' products was indexed.<br />';
			}								
		}
	}	
	public function getJsonDocument($store){
		
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $store->getId());
        $solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $store->getId());
        	
		if (empty($solr_index) || empty($solr_server_url)){
	       	Mage::getSingleton("core/session")->addSuccess('Solr Server Url is empty or Magento store and Solr index not yet mapped.');
			throw new Exception('error');
			return false;
	    }
	    
	    $Url = trim($solr_server_url,'/').'/'.$solr_index.'/update/json?commit=true&wt=json';
	    $SolrQueryUrl = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q=*:*&fl=products_id&rows=1&wt=json';
		
        $collection = Mage::getModel('catalog/product')->getCollection()
		->addAttributeToSelect('*')
		//->addAttributeToFilter('entity_id', array('in' => array(164)))
		->setStore($store); // set the offset (useful for pagination)
		
		$documents = "{";
		$startPoint = 0;
		$index = 1;
		
		$boostFields = Mage::getStoreConfig('webmods_solrsearch_boost/settings/enabled_fields', 0);
		$boostFieldsArray = explode(',', $boostFields);
		$searchFields = Mage::getStoreConfig('webmods_solrsearch_fields/settings/enabled_fields', 0);
		$searchFieldsArray = explode(',', $searchFields);
		$sortFields = Mage::getStoreConfig('webmods_solrsearch_fields/settings/sorts_fields', 0);
		$sortFieldsArray = explode(',', $sortFields);
		$facetsFields = Mage::getStoreConfig('webmods_solrsearch_fields/settings/facet_fields', 0);
		$facetsFieldsArray = explode(',', $facetsFields);
		
		$indexFieldsArray = array_merge($boostFieldsArray,$searchFieldsArray,$sortFieldsArray,$facetsFieldsArray);
		$indexFieldsArray = array_unique($indexFieldsArray);
		
		foreach ($indexFieldsArray as $key=>$item) {
			$item = trim($item);
			if (empty($item)) {
				unset($indexFieldsArray[$key]);
			}
		}
		
		$defaultIndexFieldsArray = array('sku', 'name', 'price', 'image');
		
		$indexFieldsArray = array_merge($indexFieldsArray, $defaultIndexFieldsArray);
		
		//loop attributes
		foreach ($collection as $product) {
			$_product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
			$atributes = $_product->getAttributes();
			
			foreach ($atributes as $key=>$atributeObj) {
				$backendType = $atributeObj->getBackendType();
				$frontEndInput = $atributeObj->getFrontendInput();
				$attributeCode = $atributeObj->getAttributeCode();
				
				if (!in_array($attributeCode, $indexFieldsArray)) continue;
				
				if ($backendType == 'int') {
					$backendType = 'varchar';
				}
				
				$attributeKey = $key.'_'.$backendType;
				$attributeKeyFacets = $key.'_facets';
				$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
				if (!empty($attributeVal)) {
					if($frontEndInput == 'multiselect') {
						$attributeValFacets = @explode(',', $attributeVal);
					}else {
						$attributeValFacets = $attributeVal;
					}
					
					if ($backendType == 'datetime') {
						$attributeVal = date("Y-m-d\TG:i:s\Z", $attributeVal);
					}
					
					$docData[$attributeKey] = $attributeVal;
					if ($backendType != 'datetime' && $backendType != 'static' && $frontEndInput != 'gallery' && $frontEndInput != 'textarea' && $frontEndInput != 'hidden' && $frontEndInput != 'date') {
					$docData[$attributeKeyFacets] = $attributeValFacets;
					}
				}
				
			}
			
			$cats = $_product->getCategoryIds();
			$catNames = array();
			$categoryPaths = array();
			foreach ($cats as $category_id) {
			    $_cat = Mage::getModel('catalog/category')->load($category_id) ;
			    $catNames[] = $_cat->getName();
			    $categoryPaths[] = $this->getCategoryPath($_cat);
			} 
			$docData['category'] = $catNames;
			$docData['category_path'] = $categoryPaths;
			//die($docData['category']);
			$docData['products_id'] = $product->getId();
			$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";
			
			if ($index >= $startPoint+5) {
				$jsonData = trim($documents,",").'}';
				
				//$jsonData = 'Vallues';
				$this->addProcess('solr_index_commit_data', array('solrurl'=>$Url,'jsondata'=>$jsonData, 'queryurl'=>$SolrQueryUrl));
				$startPoint = $index;
				$documents = "{";
			}
			$index++;			
		}
		return true;
	}    
	public function getCategoryPath($category){
		$currentCategory = $category;
		$categoryPath = $category->getName();
		while ($category->getParentId() > 0){
			
			$category = $category->getParentCategory();
			if ($category->getParentId() > 0){
				$categoryPath = $category->getName().'/'.$categoryPath;
			}
		}
		return $categoryPath.'/'.$currentCategory->getId();
	}
	
    public function reindexAll(){

        ini_set('max_execution_time', 18000);
        ini_set('mysql.connect_timeout', 18000);
        ini_set('default_socket_timeout', 18000);
		
		$stores = Mage::app()->getStores();
		
        foreach ($stores as $store) {
           $this->_reindexStore($store);
        }
        
		$message = '';
		foreach ($this->_stats as $value) {
			if (!is_numeric($value)) {
				$message .= $value;
			}
		}
		if (!empty($message) > 0){
			Mage::getSingleton("core/session")->addSuccess($message);
			return true;
		}else{
			Mage::getSingleton("core/session")->addSuccess('Batch files was built and stored in folder var/solrbridge_processes.Please hit the url http://youdomain/magentofolder/sbbatch.php to start fulling data to Solr for indexing.');
			return false;
		}
    }

    protected function _reindexStore($store){
		$storeId = $store->getId();
        $solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $storeId);
        if (empty($solr_index)) return false;
        
        $this->getJsonDocument($store); //generate batch process files
        $solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $store->getId());
        $solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $store->getId());
	
        $Url = trim($solr_server_url,'/').'/'.$solr_index.'/update?stream.body=<delete><query>*:*</query></delete>&commit=true';
		$ch = curl_init($Url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
        //$this->solr_index_commit_data($jsonDocuments,$store);
    }
}