<?php
class WebMods_Solrsearch_Model_Indexer_Thumb extends Mage_Index_Model_Indexer_Abstract{
	protected $_matchedEntities = array();
	
	protected function _construct() {
        $this->_init('solrsearch/indexer_thumb');
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
        return Mage::getResourceSingleton('solrsearch/indexer_thumb');
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
		return Mage::helper('solrsearch')->__('Solr Bridge Thumbnail Indexer');
	}
	
	public function reindexAll(){		
		ini_set('max_execution_time', 18000);
		ini_set('mysql.connect_timeout', 18000);
		ini_set('default_socket_timeout', 18000);
		
		$storeIds = array_keys(Mage::app()->getStores());
        foreach ($storeIds as $storeId) {
            $this->_reindexStore($storeId);
        }
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
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q=*:*&fl=products_id&rows=-1&wt=json';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				if (empty($output)) {
				return Mage::getSingleton("core/session")->addError('The solr server '.$solr_server_url.' seem not accessable.');
				}
				
				$result = json_decode($output,true);
				
				if (empty($result['response']['numFound']) || intval($result['response']['numFound']) < 1) {
					Mage::getSingleton("core/session")->addError('there is no thumb processed...number of product found is 0');
					return true;
				}
				
				$Url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q=*:*&fl=products_id&rows='.$result['response']['numFound'].'&wt=json';
				$ch = curl_init($Url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$output = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($output,true);
				
				$productModel = Mage::getModel('catalog/product');
				$index = 0;
				foreach ($result['response']['docs'] as $doc) {
					$productId = $doc['products_id'];
					$product = $productModel->load($productId);		
					$image = $product->getImage();
					$productImagePath = Mage::getBaseDir("media").DS.'catalog'.DS.'product'.$image;
					if (!file_exists($productImagePath) || empty($image)){
						$productImagePath = Mage::getBaseDir("skin").DS.'frontend'.DS.'base'.DS.'default'.DS.'images'.DS.'catalog'.DS.'product'.DS.'placeholder'.DS.'image.jpg';
					}
					if (!file_exists($productImagePath)){						
						continue;
					}
											 
			
					$productImageThumbPath = Mage::getBaseDir('media').DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
					if (file_exists($productImageThumbPath)) {
						$index++;
						continue;
					}
					$imageResizedUrl = Mage::getBaseUrl("media").DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
					
					$imageObj = new Varien_Image($productImagePath);
					$imageObj->constrainOnly(TRUE);
					$imageObj->keepAspectRatio(TRUE);
					$imageObj->keepFrame(FALSE);
					$imageObj->resize(32, 32);
					$imageObj->save($productImageThumbPath);
					if (file_exists($productImageThumbPath)) {
						$index++;
					}
					$product->reset();
				}				
				Mage::getSingleton("core/session")->addSuccess('Found '.$result['response']['numFound'].' products, processed '.$index.' thumbnails','success');						
		}
		return true;
	}
}