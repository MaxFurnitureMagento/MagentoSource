<?php
class Celebros_Conversionpro_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{	
	//Extending the function to run getItemsData() first, which runs _search(), which means it'll have the correct count.
	public function getItemsCount()
    {
		//Refresh # of items to get an acurate # of results.
		$this->_initItems();
		
		//If this question was already answered, don't display it again.
		$previous_search = Mage::helper('conversionpro')->getPreviousSearch();
		
		/**
		 *Cancelling this to consider sub-categories.
		if (array_key_exists('category', $previous_search['filters'])) {
			return 0;
		}
		*/
		
		//If the question wasn't answered yet, get the # of results.
		return count($this->getItems());
    }
	
	/**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
		$layer = Mage::helper('conversionpro')->getCurrentLayer();
		$key    = $layer->getStateKey() . '_SUBCATEGORIES';
        $data   = $layer->getCacheData($key);

        if ($data === null || $data = '') {
        	$question = $layer->getProductCollection()->getFacetedData('Category');

        	if(!isset($question)) return array();
			
        	$data = array();
	    	if(isset($question->Answers) && $question->Answers->Count > 0) {
		    	foreach ($question->Answers->Items as $answer) {
					$data[] = array(
						'label' => $answer->Text,
						'value' => $answer->Id,//$this->getCategoryIdByAnswerText($answer->Text),
						'count' => $answer->ProductCount,
					);
					//We're keeping a mapping of category ids and answer ids. If it can't find a category id, 
					// then getCategoryIdByAnswerText() will return the answer's text instead. We'll use that later
					// on when applying the filter, in cases where conversion pro sends a category that doesn't exist
					// in Magento.
					Mage::helper('conversionpro')->addCategoryMapping($this->getCategoryIdByAnswerText($answer->Text), $answer->Id);
				}
	    	}
	    	
	    	if(isset($question->ExtraAnswers) && $question->ExtraAnswers->Count > 0) {
	    		foreach ($question->ExtraAnswers->Items as $answer) {
					$data[] = array(
						'label' => $answer->Text,
						'value' => $answer->Id,//$this->getCategoryIdByAnswerText($answer->Text),
						'count' => $answer->ProductCount,
					);
					Mage::helper('conversionpro')->addCategoryMapping($this->getCategoryIdByAnswerText($answer->Text), $answer->Id);
	    		}
	    	}  

            $tags = $layer->getStateTags();
            $layer->getAggregator()->saveCacheData($data, $key, $tags);
        }

		if (!is_array($data)) {
			Mage::log('error from category');
		}
        return $data;
    }

    protected function getCategoryIdByAnswerText($answer_text) {
    	$category = Mage::getModel('catalog/category')->loadByAttribute('name', $answer_text);
		if ($category) {
			return $category->getId();
		}
		return $answer_text;
    }

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		$layer = Mage::helper('conversionpro')->getCurrentLayer();
		$filter = (int) $request->getParam($this->getRequestVar());
		
        if (!$filter) {
            return $this;
        }
		$this->_categoryId = Mage::helper('conversionpro')->getCategoryIdByAnswerId($filter);

        Mage::register('current_category_filter', $this->getCategory(), true);

        $this->_appliedCategory = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($this->_categoryId);

		// For some reason, this class runs twice, and not as a singelton, so we have no way of monitoring
		// whether the state filter was already added or not by using some class parameter. Instead, 
		// we're going over all the filters to see if any has 'Category' for the name, and only adding the state 
		// tag if we can't find one.
		$isFilterApplied = false;
		
		$filters = $this->getLayer()->getState()->getFilters();
		foreach ($filters as $filter) {
			if ($filter->getName() == $this->getName()) {
				$isFilterApplied = true;
			}
		}
		
		//If there's no state tag for the cateogry yet, create it.
		if (!$isFilterApplied) {
			//If this category exists in Magento, it will execute normally. In any other case, we assume that the chosen
			// answer doesn't exist in Magento and apply it as a regular filter. The getCategoryIdByAnswerText() function
			// saves the answer's text to $this->_categoryId in these cases, so we can use that as the state tag's 
			// label.
			if ($this->_isValidCategory($this->_appliedCategory)) {
				$this->getLayer()->getState()->addFilter(
					$this->_createItem($this->_appliedCategory->getName(), $this->_categoryId)
				);
			} else {
				$this->getLayer()->getState()->addFilter(
					$this->_createItem($this->_categoryId, $filter)
				);
			}
		}
        return $this;
    }

    /**
     * Apply category filter to product collection
     *
     * @deprecated after 1.10.0.2
     *
     * @param   Mage_Catalog_Model_Category $category
     * @param   Mage_Catalog_Model_Layer_Filter_Category $filter
     * @return  Celebros_Conversionpro_Model_Catalog_Layer_Filter_Category
     */
    public function addCategoryFilter($category, $filter)
    {
        Mage::helper('conversionpro')->getCurrentLayer()->getProductCollection()->addCategoryFilter($category);
        return $this;
    }
	
	/**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue()
    {
        /*
		if ($this->_appliedCategory) {
            /**
             * Revert path ids
             * 
            $pathIds = array_reverse($this->_appliedCategory->getPathIds());
            $curCategoryId = $this->getLayer()->getCurrentCategory()->getId();
            if (isset($pathIds[1]) && $pathIds[1] != $curCategoryId) {
            	return $pathIds[1];
            }
        }
		*/
        return null;
    }
}
