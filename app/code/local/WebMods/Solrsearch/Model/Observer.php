<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Model_Observer {
	const FLAG_SHOW_CONFIG = 'showConfig';
	const FLAG_SHOW_CONFIG_FORMAT = 'showConfigFormat';
	const BATCH_DIRECTORY = '';

	private $request;

	protected $ultility = null;

	public function __construct()
	{
		$this->ultility = Mage::getModel('solrsearch/ultility');
	}

	public function checkForConfigRequest($observer) {
		$this->request = $observer->getEvent()->getData('front')->getRequest();
		if($this->request->{self::FLAG_SHOW_CONFIG} === 'true'){
			$this->setHeader();
			$this->outputConfig();
		}
	}

	public function addSearchWeightFieldToAttributeForm($observer){
		$weights = array(
				array(
						'value' => "",
						'label' => Mage::helper('catalog')->__('Default')
				),
				array(
						'value' => 200,
						'label' => Mage::helper('catalog')->__('1')
				),
				array(
						'value' => 190,
						'label' => Mage::helper('catalog')->__('2')
				),
				array(
						'value' => 180,
						'label' => Mage::helper('catalog')->__('3')
				),
				array(
						'value' => 170,
						'label' => Mage::helper('catalog')->__('4')
				),
				array(
						'value' => 160,
						'label' => Mage::helper('catalog')->__('5')
				),
				array(
						'value' => 150,
						'label' => Mage::helper('catalog')->__('6')
				),
				array(
						'value' => 140,
						'label' => Mage::helper('catalog')->__('7')
				),
				array(
						'value' => 130,
						'label' => Mage::helper('catalog')->__('8')
				),
				array(
						'value' => 120,
						'label' => Mage::helper('catalog')->__('9')
				),
				array(
						'value' => 110,
						'label' => Mage::helper('catalog')->__('10')
				),
				array(
						'value' => 100,
						'label' => Mage::helper('catalog')->__('11')
				),
				array(
						'value' => 90,
						'label' => Mage::helper('catalog')->__('12')
				),
				array(
						'value' => 80,
						'label' => Mage::helper('catalog')->__('13')
				),
				array(
						'value' => 70,
						'label' => Mage::helper('catalog')->__('14')
				),
				array(
						'value' => 60,
						'label' => Mage::helper('catalog')->__('15')
				),
				array(
						'value' => 50,
						'label' => Mage::helper('catalog')->__('16')
				),
				array(
						'value' => 40,
						'label' => Mage::helper('catalog')->__('17')
				),
				array(
						'value' => 30,
						'label' => Mage::helper('catalog')->__('18')
				),
				array(
						'value' => 20,
						'label' => Mage::helper('catalog')->__('19')
				),
				array(
						'value' => 10,
						'label' => Mage::helper('catalog')->__('20')
				)
		);

		$fieldset = $observer->getForm()->getElement('front_fieldset');
		$attribute = $observer->getAttribute();
		$attributeCode = $attribute->getName();

		$fieldset->addField('solr_search_field_weight', 'select', array(
				'name'      => 'solr_search_field_weight',
				'label'     => Mage::helper('solrsearch')->__('Solr Search weight'),
				'title'     => Mage::helper('solrsearch')->__('Solr Search weight'),
				'values'    => $weights,
		));

		$fieldset->addField('solr_search_field_boost', 'textarea', array(
				'name'      => 'solr_search_field_boost',
				'label'     => Mage::helper('solrsearch')->__('Solr Search boost'),
				'title'     => Mage::helper('solrsearch')->__('Solr Search booost'),
				//'values'    => $weights,
				'note'  => Mage::helper('solrsearch')->__('Example: Sony:1. Each pair of key:value separted by linebreak, value will be 1-20')
		));
	}
	public function productDeleteBefore($observer){
		$product = $observer->getEvent()->getProduct();
			
	}
	/**
	 * When a magento product deleted
	 * @param unknown $observer
	 */
	public function productDeleteAfter($observer){

		$availableCores = array_keys($this->ultility->getAvailableCores());

		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);

		$product = $observer->getEvent()->getProduct();

		foreach ($availableCores as $solrcore) {

			$Url = trim($solr_server_url,'/').'/'.$solrcore.'/update?stream.body=<delete><query>products_id:'.$product->getId().'</query></delete>&commit=true';
			Mage::getResourceModel('solrsearch/solr')->doRequest($Url);
			//Remove product id from log table
			$this->ultility->removeLogProductId($product->getId());
		}
	}
	/**
	 * When added/update a product
	 * @param unknown $observer
	 */
	public function productAddUpdate($observer){

		$_product = $observer->getProduct();

		$getSolrIndexesConfigArray = $this->getSolrIndexesConfigArray();

		$params = array();

		$solr_server_url = Mage::getStoreConfig('webmods_solrsearch/settings/solr_server_url', 0);

		//Loop thru solr cores
		foreach ($getSolrIndexesConfigArray as $core) {
			if( !empty($core['stores']) ) {
				$storeIdArray = explode(',', $core['stores']);

				foreach ($storeIdArray as $storeid) {
					$storeObject = Mage::getModel('core/store')->load($storeid);

					$collection = $this->ultility->getProductCollectionByProduct($_product, $storeid);

					$dataArray = $this->ultility->parseJsonData($collection, $storeObject);

					$jsonData = $dataArray['jsondata'];

					$solrcore = $core['key'];

					$params['solr_update_url'] = trim($solr_server_url,'/').'/'.$solrcore.'/update/json?commit=true&wt=json';

					$params['solr_query_url'] = trim($solr_server_url,'/').'/'.$solrcore.'/select/?q=*:*&fl=products_id,store_id&start=0&rows=1&wt=json';

					$returnNoOfDocuments = $this->solr_index_commit_data($jsonData, $params['solr_update_url'], $params['solr_query_url']);
				}

			}
		}
		$this->ultility->removeLogProductId($_product->getId());
	}

	private function setHeader() {
		$format = isset($this->request->{self::FLAG_SHOW_CONFIG_FORMAT}) ?
		$this->request->{self::FLAG_SHOW_CONFIG_FORMAT} : 'xml';
		switch($format){
			case 'text':
				header("Content-Type: text/plain");
				break;
			default:
				header("Content-Type: text/xml");
		}
	}


	public function getSolrIndexesConfigArray() {
		$solrIndexesConfigArray = Mage::getStoreConfig('webmods_solrsearch_indexes');

		$solrIndexesConfigArrayData = array();

		foreach ($solrIndexesConfigArray as $key=>$values) {
			$coreData = array();
			$coreData['key'] = $key;
			$coreData['stores'] = trim($values['stores'], ',');
			$coreData['label'] = $values['label'];
			if (trim($coreData['stores']) == '' || empty($coreData['label'])) {
				continue;
			}

			$solrIndexesConfigArrayData[] = $coreData;
		}

		return $solrIndexesConfigArrayData;
	}

	public function solr_index_commit_data($jsonData, $updateurl, $queryurl){

		if (!function_exists('curl_init')){
			echo 'CURL have not installed yet in this server, this caused the Solr index data out of date.';
			exit;
		}else{
			if(!isset($jsonData) && empty($jsonData)) {
				return 0;
			}

			$postFields = array('stream.body'=>$jsonData);

			$output = Mage::getResourceModel('solrsearch/solr')->doRequest($updateurl, $postFields);

			if (isset($output['responseHeader']['QTime']) && intval($output['responseHeader']['QTime']) > 0){
				$returnData = Mage::getResourceModel('solrsearch/solr')->doRequest($queryurl);

				if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
					if (is_array($returnData['response']['docs'])) {
						foreach ($returnData['response']['docs'] as $doc) {
							$this->ultility->logProductId($doc['products_id'], $doc['store_id']);
						}
					}
					return $returnData['response']['numFound'];
				}
			}else {
				return 0;
			}
		}
	}
}