<?php
class Celebros_Conversionpro_Model_Mysql4_Fulltext_Engine extends Mage_CatalogSearch_Model_Resource_Fulltext_Engine
{
	/**
     * Store last search query number of found results
     *
     * @var int
     */
    protected $_lastNumFound = 0;
	
	
	//This is aimed at the advanced search (which currently is not supported).
	public function test()
	{
		return true;
	}
	
	protected function _search($query, $params = array())
	{
		$helper = Mage::helper('conversionpro');

		// Preparing initial values.
		if(is_array($query) && count($query)== 1 && isset($query["*"]) && $query["*"] == '*') $query =""; 
		
		//@todo instead of cleaning these each time, remove the collection model code that creates them.
		unset($params['filters']['solr_params']);
		unset($params['filters']['visibility']);
		
		try {	
			$salespersonApi = Mage::getModel('conversionpro/salespersonSearchApi');
			$previous_search = $helper->getPreviousSearch();

			//Getting new results:
			$rawSearchHandle = $this->_prepareSearchHandle($query, $params);
			
			//Resetting the session storage for the query and parameters.
			$helper->persistPreviousSearch($query, $params['filters']);

			//Getting the custom results from scratch to comply with the differences in the requested parameters.
			$salespersonApi->GetCustomResults($rawSearchHandle,'',$helper->getSearchHandle());

			$result = array();
			
			if(isset($salespersonApi->results)) {
				$searchResults = $salespersonApi->results;
				//Persist search response data
				$helper->registerSearchResults($searchResults);
				$helper->persistSearchHandle($searchResults->GetSearchHandle());
				$helper->persistSearchSessionId($searchResults->SearchInformation->SessionId);

				//If the query is new, remove old price data, and reset Conversion Pro's disabler.
				if (!isset($previous_search['query']) || $query != $previous_search['query']) {
					$helper->setPriceQuestion($helper->getQuestionByAttributeCode('price'));
					$helper->resetConversionproDisabler();
				}
				
				if(!$helper->isSearchResultsWasRegisteredBefore()) {
					//Set search result message for catalog search
					$this->_setRecommendedMessage($searchResults);
					
					//Set merchandising campaigns
					$this->_setAndApplyMerchandisingCampaigns($searchResults);
				}
				
				$data = array();
				$this->_lastNumFound = (int)$searchResults->GetRelevantProductsCount();

				if($searchResults->Products && $searchResults->Products->Items)
				{
					foreach($searchResults->Products->Items as $product_item){
						$data[] = $product_item->Field[Mage::helper('conversionpro/mapping')->getMapping('id')];
					}
				}

				$result = array('ids' => $data);
			}

			return $result;
			
		} catch (Exception $e) {
			Mage::logException($e);
		}
	}
	
	/*
	 * @todo this is a duplicate of getIdsByQuery().
	 */ 
	public function search($query, $params)
	{
		return $this->_search($query, $params);
	}
	
	/**
     * Prepares search handle from query and search params
     *
     * @param string $prefix
     */
    public function _prepareSearchHandle($query, $params)
    {
    	$helper = Mage::helper('conversionpro');
		$searchInformation = Mage::getModel('conversionpro/Api_SearchInformation');
		
		//Spaces in the query string should be represented by a plus sign.
		$searchInformation->Query = str_replace(' ', '+', $query);
		
		//Prepare answers		
		if(isset($params["filters"])){
			$answeredAnswers = Mage::getModel('conversionpro/Api_QwiserAnsweredAnswers');
			$answeredAnswers->Count = 0;
			$answeredAnswers->Items = array();
			
			//@todo This line can be removed, as there's no visibility on the previous_search session storage anyway.
			unset($params['filters']['visibility']);
			
			foreach($params["filters"] as $solrFilterName => $optionIds){

				if(is_string($optionIds)) $optionIds = array($optionIds);
				
				foreach($optionIds as $optionId){
					$answeredAnswer = Mage::getModel('conversionpro/Api_QwiserAnsweredAnswer');
					$answeredAnswer->AnswerId = $optionId;
					$answeredAnswer->EffectOnSearchPath = 0;
					$answeredAnswers->Items[] = $answeredAnswer;
					$answeredAnswers->Count ++;
				}
			}
			$searchInformation->AnsweredAnswers = $answeredAnswers;
		}

		//Prepare sorting options
		if(isset($params["sort_by"])){
			$sortingOptions = Mage::getModel('conversionpro/Api_SortingOptions');
			//Take only the first sorting field (only supported by the engine)
			foreach($params["sort_by"] as $values){
				foreach($values as $key=>$value) {
					$sortingOptions->Ascending = ($value == "desc") ? "false" : "true";
					$sortingOptions->FieldName = $this->getCelebrosSearchEngineFieldName($key);
					$CATALOG_PRODUCT_ATTRIBUTE_ENTITY_TYPE = 10;
					$backendType = Mage::getModel('eav/entity_attribute')->loadByCode($CATALOG_PRODUCT_ATTRIBUTE_ENTITY_TYPE,$key)->getBackendType();
					$strNumericsort = ($backendType == "int" || $backendType == "decimal" || $backendType == "datetime") ?
									"true" : "false";
					
					$sortingOptions->NumericSort = $strNumericsort;
					$sortingOptions->Method = "SpecifiedField";
					break;
				}
				break;
			}
			
			$searchInformation->SortingOptions = $sortingOptions;
		}

		//Prepare paging options
		if(isset($params["offset"]) && isset($params["limit"])){
			$offset = $params["offset"];
			$limit = $params["limit"];
			$searchInformation->CurrentPage = $offset/$limit;
			$searchInformation->PageSize = $limit;
		}
		else {
			$searchInformation->CurrentPage = "0";
			$searchInformation->IsDefaultPageSize = "true";
		}
		
		$searchInformation->PriceFieldName = "Price";
		$searchInformation->IsDefaultSearchProfileName = "true";
		$searchInformation->NumberOfPages = 9999999;
		$searchInformation->MaxMatchClassFound = 0;
		$searchInformation->Stage = 1;
		
		//Profile section
		$searchInformation->IsDefaultSearchProfileName = false;
		$searchInformation->SearchProfileName = $helper->getProfileName();
		
		return $searchInformation->ToSearchHandle();
    }
	
