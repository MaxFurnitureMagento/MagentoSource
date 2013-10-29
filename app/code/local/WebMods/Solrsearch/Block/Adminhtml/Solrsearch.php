<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Block_Adminhtml_Solrsearch extends Mage_Core_Block_Template
{
	protected $ultility = null;
	
	public function __construct()
	{
		$this->ultility = Mage::getModel('solrsearch/ultility');
		$this->setTemplate('solrsearch/solrsearch.phtml');
	}

	/**
	 * Return active solr cores
	 * @return array
	 */
	public function getActiveSolrCores()
	{
		$availableCores = $this->ultility->getAvailableCores();
		$activeSolrCores = array();
		
		foreach ($availableCores as $solrcore => $infoArray)
		{
			if ( isset($infoArray['stores']) && strlen(trim($infoArray['stores'], ',')) > 0 )
			{
				$infoArray['stores'] = trim($infoArray['stores'], ',');
				$activeSolrCores[$solrcore] = $infoArray;
			}
		}
		
		return $activeSolrCores;
	}

	public function getCollectionData($store) {
		
		$collectionMetaData = $this->ultility->getCollectionMetaDataByStoreId($store->getId());
		
		return $collectionMetaData;
	}

	public function getSolrLuke($solr_index) {
		$solr_server_url = Mage::helper('solrsearch')->getSettings('solr_server_url');

		$Url = trim($solr_server_url,'/').'/'.$solr_index.'/admin/luke/?numTerms=0&fq=dummy&wt=json';

		$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($Url);
		return $returnData;
	}
}