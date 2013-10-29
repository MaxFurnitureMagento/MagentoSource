<?php
class Celebros_Conversionpro_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
	//Extending the function to run getItemsData() first, which runs _search(), which means it'll have the correct count.
    public function getItemsCount()
    {
		$helper = Mage::helper('conversionpro');
		
		//Refresh # of items to get an acurate # of results.
		$this->_initItems();
		
		//If this question is hierarchical or multiselect is disabled, and it was already answered, don't display it again.
		$previous_search = $helper->getPreviousSearch();
		if (array_key_exists($this->_requestVar, $previous_search['filters'])) {
			if (!$helper->isMultiselectEnabled() || $helper->isHierarchical($this->_requestVar)) {
				return 0;
			}
		}
		
		//If none of the above is true, use the results' count of this question's answers.
		return count($this->getItems());
		
		//This is the previous action that we're overriding.
		//return parent::getItemsCount();
    }
	
	/**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
    	$helper = Mage::helper('conversionpro');
    	$data = array();
    	$this->_requestVar = $this->getAttributeModel()->getAttributeCode();
		
    	//Get Celebros search results question name
    	$key = Mage::getResourceSingleton('conversionpro/fulltext_engine')
			->getCelebrosSearchEngineQuestionFieldName($this->_requestVar);

		//We're getting the question item with getFacetedData(), 
		// so that it'll call _search() if there aren't any results stored in the registry.
		$question = $helper->getCurrentLayer()->getProductCollection()->getFacetedData($key);
		
    	if(count($question) == 0) return array();
    	
    	if($question->Answers->Count > 0) {
    		foreach ($question->Answers->Items as $answer) {
    			$data[] = array(
    					'label' => $answer->Text,
    					'value' => $answer->Id,
    					'count' => $answer->ProductCount,
    			);
    		}
    	}
    	
    	if($question->ExtraAnswers->Count > 0) {
    		foreach ($question->ExtraAnswers->Items as $answer) {
    			$data[] = array(
    					'label' => $answer->Text,
    					'value' => $answer->Id,
    					'count' => $answer->ProductCount,
    			);
    		}
    	}
		if (!is_array($data)) {
			Mage::log('error from attribute');
		}
		
    	return $data;
    }
    
    /**
     * Apply attribute filter to layer
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param object $filterBlock
     * @return Celebros_Conversionpro_Model_Catalog_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		$filter = $request->getParam($this->_requestVar);
		$helper = Mage::helper('conversionpro');
		
        if (is_array($filter)) {
            return $this;
        }

        if ($filter && $this->_requestVar != 'price') {
			
			$filter = explode(',', $filter);
			
			$this->applyFilterToCollection($this, $filter);

			$this->_items = array();
			
			//We used to add a filter to the state model here, but we're doing that
			// in the layer's getFilter now.
        }

        return $this;
    }

    /**
     * Apply attribute filter to solr query
     *
     * @param   Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param   int $value
     *
     * @return  Celebros_Conversionpro_Model_Catalog_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
		if (empty($value) || (isset($value['from']) && empty($value['from']) && isset($value['to'])
            && empty($value['to']))
        ) {
            $value = array();
        }

        if (!is_array($value)) {
            $value = array($value);
        }
		
		$collection = Mage::helper('conversionpro')->getCurrentLayer()->getProductCollection();
		$engine = Mage::getResourceSingleton('conversionpro/fulltext_engine');
        $fieldName = $engine->getSearchEngineFieldName($filter->getAttributeModel(), 'nav');

		foreach ($value as $answerId) {
			$collection->addFqFilter(array($fieldName => $answerId));
		}

        return $this;
    }
	
	/**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0)
    {
        return Mage::getModel('conversionpro/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }
}
