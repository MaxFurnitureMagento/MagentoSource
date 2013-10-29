<?php
class WebMods_Solrsearch_Model_Indexer_Delta extends Mage_Index_Model_Indexer_Abstract{
	protected $_matchedEntities = array();
	
	protected function _construct() {
        //$this->_init('solrsearch/indexer_solr');
        return parent::_construct();
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
        return Mage::getResourceSingleton('solrsearch/indexer_delta');
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
		return Mage::helper('solrsearch')->__('Solr Bridge Update Documents Indexer');
	}
	
	public function reindexAll(){
		
		ini_set('max_execution_time', 18000);
		ini_set('mysql.connect_timeout', 18000);
		ini_set('default_socket_timeout', 18000);
		
		$storeIds = array_keys(Mage::app()->getStores());
        foreach ($storeIds as $storeId) {
            $this->_reindexStore($storeId);
        }
        
        return true;
	}
	
	protected function _reindexStore($storeId = ""){		
		
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', $storeId);
		$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', $storeId);		
		if (!$solr_index) return false;
		$opts = array(
			'http'=>array(
			    'method'=>"GET",
			)
		);
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
		}else{
			// Now set some options (most are optional)
			$Url = trim($solr_server_url,'/').'/'.$solr_index.'/dataimport?command=delta-import&wt=json';
			$ch = curl_init($Url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			if (empty($output)) {
				return Mage::getSingleton("core/session")->addError('The solr server '.$solr_server_url.' seem not accessable.');
			}
			$time_start = microtime(true);
			
			while (true){
				// Now set some options (most are optional)
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/dataimport?command=stats&wt=json';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$raw_output = curl_exec($ch);
				$output = json_decode($raw_output);
				curl_close($ch);
				$output = json_decode($raw_output,true);
				if (isset($output['status']) && $output['status'] !== 'busy') {
					Mage::getSingleton("core/session")->addSuccess('Total Changed Documents: '.$output['statusMessages']['Total Changed Documents'],'success');
					break;
				}
			}						
		}
		//return true;
	}
}