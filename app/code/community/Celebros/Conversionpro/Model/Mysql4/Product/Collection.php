<?php
class Celebros_Conversionpro_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    /**
     * Store search query text
     *
     * @var string
     */
    protected $_searchQueryText = '';

    /**
     * Store search query params
     *
     * @var array
     */
    protected $_searchQueryParams = array();

    /**
     * Store search query filters
     *
     * @var array
     */
    protected $_searchQueryFilters = array();
    
    /**
     * Store found entities ids
     *
     * @var array
     */
    protected $_searchedEntityIds = array();

    /**
     * Store found suggestions
     *
     * @var array
     */
    protected $_searchedSuggestions = array();

    /**
     * Store engine instance
     *
     * @var Celebros_Conversionpro_Model_Resource_Engine
     */
    protected $_engine = null;

    /**
     * Store sort orders
     *
     * @var array
     */
    protected $_sortBy = array();

    /**
     * General default query *:* to disable query limitation
     *
     * @var array
     */
    protected $_generalDefaultQuery = array('*' => '*');

    /**
     * Faceted search result data
     *
     * @var array
     */
    protected $_facetedData = array();

    /**
     * Suggestions search result data
     *
     * @var array
     */
    protected $_suggestionsData = array();

    /**
     * Stores original page size, because _pageSize will be unset at _beforeLoad()
     * to disable limitation for collection at load with parent method
     *
     * @var int|bool
     */
    protected $_storedPageSize = false;
	
    /**
     * Get a question by field name. 
	 * Will return an empty array if no search results data exists yet.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFacetedData($field)
    {
		$helper = Mage::helper('conversionpro');
		
		$this->_facetedData = $helper->getQuestions();

        if (isset($this->_facetedData[$field])) {
            return $this->_facetedData[$field];
        }

        return array();
    }

    /**
     * Return suggestions search result data
     *
     *  @return array
     */
    public function getSuggestionsData()
    {
        return $this->_suggestionsData;
    }

    /**
     * Add search query filter
     * Set search query
     *
     * @param   string $queryText
     *
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addSearchFilter($queryText)
    {
        /**
         * @var Mage_CatalogSearch_Model_Query $query
         */
        $query = Mage::helper('catalogsearch')->getQuery();
        $this->_searchQueryText = $queryText;
        $synonymFor = $query->getSynonymFor();
        if (!empty($synonymFor)) {
            $this->_searchQueryText .= ' ' . $synonymFor;
        }

        return $this;
    }

    /**
     * Add search query filter
     * Set search query parameters
     *
     * @param   string|array $param
     * @param   string|array $value
     *
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addSearchParam($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value) {
                $this->addSearchParam($field, $value);
            }
		} elseif (!isset($param)) {
			//This option is meant for nav2search's use of category names as a search string.
			// I'm using addSearchParam() to modify the search query string to the category name.
			$this->_searchQueryParams = $value;
        } elseif (!empty($value)) {
            $this->_searchQueryParams[$param] = $value;
        }
        return $this;
    }

    /**
     * Get extended search parameters
     *
     * @return array
     */
    public function getExtendedSearchParams()
    {
        $result = $this->_searchQueryFilters;
        $result['query_text'] = $this->_searchQueryText;

        return $result;
    }

    /**
     * Add search query filter (fq)
     *
     * @param   array $param
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addFqFilter($param)
    {
		$helper = Mage::helper('conversionpro');
		if (is_array($param)) {
            foreach ($param as $field => $value) {
				$question = $helper->getQuestionByAttributeCode($field);
				//If we know the question to be hierarchical or multiselect is disabled, then rewrite old values.
				// Most of the time, we wouldn't have the questions available at this point, but that's no reason to override existing
				// values, which is why we wouldn't run this piece if $question isn't set.
				if (!$helper->isMultiselectEnabled()
					|| ($question && $question->DynamicProperties['IsHierarchical'] == 'True')) {
						$this->_searchQueryFilters[$field] = array($value);
				} else {
					if (!isset($this->_searchQueryFilters[$field])) {
						$this->_searchQueryFilters[$field] = array();
					}
					if (!in_array($value, $this->_searchQueryFilters[$field])) {
						$this->_searchQueryFilters[$field][] = $value;
					}
				}
            }
        }
        return $this;
    }

    /**
     * Add advanced search query filter
     * Set search query
     *
     * @param  string $query
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addAdvancedSearchFilter($query)
    {
        return $this->addSearchFilter($query);
    }

    /**
     * Specify category filter for product collection
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
		//@todo get the engine from this class's getEngine().
		$engine = Mage::getResourceSingleton('conversionpro/fulltext_engine');
		$helper = Mage::helper('conversionpro');

		$answer_id = $helper->getAnswerIdByCategoryId($category->getId());
		if (isset($answer_id)) {
			$this->addFqFilter(array('category' => $answer_id));
		}
		
        parent::addCategoryFilter($category);
        return $this;
    }

    /**
     * Add sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        $this->_sortBy[] = array($attribute => $dir);
        return $this;
    }

    /**
     * Prepare base parameters for search adapters
     *
     * @return array
     */
    protected function _prepareBaseParams()
    {
        $store  = Mage::app()->getStore();
        $params = array(
            'store_id'      		=> $store->getId(),
            'locale_code'   		=> $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE),
            'filters'       		=> $this->_searchQueryFilters
        );
        if (!empty($this->_searchQueryParams)) {
            $params['ignore_handler'] = true;
            $query = $this->_searchQueryParams;
        } else {
            $query = $this->_searchQueryText;
        }
        return array($query, $params);
    }

    /**
     * Search documents by query
     * Set found ids and number of found results
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    protected function _beforeLoad()
    {
        $ids = array();
        if ($this->_engine) {
            list($query, $params) = $this->_prepareBaseParams();

            if ($this->_sortBy) {
                $params['sort_by'] = $this->_sortBy;
            }
            if ($this->_pageSize !== false) {
                $page              = ($this->_curPage  > 0) ? (int) $this->_curPage  : 1;
                $rowCount          = ($this->_pageSize > 0) ? (int) $this->_pageSize : 1;
                $params['offset']  = $rowCount * ($page - 1);
                $params['limit']   = $rowCount;
            }

            $result = $this->_engine->getIdsByQuery($query, $params);
            $ids    = (array) $result['ids'];

            $this->_facetedData = array();
        }

        $this->_searchedEntityIds = &$ids;
        $this->getSelect()->where('e.entity_id IN (?)', $this->_searchedEntityIds);

		$this->_totalRecords = $this->_engine->getLastNumFound();
		
		//In case Quiser tells us to, we should cancel conversionpro and re-run the search.
		//Right now this feature isn't in use, but we're leaving it here just in case.
		if (false) {
			$status = Mage::getSingleton('conversionpro/session')->getConversionproDisabler();
			//This prevents the redirect from looping forever. We'll only redirect in case we haven't already enabled the disabler.
			if (!isset($status) || !$status) {
				Mage::helper('conversionpro')->enableConversionproDisabler();
				Mage::app()->getResponse()->setRedirect(Mage::helper('core/url')->getCurrentUrl());
			}
		}

		//Handle the case of a single search result (only when not in an ajax request).
		if ($this->_totalRecords == 1 
			&& Mage::getStoreConfig('conversionpro/display_settings/go_to_product_on_one_result')
			&& !Mage::app()->getRequest()->isXmlHttpRequest()) {
			
			$searchResults = Mage::helper('conversionpro')->getSearchResults();

			Mage::app()->getFrontController()->getResponse()->setRedirect(
				Mage::getModel('catalog/product')->load($ids[0])->getProductUrl()
			);
		}
		
        /**
         * To prevent limitations to the collection, because of new data logic.
         * On load collection will be limited by _pageSize and appropriate offset,
         * but third party search engine retrieves already limited ids set
         */
        $this->_storedPageSize = $this->_pageSize;
        $this->_pageSize = false;

        return parent::_beforeLoad();
    }

    /**
     * Sort collection items by sort order of found ids
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $sortedItems = array();
        foreach ($this->_searchedEntityIds as $id) {
            if (isset($this->_items[$id])) {
                $sortedItems[$id] = $this->_items[$id];
            }
        }
        $this->_items = &$sortedItems;

        /**
         * Revert page size for proper paginator ranges
         */
        $this->_pageSize = $this->_storedPageSize;

        return $this;
    }

    /**
     * Retrieve found number of items
     *
     * @return int
     */
    public function getSize()
    {
        if (!Mage::helper('conversionpro')->hasSearchResults()) {
            list($query, $params) = $this->_prepareBaseParams();
            $params['limit'] = 1;

            //$this->_engine->getIdsByQuery($query, $params);
        }
		
		$this->_totalRecords = $this->_engine->getLastNumFound();
        if ($this->_totalRecords) {
			return $this->_totalRecords;
		}
		return 1; //$this->_totalRecords;
    }

    /**
     * Collect stats per field
     *
     * @param  array $fields
     * @return array
     */
    public function getStats($fields)
    {
        list($query, $params) = $this->_prepareBaseParams();
        $params['limit'] = 0;
        $params['solr_params']['stats'] = 'true';

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $field) {
            $params['solr_params']['stats.field'][] = $field;
        }

        //return $this->_engine->getStats($query, $params);
    }

    /**
     * Set query *:* to disable query limitation
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function setGeneralDefaultQuery()
    {
        $this->_searchQueryParams = $this->_generalDefaultQuery;
        return $this;
    }

    /**
     * Set search engine
     *
     * @param object $engine
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function setEngine($engine)
    {
        $this->_engine = $engine;
        return $this;
    }

    /**
     * Stub method
     *
     * @param array $fields
     *
     * @return Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addFieldsToFilter($fields)
    {
        return $this;
    }

    /**
     * Adding product count to categories collection
     *
     * @param   Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addCountToCategories($categoryCollection)
    {
        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param   array $visibility
     * @return  Mage_Catalog_Model_Resource_Product_Collection
     */
    public function setVisibility($visibility)
    {
        if (is_array($visibility)) {
            $this->addFqFilter(array('visibility' => $visibility));
        }

        return $this;
    }

    /**
     * Retrieve faceted search results
     *
     * @deprecated after 1.9.0.0 - integrated into $this->getSize()
     *
     * @param  array $params
     * @return array
     */
    public function getFacets($params)
    {
        list($query, $params) = $this->_prepareBaseParams();
        $params['limit'] = 1;

        return (array) $this->_engine->search($query, $params);
    }

    /**
     * Add search query filter (qf)
     *
     * @deprecated after 1.9.0.0
     * @see $this->addFqFilter()
     *
     * @param   string|array $param
     * @param   string|array $value
     * @return  Celebros_Conversionpro_Model_Resource_Collection
     */
    public function addSearchQfFilter($param, $value = null)
    {
        if (is_array($param)) {
            foreach ($param as $field => $value) {
                $this->addSearchQfFilter($field, $value);
            }
        } elseif (isset($value)) {
            if (isset($this->_searchQueryFilters[$param]) && !is_array($this->_searchQueryFilters[$param])) {
                $this->_searchQueryFilters[$param] = array($this->_searchQueryFilters[$param]);
                $this->_searchQueryFilters[$param][] = $value;
            } else {
                $this->_searchQueryFilters[$param][] = $value;
            }
        }

        return $this;
    }

    /**
     * Get prices from search results
     *
     * @param   null|float $lowerPrice
     * @param   null|float $upperPrice
     * @param   null|int   $limit
     * @param   null|int   $offset
     * @param   boolean    $getCount
     * @param   string     $sort
     * @return  array
     */
    public function getPriceData($lowerPrice = null, $upperPrice = null,
        $limit = null, $offset = null, $getCount = false, $sort = 'asc')
    {
        list($query, $params) = $this->_prepareBaseParams();
        $priceField = $this->_engine->getSearchEngineFieldName('price');
        $conditions = null;
        if (!is_null($lowerPrice) || !is_null($upperPrice)) {
            $conditions = array();
            $conditions['from'] = is_null($lowerPrice) ? 0 : $lowerPrice;
            $conditions['to'] = is_null($upperPrice) ? '' : $upperPrice;
        }
        if (!$getCount) {
            $params['fields'] = $priceField;
            $params['sort_by'] = array(array('price' => $sort));
            if (!is_null($limit)) {
                $params['limit'] = $limit;
            }
            if (!is_null($offset)) {
                $params['offset'] = $offset;
            }
            if (!is_null($conditions)) {
                $params['filters'][$priceField] = $conditions;
            }
        } else {
            $params['solr_params']['facet'] = 'on';
            if (is_null($conditions)) {
                $conditions = array('from' => 0, 'to' => '');
            }
            $params['facet'][$priceField] = array($conditions);
        }

        $data = $this->_engine->getResultForRequest($query, $params);
        if ($getCount) {
            return array_shift($data['faceted_data'][$priceField]);
        }
        $result = array();
        foreach ($data['ids'] as $value) {
            $result[] = (float)$value[$priceField];
        }

        return ($sort == 'asc') ? $result : array_reverse($result);
    }
}