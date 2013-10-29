<?php
class WebMods_Solrsearch_Block_Result extends Mage_Catalog_Block_Product_List
{

	protected $facetFieldsLabels;
	
	protected $_productCollection;
	
	protected $_solrModel = null;
	
	protected $_solrData = null;
	
	protected $_sortDirection = 'asc';
	
	protected function _construct()
    {
    	$this->setTemplate('solrsearch/result.phtml');
    	
    	$this->_solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $this->_solrModel->buildRequestUrl($store);
    	$this->_solrData = $this->_solrModel->doRequest($url, $store);

    }

	public function _prepareLayout()
    {
    	// add Home breadcrumb
    	if (Mage::app()->getFrontController()->getRequest()->getRouteName() === 'solrsearch') {
    		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
    		if ($breadcrumbs) {
    			$title = $this->__("Search results for: '%s'", $this->helper('catalogsearch')->getQueryText());
    		
    			$breadcrumbs->addCrumb('home', array(
    					'label' => $this->__('Home'),
    					'title' => $this->__('Go to Home Page'),
    					'link'  => Mage::getBaseUrl()
    			))->addCrumb('search', array(
    					'label' => $title,
    					'title' => $title
    			));
    		}
    		
    		// modify page title
    		$title = $this->__("Search results for: '%s'", $this->helper('solrsearch')->getEscapedQueryText());
    		$this->getLayout()->getBlock('head')->setTitle($title);
    	}
    	else
    	{
    		$this->getLayout()->createBlock('catalog/breadcrumbs');
    	}
        return parent::_prepareLayout();
    }

    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {	
    	if (is_null($this->_productCollection)) {
    		$orderby = $this->getOrderBy();
    		
    		$direction = $this->getOrderByDirection();
    		
    		$documents = $this->_solrData['response']['docs'];
    		
    		$productIds = array();
    		if(is_array($documents) && count($documents) > 0) {
    			foreach ($documents as $_doc) {
    				$productIds[] = $_doc['products_id'];
    			}
    		}
    		
    		$collection = Mage::getResourceModel('catalog/product_collection');
    		 
    		$collection->addAttributeToFilter('entity_id', array('in' => $productIds));
    		$collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
    		->addMinimalPrice()
    		->addFinalPrice()
    		->addPriceData()
    		->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
    		//->addFieldToFilter('is_in_stock', 1)
    		->addFieldToFilter(
    				array(
    						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH),
    						array('attribute'=>'visibility','eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH)
    				)
    		)
    		->addTaxPercents();
    		if (empty($orderby)){
    			$collection->getSelect()->order("find_in_set(e.entity_id,'".implode(',',$productIds)."')");
    		}else {
    			$collection->addAttributeToSort($orderby, strtoupper($direction));
    		}

    		$this->_productCollection = $collection;
    	}
    	
    	//die($this->_productCollection->getSelect());
    
    	return $this->_productCollection;
    }
    
    protected function getProducts(){
    	
    	$solrModel = Mage::getModel('solrsearch/solr');
    	$store = Mage::app()->getStore();
    	$url = $solrModel->buildRequestUrl($store);
    	$returnData = $solrModel->doRequest($url, $store);
    	
		$facetFieldsLabels['category_facet'] = $this->__('Category');	
		
		Mage::getSingleton('core/session')->setSolrFacetFieldsLabels($facetFieldsLabels);
		
		$toolbar = $this->getToolbarBlock();
		$currentPage = 1;//$toolbar->getCurrentPage()?$toolbar->getCurrentPage():$_GET['p'];
		if(!empty($params['p'])){
			$currentPage = $params['p'];
		}
		$itemsPerPage= 9;//$toolbar->getItemPerPage()?$toolbar->getItemPerPage():9;
		$start = $itemsPerPage * ($currentPage - 1);
		
		if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
			return $returnData;
		}else{
			$url = trim($solr_server_url,'/').'/'.$solr_index.'/select/?q='.urlencode(strtolower($returnData['spellcheck']['suggestions']['collation']));
			
			$url .= '&facet.field=category_facet';	
			$returnData = $solrModel->doRequest($url,$arguments);
		}
		return $returnData;
    }
    
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
    	$params = $this->getRequest()->getParams();
    	 
    	//order by
    	
    	$orderby = $this->getOrderBy();
    	
    	//direction
    	$direction = $this->getOrderByDirection();
    	
    	//mode
    	$mode = "grid";
    	$mode = Mage::getSingleton('core/session')->getSolrSearchResultMode();
    	if(isset($params['mode']) && !empty($params['mode'])) {
    		$mode = $params['mode'];
    	}
    	Mage::getSingleton('core/session')->setSolrSearchResultMode($mode);
    	
    	//Assign data to block
    	$toolbar = $this->getLayout()->createBlock('solrsearch/result_toolbar', microtime());
    	
    	$toolbar->setData('mode', $mode);
    	$toolbar->setData('direction', $direction);
    	$toolbar->setData('orderby', $orderby);
    	$toolbar->setSolrData($this->_solrData);
    	
        return $toolbar;
    }
    
    public function getOrderBy() {
    	$params = $this->getRequest()->getParams();
    	
    	$orderby = "";
    	$orderby = Mage::getSingleton('core/session')->getSolrSortOrderBy();
    	 
    	if(isset($params['orderby']) && !empty($params['orderby'])) {
    		$orderby = $params['orderby'];
    	}
    	Mage::getSingleton('core/session')->setSolrSortOrderBy($orderby);
    	
    	return $orderby;
    }
    
    public function getOrderByDirection() {
    	$params = $this->getRequest()->getParams();
    	 
    	$direction = "asc";
    	$direction = Mage::getSingleton('core/session')->getSolrSortOrderDirection();
    	if(isset($params['direction']) && !empty($params['direction'])) {
    		$direction = $params['direction'];
    	}
    	Mage::getSingleton('core/session')->setSolrSortOrderDirection($direction);
    	 
    	return $direction;
    }
    
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getOptionsBlock()
    {
        $block = $this->getLayout()->createBlock('solrsearch/result_options', microtime());
        return $block;
    }
	/**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getFacesBlock()
    {
        $block = $this->getLayout()->getBlock('searchfaces');
        return $block;
    }
    
    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    	$toolbar = $this->getToolbarBlock();
    
    	// called prepare sortable parameters
    	//$collection = $this->_getProductCollection();
    
    	$this->setChild('toolbar', $toolbar);
    	
    	$facetsBlock = $this->getFacesBlock();
    	
    	$facetsBlock->setData('solrdata', $this->_solrData);
    	
    	$this->setData('solrdata', $this->_solrData);
    	
    	Mage::dispatchEvent('catalog_block_product_list_collection', array(
    	'collection' => $this->_getProductCollection()
    	));
    
    	$this->_getProductCollection()->load();
    
    }

	/**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getOptionsHtml()
    {
        return $this->getChildHtml('options');
    }
    public function setFacetFieldsLabels($facetFieldsLabels){
    	$this->facetFieldsLabels = $facetFieldsLabels;
    }
	public function getFacetFieldsLabels(){
    	return $this->facetFieldsLabels;
    }
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
    	return $this->_getProductCollection()->load();
    }
    
    public function getTitleBlock()
    {
    	$titleBlockHtml = $this->getChildHtml('solrsearch_result_title');
    	return $titleBlockHtml;
    }
}