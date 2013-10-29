<?php
/**
 * @category SolrBridge
 * @package Solrbridge_Search
 * @author	Hau Danh
 * @copyright	Copyright (c) 2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Model_Resource_Solr extends Mage_Core_Model_Resource_Db_Abstract
{
	public $_solrServerUrl = 'http://localhost:8080/solr/';
	
	public $core = 'english';
    
    public function _construct()
    {
        $this->_init('solrsearch/logs', 'logs_id');
    }
    
    public function getSolrServerUrl(){
    	$solr_server_url = Mage::helper('solrsearch')->getSettings('solr_server_url');
    	return $solr_server_url;
    }
    
    /**
     * Ping to see if solr server available or not
     * @param string $core
     */
    public function pingSolrCore($solrcore = 'english')
    {
    	$solr_server_url = $this->getSolrServerUrl();
    	$pingUrl = trim($solr_server_url,'/').'/'.$solrcore.'/admin/ping?wt=json';
    	$result = $this->doRequest($pingUrl);
    	
    	if (isset($result['status']) && $result['status'] == 'OK') {
    		return true;
    	}
    	return false;
    }
    
    /**
     * Get solr core statistic to find how many documents exist
     * @param string $solr_index
     */
    public function getSolrLuke($solrcore) {
    	$solr_server_url =$this->getSolrServerUrl();
    
    	$Url = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id&rows=1&wt=json';
    
    	$returnData = $this->doRequest($Url);
    	return $returnData;
    }
    
    /**
     * Request Solr
     * @param string $url
     * @param mixed $postFields
     * @return array
     */
    public function doRequest($url, $postFields = null, $type='array'){
    
    	$sh = curl_init($url);
    	curl_setopt($sh, CURLOPT_HEADER, 0);
    	if(is_array($postFields)) {
    		curl_setopt($sh, CURLOPT_POST, true);
    		curl_setopt($sh, CURLOPT_POSTFIELDS, $postFields);
    	}
    	curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
    	
    	curl_setopt( $sh, CURLOPT_FOLLOWLOCATION, true );
    	if ($type == 'json') {
    		curl_setopt( $sh, CURLOPT_HEADER, true );
    	}
    	
    	curl_setopt( $sh, CURLOPT_USERAGENT, isset($_GET['user_agent']) ? $_GET['user_agent'] : $_SERVER['HTTP_USER_AGENT'] );
    
    	$this->setupSolrAuthenticate($sh);
    	
    	if ($type == 'json') {
    		list( $header, $contents ) = @preg_split( '/([\r\n][\r\n])\\1/', curl_exec($sh), 2 );
    		$output = preg_split( '/[\r\n]+/', $contents );
    	}else{
    		$output = curl_exec($sh);
    		$output = json_decode($output,true);
    	}
    	
    	curl_close($sh);
    	return $output;
    }
    
    /**
     * Setup Solr authentication user/pass if neccessary
     * @param unknown_type $sh
     */
    public function setupSolrAuthenticate(&$sh) {
    	$isAuthentication = 0;
    	$authUser = '';
    	$authPass = '';
    		
    	$isAuthenticationCache = Mage::app()->loadCache('solr_bridge_is_authentication');
    	if ( isset($isAuthenticationCache) && !empty($isAuthenticationCache) ) {
    		$isAuthentication = $isAuthenticationCache;
    		$authUser = Mage::app()->loadCache('solr_bridge_authentication_user');
    		$authPass = Mage::app()->loadCache('solr_bridge_authentication_pass');
    	}else {
    		// Save data to cache
    		$isAuthentication = Mage::helper('solrsearch')->getSettings('solr_server_url_auth');
    		$authUser = Mage::helper('solrsearch')->getSettings('solr_server_url_auth_username');
    		$authPass = Mage::helper('solrsearch')->getSettings('solr_server_url_auth_password');
    
    		Mage::app()->saveCache($isAuthentication, 'solr_bridge_is_authentication', array(), 60*60*24);
    		Mage::app()->saveCache($authUser, 'solr_bridge_authentication_user', array(), 60*60*24);
    		Mage::app()->saveCache($authPass, 'solr_bridge_authentication_pass', array(), 60*60*24);
    	}
    		
    	if (isset($isAuthentication) && $isAuthentication > 0 ) {
    		curl_setopt($sh, CURLOPT_USERPWD, $authUser.':'.$authPass);
    		curl_setopt($sh, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    	}
    }
}