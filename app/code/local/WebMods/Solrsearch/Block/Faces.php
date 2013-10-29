<?php
class WebMods_Solrsearch_Block_Faces extends Mage_Core_Block_Template
{
	protected $solrData = array();
	
	protected $filterQuery = array();
	
	protected $solrModel = null;
	
	protected function _construct()
    {
    	$this->solrModel = Mage::getModel('solrsearch/solr');
    	
    	$is_ajax = Mage::helper('solrsearch')->getSettings('use_ajax_result_page');
    	if (intval($is_ajax) > 0) {
    		$this->setTemplate('solrsearch/searchfaces.phtml');
    	}else{
    		$this->setTemplate('solrsearch/standard/searchfaces.phtml');
    	}
    }
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
        
    public function getSolrData(){
    	return $this->getData('solrdata');
    }
	/**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
		return parent::_beforeToHtml();
    }
    
    public function getFacetLabel($facetCode){
    	
    	$startPoint = strrpos($facetCode, '_')+1;
    	$endPoint = strlen($facetCode);
    	$attributeCode = substr($facetCode, 0, ($startPoint-1));
    	
    	$facetLabelCache = Mage::app()->loadCache('solr_bridge_'.$facetCode.'_cache');
    	
    	if ( isset($facetLabelCache) && !empty($facetLabelCache) ) {
    		return $facetLabelCache;
    	}else {
    		$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
			$catalogProductEntityTypeId = $entityType->getEntityTypeId();
			
			$facetFieldsInfo = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($catalogProductEntityTypeId)
			->setCodeFilter(array($attributeCode))
			->addSetInfo()
			->getData();
			
			$facetLabel = '';
			foreach($facetFieldsInfo as $att){
				if ($att['attribute_code'] == $attributeCode) {
					$facetLabel = $att['frontend_label'];
					Mage::app()->saveCache($facetLabel, 'solr_bridge_'.$facetCode.'_cache', array(), 60*60*24*360);
					break;
				}
			}
			if ($attributeCode == 'category') {
				$facetLabel = $this->__('Category');
			}
			return $facetLabel;
    	}
    }
    
    public function getFacetFields()
    {
    	$solrData = $this->getSolrData();
    	
    	$facets_fields = array();
    	
    	if (isset($solrData['facet_counts']['facet_fields']) && is_array($solrData['facet_counts']['facet_fields'])) {
    		$facets_fields = $solrData['facet_counts']['facet_fields'];
    	}
    	
    	//Ignore the price_decimal
    	if (isset($facets_fields['price_decimal'])) {
    		unset($facets_fields['price_decimal']);
    	}
    	
    	$this->manupulateFacetFields($facets_fields);
    	
    	return $facets_fields;
    }
    
    public function isSelectedFacetActive()
    {
    	$filterQuery = $this->solrModel->getStandardFilterQuery();
    	
    	$this->filterQuery = $filterQuery;
    	
    	$isFacetActived = false;
    	foreach($filterQuery as $key=>$value) {
    		if(is_array($value) && count($value) > 0) {
    			$isFacetActived = true;
    		}
    	}
    	
    	return $isFacetActived;
    }
    
    protected function getFilterQuery()
    {
    	if (!$this->filterQuery) {
    		$this->filterQuery = $this->solrModel->getStandardFilterQuery();
    	}
    	return $this->filterQuery;
    }
    
    protected function manupulateFacetFields(&$facetData)
    {
    	if (Mage::helper('solrsearch')->getSettings('allow_multiple_filter') > 0)
    	{
    		$queryText = $this->solrModel->getParams('q');
    		$key = sha1('solrbridge_solrsearch_'.$queryText);
    		
    		$originalSolrData = Mage::getSingleton('core/session')->getOriginSolrFacetData();
    		 
    		if (isset($originalSolrData) && isset($originalSolrData[$key])) {
    			
    			$filterQuery = $this->getFilterQuery();
    			
    			$filterQueryKeys = array_keys($filterQuery);

    			foreach ($filterQueryKeys as $facetkey) {
    				if (isset($originalSolrData[$key]['facet_counts']['facet_fields'][$facetkey])) {
    					$facetData[$facetkey] = $originalSolrData[$key]['facet_counts']['facet_fields'][$facetkey];
    				}
    				
    				$display_category_as_hierachy = Mage::helper('solrsearch')->getSettings('display_category_as_hierachy');
    				if ($display_category_as_hierachy > 0 && $facetkey == 'category_facet') {
	    				if (isset($originalSolrData[$key]['facet_counts']['facet_fields']['category_path'])) {
	    					$facetData['category_path'] = $originalSolrData[$key]['facet_counts']['facet_fields']['category_path'];
	    				}
    				}
    				
    			}
    			
    		}
    		//Update original facet data
    		$originalSolrData[$key]['facet_counts']['facet_fields'] = $facetData;
    		Mage::getSingleton('core/session')->setOriginSolrFacetData($originalSolrData);
    	}
    }
    
	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getFacesUrl($params=array())
    {
    	$_solrDataArray = $this->getSolrData();

    	$paramss = $this->getRequest()->getParams();
    	
    	if( isset($_solrDataArray['responseHeader']['params']['q']) && !empty($_solrDataArray['responseHeader']['params']['q']) ) {
        	if ($paramss['q'] != $_solrDataArray['responseHeader']['params']['q']) {
        		$paramss['q'] = $_solrDataArray['responseHeader']['params']['q'];
        	}
        }
        
        foreach ($params as $key=>$item) {
        	if ($key == 'fq') {
        		
        		foreach ($item as $k=>$v) {
        			if (isset($paramss[$key][$k]) && $v == $paramss[$key][$k]){
        				
        			}else{
        				$finalParams = array_merge_recursive($params, $paramss);
        			}
        		}
        	}
        }
        
        if (isset($finalParams['p'])) {
        	$finalParams['p'] = 1;
        }
		
    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        if (isset($finalParams)) {
        	$urlParams['_query']    = $finalParams;
        }
        
        return $this->getUrl('*/*/*', $urlParams);
    }
    
	/**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getRemoveFacesUrl($key,$value)
    {
        $paramss = $this->getRequest()->getParams();
        
        $finalParams = $paramss;
        
        if (!is_array($finalParams['fq'][$key]) && !empty($finalParams['fq'][$key])) {
        	unset($finalParams['fq'][$key]);
        }else if (is_array($finalParams['fq'][$key]) && count($finalParams['fq'][$key]) > 0) {
        	foreach ($finalParams['fq'][$key] as $k=>$v) {
        		if ($v == $value) {
        			unset($finalParams['fq'][$key][$k]);
        		}
        	}
        }

    	$urlParams = array();
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $finalParams;

        return Mage::getUrl('*/*/*', $urlParams);
    }
    
    public function parseCategoryPathFacet($categoryPathFaces)
    {
    	
    	$categoryArray = $this->parseCategoryPathToArray($categoryPathFaces);
    	
    	return $this->renderCategoryHierachy($categoryArray);
    	
    }
    
	public function parseCategoryPathToArray($categoryPathFaces){
		$returnData = array();
		
		if (is_array($categoryPathFaces)) {
			foreach ($categoryPathFaces as $categoryPath=>$count) {
				
				$categoryPathArray = $this->pathToArray($categoryPath);
				
				$index = 0;
				
				$parents = array();
				
				foreach ($categoryPathArray as $key=>$item)
				{
					$categoryName = $item['name'];
					$categoryId = $item['id'];
					
					$categoryItem = array('id' => $categoryId, 'name' => $categoryName, 'count' => 0, 'parent_id' => 0);
					
					if ($index == (count($categoryPathArray) - 1)) {
						$categoryItem['count'] = $count;
					}
					
					if ($key > 0) {
						$categoryItem['parent_id'] = $categoryPathArray[($key - 1)]['id'];
					}
					
					$parents[] = $categoryId;
					
					if (array_key_exists($categoryId, $returnData)) {
						$returnData[$categoryId]['count'] = ($returnData[$categoryId]['count'] + $categoryItem['count']);
					}
					else
					{
						$returnData[$categoryId] = $categoryItem;
					}
					
					$index++;
				}
			}
		}
	    return $returnData;
    }
    /**
     * Convert string path to array
     * @param string $path
     * @return array
     */
    public function pathToArray($path) {
    	$chunks = explode('/', $path);
    	$result = array();
    	for ($i = 0; $i < sizeof($chunks) - 1; $i+=2)
    	{
    		$result[] = array('id' => $chunks[($i+1)], 'name' => $chunks[$i]);
    	}
    	
    	return $result;
    }
    
