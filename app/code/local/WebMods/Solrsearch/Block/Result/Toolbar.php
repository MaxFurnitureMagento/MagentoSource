<?php
class WebMods_Solrsearch_Block_Result_Toolbar extends Mage_Core_Block_Template
{
	protected $solrData = array();
	protected $totalDocuments = 0;
	protected $itemPerPage = 0;
	protected $totalPage = 0;
	protected $currentPage = 1;
	protected function _construct()
    {
    	$is_ajax = Mage::getStoreConfig('webmods_solrsearch/settings/use_ajax_result_page', 0);
    	if (intval($is_ajax) > 0) {
    		$this->setTemplate('solrsearch/result/toolbar.phtml');
    	}else{
    		$this->setTemplate('solrsearch/standard/toolbar.phtml');
    	}
    	
    }
    
    public function setSolrData($data){
    	$this->solrData = $data;
    }
    
    public function getSolrData(){
    	return $this->solrData;
    }
    
	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
    	$this->prepareData();
    	return parent::_beforeToHtml();
    }
    protected function prepareData(){
    	$solrData = $this->getSolrData();
    	
    	$itemsPerPage = Mage::getStoreConfig('webmods_solrsearch/settings/items_per_page', 0);
    	
    	$itemPerPage = empty($solrData['responseHeader']['rows'])?$itemsPerPage:$solrData['responseHeader']['rows'];
    	$this->setItemPerPage($itemPerPage);
    	$totalPage = ceil(intval($solrData['response']['numFound'])/$itemPerPage);
    	$this->setTotalPage($totalPage);
    	
    	$currentPage = 1;
    	$params = $this->getRequest()->getParams();
		if(isset($params['p'])){
			$currentPage = $params['p'];
		}
		$this->setCurrentPage($currentPage);
    	
    }
    protected function setTotalPage($totalPage){
    	$this->totalPage = $totalPage;
    }
	protected function getTotalPage(){
    	return $this->totalPage;
    }
	protected function setItemPerPage($itemPerPage){
    	$this->itemPerPage = $itemPerPage;
    }
	protected function getItemPerPage(){
    	return $this->itemPerPage;
    }
	protected function setCurrentPage($currentPage){
    	$this->currentPage = $currentPage;
    }
	protected function getCurrentPage(){
    	return $this->currentPage;
    }
	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params=array())
    {
        $paramss = $this->getRequest()->getParams();
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = array_merge($paramss,$params);
        return $this->getUrl('*/*/*', $urlParams);
    }
    
    public function getModes(){
    	return array('grid'=>$this->__('Grid'), 'list'=>$this->__('List'));
    }
    
    public function isModeActive($mode){
    	
    	$currentmode = "grid";
		$currentmode = Mage::getSingleton('core/session')->getSolrSearchResultMode();		
    	$params = $this->getRequest()->getParams();
    	if (isset($params['mode'])){
    		$currentmode = $params['mode'];
    	}    	
    	if (isset($currentmode)){
    		return $currentmode == $mode;
    	}else{
    		return false;
    	}    	
    }
    
 	public function getModeUrl($mode)
    {
        $paramss = $this->getRequest()->getParams();
		$paramss['mode'] = $mode;
		
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $paramss;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
    public function getAvailableOrders(){
    	//$sortableFields = Mage::getStoreConfig('webmods_solrsearch_fields/settings/sorts_fields', $this->getStoreId());
		//$sortableFieldsArray = explode(",",$sortableFields);
		
    	$sortableFields = array();
    	
    	
		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
		$catalogProductEntityTypeId = $entityType->getEntityTypeId();
		
		$sortFieldsInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($catalogProductEntityTypeId)
		//->setCodeFilter($sortableFieldsArray)
		->addSetInfo()
		->getData();
		
		foreach ($sortFieldsInfo as $att) {
			if ($att['used_for_sort_by'] > 0) {
				$sortableFields[] = $att;
			}
		}
		
		return $sortableFields;
    }
    
	public function getOrderUrl($order,$direction)
    {
        $paramss = $this->getRequest()->getParams();
		if($order){
		$paramss['orderby'] = $order;
		}
		if($direction){
		$paramss['direction'] = $direction;
		}
		
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $paramss;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
    public function isOrderCurrent($order){
    	$orderby = "";
		$orderby = Mage::getSingleton('core/session')->getSolrSortOrderBy();		
    	$params = $this->getRequest()->getParams();
    	if (isset($params['orderby'])){
    		$orderby = $params['orderby'];
    	}    	
    	if (isset($orderby)){
    		return $orderby == $order;
    	}else{
    		return false;
    	}
    }
	
	public function getCurrentDirection(){
		$currentDirection = "asc";
		$currentDirection = Mage::getSingleton('core/session')->getSolrSortOrderDirection();
		$params = $this->getRequest()->getParams();
    	if (isset($params['direction'])){
    		$currentDirection = $params['direction'];
    	}
		return $currentDirection;
	}
	
	public function getCurrentMode(){
		$mode = "grid";
		$mode = Mage::getSingleton('core/session')->getSolrSearchResultMode();		
		$params = $this->getRequest()->getParams();
    	if (isset($params['mode'])){
    		$mode = $params['mode'];
    	}
		return $mode;
	}



}
