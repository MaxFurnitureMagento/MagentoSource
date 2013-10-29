<?php 
class WebMods_Solrsearch_Model_Mysql4_Indexer_Solr extends Mage_Index_Model_Mysql4_Abstract{
	public function __construct(){
		//$this->_setResource('WebMods_Solrsearch');
		parent::__construct();
	}	
	
	public function  _construct(){
		
	}
	
	public function reindexAll(){
		//generate image icon
				
		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);
		$solr_index = Mage::getStoreConfig('webmods_solrsearch/settings/solr_index', 0);
		$solr_index = "main";
		$opts = array(
			  'http'=>array(
			    'method'=>"GET",
		)
		);
			
		//$context = stream_context_create($opts);
			
			
			
		// is cURL installed yet?
		if (!function_exists('curl_init')){
			Mage::getSingleton("core/session")->addError('CURL have not installed yet in this server, this caused the Solr index data out of date.');
			
		}else{
			// Now set some options (most are optional)
			// Now set some options (most are optional)
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/dataimport?command=full-import&wt=json';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				//$output = json_decode($output);
				curl_close($ch);
				if (empty($output)) {
				return Mage::getSingleton("core/session")->addError('The solr server '.$solr_server_url.' seem not accessable.');
				}
			while (true){
				// Now set some options (most are optional)
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/dataimport?command=stats&wt=json';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				$output = json_decode($output);
				curl_close($ch);
				if(isset($output->statusMessages->_empty_)){
					if (preg_match("/^Indexing completed./i", $output->statusMessages->_empty_)) {
					    Mage::getSingleton("core/session")->addSuccess($output->statusMessages->_empty_,'success');
					    break;
					}else if (preg_match("/^Indexing failed./i", $output->statusMessages->_empty_)) {
					    Mage::getSingleton("core/session")->addSuccess($output->statusMessages->_empty_,'success');
					    throw new Exception($output->statusMessages->_empty_);
					    break;
					}
				}
			}
		}
		return true;
	}
	
	public function getName(){
		Mage::helper('WebMods_Solrsearch')->__('Solr Index');
	}
	
	public function getDescription(){
		Mage::helper('WebMods_Solrsearch')->__('Solr Bridge Index');
	}
	
	protected function _registerEvent(Mage_Index_Model_Event $event){
		return $event;
	}
	
	protected function _processEvent(Mage_Index_Model_Event $event){
		return $event;
	}
	
	/**
     * Retrieve connection for read data
     */
    protected function _getReadAdapter(){
    	
    }

    /**
     * Retrieve connection for write data
     */
    protected function _getWriteAdapter(){
    	
    }

}
?>