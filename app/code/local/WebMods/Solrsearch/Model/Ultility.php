<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Model_Ultility
{
	public $logfile = null;

	public $logTypes = array(
			'1' => '[ERROR] : ',
			'2' => '[WARNING] : ',
			'3' => '[INFO] : ',
	);

	public $logPath = '/solrbridge';
	
	public $allowCategoryIds = array();
	
	protected $logQuery = true;
	
	public $solrServerUrl = 'http://localhost:8080/solr/';
	
	public $itemsPerCommit = 100;
	
	public $writeLog = false;
	
	public $checkInStock = FALSE;

	public function __construct()
	{
		$solr_server_url = Mage::helper('solrsearch')->getSettings('solr_server_url');
		$this->solrServerUrl = $solr_server_url;
		
		$itemsPerCommitConfig = Mage::helper('solrsearch')->getSettings('items_per_commit');
		if( intval($itemsPerCommitConfig) > 0 )
		{
			$this->itemsPerCommit = $itemsPerCommitConfig;
		}
		
		$checkInstockConfig =  Mage::helper('solrsearch')->getSettings('check_instock');
		if( intval($checkInstockConfig) > 0 )
		{
			$this->checkInStock = $checkInstockConfig;
		}
		
		$baseDir = '/'.trim(Mage::getBaseDir('var'), '/');
		$solrBridgePath = $baseDir.$this->logPath;
		//check if directory solrbridge exist
		if (!file_exists($solrBridgePath)) {
			mkdir($solrBridgePath);
		}

		$logFilePath = $baseDir.$this->logPath.'/logs-'.date('Y-m-d').'.txt';
		//echo $logFilePath;
		$this->logfile = fopen($logFilePath, 'a+');
	}
	/**
	 * Write log message to file
	 * @param string $message
	 * @param number $logType
	 */
	public function writeLog($message = '', $logType = 0) {
		if ( $logType > 0 && in_array($logType, array_keys($this->logTypes))) {
			$message = $this->logTypes[$logType] . $message;
		}
		if (!empty($message) && $this->writeLog) {
			fwrite($this->logfile, $message."\n");
		}
		return $message."\n";
	}

	/**
	 * Log successed indexed product id into table
	 * @param unknown_type $id
	 * @param unknown_type $store_id
	 */
	public function logProductId($id, $store_id){
		//Log index fields
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$logtable = $resource->getTableName('solrsearch/logs');

		$connectionRead = $resource->getConnection('core_read');

		$results = $connectionRead->query("SELECT * FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND `store_id`=".$store_id." AND `value`=".$id.";");

		$row = $results->fetch();

		if (is_array($row) && $row['logs_id'] > 0) {
			return false;
		}

		$writeConnection->beginTransaction();
		//Log index fields
		$insertArray = array();
		$insertArray['logs_id'] = NULL;
		$insertArray['logs_type'] = 'INDEXEDPRODUCT';
		$insertArray['value'] = $id;
		$insertArray['store_id'] = $store_id;
		$writeConnection->insert($logtable, $insertArray);

		$writeConnection->commit();
	}
	
	public function removeLogProductId($id) {
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$logtable = $resource->getTableName('solrsearch/logs');
		
		$results = $writeConnection->query("DELETE FROM {$logtable} WHERE `logs_type` = 'INDEXEDPRODUCT' AND `value`=".$id.";");
	}
	
	public function getMinimalProductCollection($store)
	{
		$collection = Mage::getResourceModel('catalog/product_collection')
		->addAttributeToSelect('*')
		//->addMinimalPrice()
		->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		->addFieldToFilter(
				array(
						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH),
				))
				->addStoreFilter($store)
				->addWebsiteFilter($store->getWebsiteId());
		
		if ($this->checkInStock) {
			$collection->getSelect()->joinLeft(
					array('stock' => 'cataloginventory_stock_item'),
					"e.entity_id = stock.product_id",
					array('stock.is_in_stock')
			)->where('stock.is_in_stock = 1');
		}
		
		return $collection;
	}

	/**
	 * Get product collection by store id
	 * @param int $store_id
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	function getProductCollectionByStoreId($store_id, $page = 1, $itemsPerPage = 50) {
		$store = Mage::getModel('core/store')->load($store_id);
			
		$collection = $this->getMinimalProductCollection($store);

		$collection->setPage($page, $itemsPerPage);
		
		$collection->addMinimalPrice()
    		->addFinalPrice()
    		->addPriceData()
			->addTierPriceData();

		return $collection->load();
	}
	/**
	 * Update collection
	 * @param int $store_id
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	public function getProductCollectionForUpdate($store_id, $page = 1, $itemsPerPage = 50) {
		$collection = $this->getProductCollectionByStoreId($store_id, $page, $itemsPerPage);

		$timezone = Mage::getStoreConfig('general/locale/timezone', 0);
		$datetime = new DateTime($_POST['lastindextime']);
		$la_time = new DateTimeZone($timezone);
		$datetime->setTimezone($la_time);
		$lastIndexTime = $datetime->format('Y-m-d H:i:s');

		$whereClause = '';

		$resource = Mage::getSingleton('core/resource');
		$logTable = $resource->getTableName('solrsearch/logs');

		$whereClause .= " NOT EXISTS (SELECT `value` FROM `{$logTable}` WHERE `logs_type` = 'INDEXEDPRODUCT' AND value = e.entity_id AND store_id = ".$store_id.")";
			
		$collection->getSelect()->where($whereClause);

		$collection->setPage($page, $itemsPerPage);
		
		if ($this->logQuery) {
			$this->writeLog($collection->getSelect());
		}
		

		return $collection->load();
	}
	/**
	 * Get product collection for only one product
	 * @param unknown $product
	 * @param int $store_id
	 */
	public function getProductCollectionByProduct($product, $store_id){
		
		$store = Mage::getModel('core/store')->load($store_id);
		
		$collection = Mage::getResourceModel('catalog/product_collection');
		 
		$collection->addAttributeToFilter('entity_id', array('in' => array( $product->getId() )));
		$collection->addAttributeToSelect('*')
		->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		->addFieldToFilter(
				array(
						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
				)
		)
		->addStoreFilter($store)
		->addWebsiteFilter($store->getWebsiteId())
		->addTaxPercents();

		if ($this->checkInStock) {
			$collection->getSelect()->joinLeft(
					array('stock' => 'cataloginventory_stock_item'),
					"e.entity_id = stock.product_id",
					array('stock.is_in_stock')
			)->where('stock.is_in_stock = 1');
		}

		return $collection->load();
	}
	
	/**
	 * Get product collection metadata
	 * @param int $store_id
	 * @return array
	 */
	function getCollectionMetaDataByStoreId($store_id) {
		$itemsPerCommit = $this->itemsPerCommit;
		$collection = $this->getProductCollectionByStoreId($store_id);
		$metaDataArray = array();
		$productCount = $collection->getSize();
		$metaDataArray['productCount'] = $productCount;
		$totalPages = ceil($productCount/$itemsPerCommit);
		$metaDataArray['totalPages'] = $totalPages;
	
		return $metaDataArray;
	}
	
	/**
	 * Get product collection metadata
	 * @param int $store_id
	 * @return array
	 */
	function getCollectionMetaDataByStoreIdForUpdate($store_id) {
		$itemsPerCommit = $this->itemsPerCommit;
		$collection = $this->getProductCollectionForUpdate($store_id);
		$metaDataArray = array();
		$productCount = $collection->getSize();
		$metaDataArray['productCount'] = $productCount;
		$totalPages = ceil($productCount/$itemsPerCommit);
		$metaDataArray['totalPages'] = $totalPages;
	
		return $metaDataArray;
	}

	/**
	 * Parse product collection into json
	 * @param unknown_type $collection
	 * @param unknown_type $store
	 * @return array
	 */
	public function parseJsonData($collection, $store) 
	{
		$fetchedProducts = 0;
		//is category name searchable
		$solr_include_category_in_search = Mage::helper('solrsearch')->getSettings('solr_search_in_category');
		//use category for facets
		$use_category_as_facet = Mage::helper('solrsearch')->getSettings('use_category_as_facet');

		$startPoint = 0;
		$index = 1;
		$textSearch = array();

		$documents = "{";

		//loop products
		foreach ($collection as $product) {
			$textSearch = array();

			$_product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($product->getId());
			$atributes = $_product->getAttributes();

			foreach ($atributes as $key=>$atributeObj) {
				$backendType = $atributeObj->getBackendType();
				$frontEndInput = $atributeObj->getFrontendInput();
				$attributeCode = $atributeObj->getAttributeCode();
				$attributeData = $atributeObj->getData();

				if (!$atributeObj->getIsSearchable()) continue; // ignore fields which are not searchable

				if ($backendType == 'int') {
					$backendType = 'varchar';
				}

				$attributeKey = $key.'_'.$backendType;

				$attributeKeyFacets = $key.'_facet';

				if (!is_array($atributeObj->getFrontEnd()->getValue($_product))){
					$attributeVal = strip_tags($atributeObj->getFrontEnd()->getValue($_product));
				}else {
					$attributeVal = $atributeObj->getFrontEnd()->getValue($_product);
					$attributeVal = implode(' ', $attributeVal);
				}

				if ($_product->getData($key) == null)
				{
					$attributeVal = null;
				}

				//Start collect values
				$this->logFields[] = $attributeKey;
					
				if (empty($attributeVal)) {
					unset($docData[$attributeKey]);
					unset($docData[$attributeKeyFacets]);
					unset($docData[$key.'_boost']);
				}else{
					if($frontEndInput == 'multiselect') {
						$attributeValFacets = @explode(',', $attributeVal);
					}else {
						$attributeValFacets = $attributeVal;
					}

					if ($backendType == 'datetime') {
						$attributeVal = date("Y-m-d\TG:i:s\Z", $attributeVal);
					}

					if (!in_array($attributeVal, $textSearch) && $attributeVal != 'None' && $attributeCode != 'status' && $attributeCode != 'sku'){
						$textSearch[] = $attributeVal;
					}

					$docData[$attributeKey] = $attributeVal;

					$docData[$key.'_boost'] = $attributeVal;

					if (
					(isset($attributeData['solr_search_field_weight']) && !empty($attributeData['solr_search_field_weight']))
					||
					(isset($attributeData['solr_search_field_boost']) && !empty($attributeData['solr_search_field_boost']))
					) {
						$docData[$key.'_boost'] = $attributeVal;
					}

					if (
					(isset($attributeData['is_filterable_in_search']) && !empty($attributeData['is_filterable_in_search']) && $attributeValFacets != 'No' && $attributeKey != 'price_decimal' && $attributeKey != 'special_price_decimal')
					) {
						$docData[$attributeKeyFacets] = $attributeValFacets;
						//$docData[$key.'_text'] = $attributeValFacets;
					}
				}

			}

			$rootCatId = $store->getRootCategoryId();

			$rootCat = Mage::getModel('catalog/category')->load($rootCatId);

			if( !isset($this->allowCategoryIds[$store->getId()]) )
			{
				$allowCatIds = Mage::getModel('catalog/category')->getResource()->getChildren($rootCat, true);
				$this->allowCategoryIds[$store->getId()] = $allowCatIds;
				//$this->allowCategoryIds[$store->getId()][] = $rootCatId;
			}


			$cats = $_product->getCategoryIds();
			$catNames = array();
			$categoryPaths = array();
			$categoryIds = array();
			foreach ($cats as $category_id) {
				if (in_array($category_id, $this->allowCategoryIds[$store->getId()])) {
					$_cat = Mage::getModel('catalog/category')->load($category_id) ;
					$catNames[] = $_cat->getName();
					$categoryPaths[] = $this->getCategoryPath($_cat, $store);
					$categoryIds[] = $_cat->getId();
				}
					
			}

			if ($solr_include_category_in_search > 0) {
				$textSearch = array_merge($textSearch, $catNames);
			}
			$sku = $_product->getSku();
			$textSearch[] = $sku;
			$textSearch[] = str_replace(array('-', '_'), '', $sku);
			if ($use_category_as_facet) {
				$docData['category_facet'] = $catNames;
				$docData['category_text'] = $catNames;
				$docData['category_boost'] = $catNames;
			}

			$docData['category_path'] = $categoryPaths;
			$docData['textSearch'] = $textSearch;

			if ($this->getProductPrice($product, $store->getId())) {
				$docData['price_decimal'] = $this->getProductPrice($product, $store->getId());
			}

			if ($_product->getSpecialPrice()) {
				$docData['special_price_decimal'] = $_product->getSpecialPrice();
			}

			$productUrl = $_product->getProductUrl();
			if (strpos($productUrl, 'solrbridge_indexer.php')) {
				$productUrl = str_replace('solrbridge_indexer.php', 'index.php', $productUrl);
			}
			
			$docData['url_path_varchar'] = $productUrl;

			$docData['name_boost'] = $_product->getName();

			$docData['products_id'] = $_product->getId();
			
			$docData['category_id'] = $categoryIds;

			$docData['unique_id'] = $store->getId().'P'.$_product->getId();

			$docData['store_id'] = $store->getId();

			$docData['website_id'] = $store->getWebsiteId();

			$docData['product_status'] = $_product->getStatus();

			$this->generateThumb($_product);
			$documents .= '"add": '.json_encode(array('doc'=>$docData)).",";

			$index++;
			$fetchedProducts++;
		}

		$jsonData = trim($documents,",").'}';

		return array('jsondata'=> $jsonData, 'fetchedProducts' => $fetchedProducts);
	}
	
	/**
	 * Get category path
	 * @param unknown_type $category
	 */
	public function getCategoryPath($category, $store){
		$currentCategory = $category;
		$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId();
		
		while ($category->getParentId() > 0){
			$category = $category->getParentCategory();
			if (in_array($category->getId(), $this->allowCategoryIds[$store->getId()]))
			{
				$category->setStoreId($store->getId());
				$categoryPath = str_replace('/', '_._._',$category->getName()).'/'.$category->getId().'/'.$categoryPath;
			}
		}
		return trim($categoryPath, '/');
	}
	
	/**
	 * Generate product thumbnails
	 * @param unknown_type $product
	 */
	public function generateThumb($product){
		$productId = $product->getId();
		$image = trim($product->getSmallImage());

		if (empty($image)){
			$productImagePath = Mage::getBaseDir("skin").DS.'frontend'.DS.'base'.DS.'default'.DS.'images'.DS.'catalog'.DS.'product'.DS.'placeholder'.DS.'image.jpg';
		}else{
			$productImagePath = Mage::getBaseDir("media").DS.'catalog'.DS.'product'.DS.$image;
		}
		if (!file_exists($productImagePath)){
			$productImagePath = Mage::getBaseDir("skin").DS.'frontend'.DS.'base'.DS.'default'.DS.'images'.DS.'catalog'.DS.'product'.DS.'placeholder'.DS.'image.jpg';
		}
	
		$productImageThumbPath = Mage::getBaseDir('media').DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
		if (file_exists($productImageThumbPath)) {
			unlink($productImageThumbPath);
		}
		$imageResizedUrl = Mage::getBaseUrl("media").DS."catalog".DS."product".DS."sb_thumb".DS.$productId.'.jpg';
	
		$imageObj = new Varien_Image($productImagePath);
		$imageObj->constrainOnly(FALSE);
		$imageObj->keepAspectRatio(TRUE);
		$imageObj->keepFrame(FALSE);
		$imageObj->backgroundColor(array(255,255,255));
		//$imageObj->keepTransparency(TRUE);
		$imageObj->resize(32, 32);
		$imageObj->save($productImageThumbPath);
		if (file_exists($productImageThumbPath)) {
			return true;
		}
	
		return false;
	}
	/**
	 * Get product attribute collection
	 */
	public function getProductAttributeCollection()
	{
		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
		$catalogProductEntityTypeId = $entityType->getEntityTypeId();
		
		$attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($catalogProductEntityTypeId)
		->addSetInfo()
		->getData();
		
		return $attributesInfo;
	}
	
	/**
	 * Retrive available stores
	 * @return array
	 */
	public function getAvailableCores() {
		$cores = $solrIndexesConfigArray = Mage::getStoreConfig('webmods_solrsearch_indexes');
		return $cores;
	}
	
	/**
	 * Get product price
	 * @param unknown $_product
	 * @param unknown $storeId
	 * @return decimal
	 */
	public function getProductPrice($_product, $storeId)
	{
		$_coreHelper = Mage::helper('core');
		$_weeeHelper = Mage::helper('weee');
		$_taxHelper  = Mage::helper('tax');
	
		$_storeId = $_product->getStoreId();
		$_id = $_product->getId();
		$_weeeSeparator = '';
		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
		$_minimalPriceValue = $_product->getMinimalPrice();
		$_minimalPrice = $_taxHelper->getPrice($_product, $_minimalPriceValue, $_simplePricesTax);
		
		$returnPrice = $_product->getFinalPrice();
		
		if ($_product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
			
			return $_product->getMinimalPrice();
			
		}
		
		if (!$_product->isGrouped()) {
			$_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($_product);
			
			if ($_weeeHelper->typeOfDisplay($_product, array(Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL, 4)))
			{
				$_weeeTaxAmount = $_weeeHelper->getAmount($_product);
			    $_weeeTaxAttributes = $_weeeHelper->getProductWeeeAttributesForDisplay($_product);
			}
			$_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
			    
			if ($_weeeHelper->isTaxable() && !$_taxHelper->priceIncludesTax($_storeId))
			{
			    $_attributes = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null, null, true);
			    $_weeeTaxAmountInclTaxes = $_weeeHelper->getAmountInclTaxes($_attributes);
			}
			

			$_price = $_taxHelper->getPrice($_product, $_product->getPrice());
			$_regularPrice = $_taxHelper->getPrice($_product, $_product->getPrice(), $_simplePricesTax);
			$_finalPrice = $_taxHelper->getPrice($_product, $_product->getFinalPrice());
			$_finalPriceInclTax = $_taxHelper->getPrice($_product, $_product->getFinalPrice(), true);
			$_weeeDisplayType = $_weeeHelper->getPriceDisplayType();
			
			if ($_finalPrice >= $_price)
			{
				if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)) // including
				{
				     $returnPrice = $_price + $_weeeTaxAmount;
				}
				else
				{
					 if ($_finalPrice == $_price)
					 {
					 	$returnPrice = $_price;
					 }else{
					 	$returnPrice = $_finalPrice;
					 }
				}
			}
			else
			{
				$_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);
				
				if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0))
				{
					$returnPrice = $_regularPrice + $_originalWeeeTaxAmount;
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)) // incl. + weee
				{
					$returnPrice = $_regularPrice + $_originalWeeeTaxAmount;
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4))
				{
					$returnPrice = $_regularPrice + $_originalWeeeTaxAmount;
				}
				elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)) // excl. + weee + final
				{
					$returnPrice = $_regularPrice;
				}
				else
				{
					$returnPrice = $_finalPriceInclTax;
				}
			}
		}
		else //Grouped product
		{
			$_exclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue);
			$_inclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue, true);
			
			
			if ($_minimalPriceValue){
				$_showPrice = $_inclTax;
				if (!$_taxHelper->displayPriceIncludingTax()) {
					$_showPrice = $_exclTax;
				}
				
				$returnPrice = $_showPrice;
			}
		}
		
		return $returnPrice;
	}
}
?>