    //output a multi-dimensional array as a nested UL
    
	protected function renderCategoryHierachy($categoryArray){
		$menuData = array(
				'items' => array(),
				'parents' => array()
		);
		
		foreach($categoryArray as $menuItem){
				$menuData['items'][$menuItem['id']] = $menuItem;
				$menuData['parents'][$menuItem['parent_id']][] = $menuItem['id'];
		}
		
		return $this->buildMenu(0, $menuData);
	}
	/**
	 * Build category hierachy html
	 * @param int $parentId
	 * @param array $menuData
	 * @return html
	 */
	protected function buildMenu($parentId, $menuData)
	{
		$html = '';
	
		if (isset($menuData['parents'][$parentId]))
		{
			if(!$parentId){
				$html = '<ol class="sf-menu sf-vertical">';
			}else{
				$html = '<ol>';
			}
			$index = 0;
			foreach ($menuData['parents'][$parentId] as $itemId)
			{
				$count = $menuData['items'][$itemId]['count'];
				
				$categoryName = '';
				if (isset($menuData['items'][$itemId]['name'])) {
					$categoryName = $menuData['items'][$itemId]['name'];
				}
				
				$facetUrl = $this->getFacesUrl(array('fq'=>array('category' => $categoryName)));
				
				$classNames = 'facet-item';
				
				if (isset($this->filterQuery['category_facet']) && in_array($categoryName, $this->filterQuery['category_facet'])){
					$classNames .= ' active';
					$facetUrl = $this->getRemoveFacesUrl('category', $categoryName);
				}
				
				if ($count < 1) {
					$facetUrl = 'javascript:;';
					$classNames .= ' empty';
				}
				
				if(!$index){
					$html .= '<li class="first">' . (($categoryName)?'<a href="'.$facetUrl.'" class="'.$classNames.'">'.$this->facetFormat(trim($categoryName)).'('.$count.')'.'</a>':"");
				}else{
					$html .= '<li>' . (($categoryName)?'<a href="'.$facetUrl.'" class="'.$classNames.'">'.$this->facetFormat(trim($categoryName)).'('.$count.')'.'</a>':"");
				}
				// find childitems recursively
				$html .= $this->buildMenu($itemId, $menuData);
	
				$html .= '</li>';
				$index++;
			}
			$html .= '</ol>';
		}
	
		return $html;
	}
	
	public function facetFormat($text) {
		$returnText = $text;
		if (strrpos($text, '_._._') > -1) {
			$returnText = str_replace('_._._', '/', $text);
		}
		return $returnText;
	}
	
	protected function getHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getFacesUrl(array('fq'=>array('category'=>str_replace('_._._', '/', $key))));
		}else{
			return 'javascript:;';
		}
	}
	
	protected function getRemoveHrefFacet($key, $path, $facetCountArray){
		$count = $facetCountArray[trim($path, '/')];
		if ($count > 0) {
			return $this->getRemoveFacesUrl('category', str_replace('_._._', '/', $key));
		}else{
			return 'javascript:;';
		}
	}
	
	public function getPriceFacets()
	{
		return $this->getChildHtml('solr_price_facets');
	}
	
	public function isLayerNavigationActive()
	{
		$returnData = $this->getSolrData();
		
		if (isset($returnData['response']['numFound']) && intval($returnData['response']['numFound']) > 0){
			return true;		
		}
		
		return false;
	}
}