	/**
     * Retrieve search result data collection
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function getResultCollection()
    {
		//Solr's layer model might use catalogsearch's data helper to get the current engine, resulting in it
		// using our engine class instead of mage's. In that case, we should take care to return mage's results
		// collection instead of our own.
		if (Mage::helper('conversionpro')->getIsEngineAvailable()) {
			$collection = Mage::getResourceModel('conversionpro/product_collection')->setEngine($this);
		} else {
			$collection = parent::getResultCollection();
		}
		return $collection;
    }
	
	/**
     * Retrieve search result data collection
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function getAdvancedResultCollection()
    {
        return $this->getResultCollection();
    }
	
	/**
     * Retrieve found document ids from Solr index sorted by relevance
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    public function getIdsByQuery($query, $params = array())
    {
        //$params['fields'] = array('id');

        $result = $this->_search($query, $params);

        if (!isset($result['ids'])) {
            $result['ids'] = array();
        }
		/*
        if (!empty($result['ids'])) {
            foreach ($result['ids'] as &$id) {
                $id = $id['id'];
            }
        }
		*/
        return $result;
    }
	
	/**
     * Retrieve attribute field name
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Attribute|string $attribute
     * @param   string $target - default|sort|nav
     *
     * @return  string|bool
     */
    public function getSearchEngineFieldName($attribute, $target = 'default')
    {
        $fieldName = $attribute->getAttributeCode();
		if ($fieldName == 'price') {
			return $this->getPriceFieldName();
		}
		return $fieldName;
    }
	
	/**
     * Prepare price field name for search engine
     *
     * @param   null|int $customerGroupId
     * @param   null|int $websiteId
     *
     * @return  bool|string
     */
    public function getPriceFieldName($customerGroupId = null, $websiteId = null)
    {
		$helper = Mage::helper('conversionpro');
		if ($helper->hasSearchResults()) {
			$searchResults = $helper->getSearchResults();
			return $searchResults->SearchInformation->PriceFieldName;
		}
		return 'Price';
    }
		
	/**
     * Define if current search engine supports advanced index
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return true;
    }
	
	public function getCelebrosSearchEngineFieldName($attribute)
    {
    	$fieldName = "";
		
    	if($attribute == "sku")
    		$fieldName = "product_sku";
    	else if($attribute == "name")
    		$fieldName = "Title";
		else if ($attribute == 'relevance' || $attribute == 'position')
			$fieldName = 'Relevancy';
    	else if (is_string($attribute))
    		$fieldName = ucfirst($attribute);
		else
    		$fieldName = ucfirst($attribute->getAttributeCode());
    
    	return $fieldName;
    }
	
	public function getCelebrosSearchEngineQuestionFieldName($attribute)
    {
    	$fieldName = "";
    	if($attribute == "price") {
    		$fieldName = $this->getPriceFieldName();
		} else {
			$fieldName = $this->getCelebrosSearchEngineFieldName($attribute);
		}
		return $fieldName;
    }
	
	/**
     * Set search messages for catalog search result page
     *
     * @param string $searchResults
     */    
    protected function _setRecommendedMessage($searchResults)
    {
		if(isset($searchResults) && $searchResults->GetRecommendedMessage() != ''){
			$message = $searchResults->GetRecommendedMessage();
			$message = preg_replace('/#%/', '', $message);
			$message = preg_replace('/%#/', '', $message);
			Mage::helper('catalogsearch')->addNoteMessage($message);
    	}
    }
	
	protected function _setAndApplyMerchandisingCampaigns($searchResults) 
    {
		if(isset($searchResults) && $searchResults->QueryConcepts->Count > 0 && Mage::helper('conversionpro')->isCampaignsEnabled()) {
    		foreach ($queryConcepts = $searchResults->QueryConcepts->Items as $queryConcept){
    			foreach ($queryConcept->DynamicProperties as $name => $value){
    			 		switch($name){
			    			case "alternative products":
								$message = str_replace('{{query}}', $searchResults->SearchInformation->Query , Mage::getStoreConfig('conversionpro/display_settings/alt_message'));
								$message = str_replace('{{new_query}}', $value, $message);
								Mage::helper('catalogsearch')->addNoteMessage($message);
			    				break;
			    			case "banner image":
			    				Mage::helper('conversionpro')->registerImageBanner($value);
			    				break;
			    			case "banner flash":
			    				Mage::helper('conversionpro')->registerFlashBanner($value);
			    				break;
			    			case "custom message":
			    				$message = $value;
			    				Mage::helper('catalogsearch')->addNoteMessage($message);
			    				break;
			    			case "redirection url":
			    				Mage::app()->getResponse()->setRedirect($value);
			    				break;
		    			}
    			}
    		}
    	}
    }
	
	/**
     * Retrieve last query number of found results
     *
     * @return int
     */
    public function getLastNumFound()
    {
        return $this->_lastNumFound;
    }
	
	/**
     * Get stat info using engine search stats component
     *
     * @param  string $query
     * @param  array  $params
     * @param  string $entityType 'product'|'cms'
     * @return array
     */
    public function getStats($query, $params = array(), $entityType = 'product')
    {
        return $this->_search($query, $params);
    }
}