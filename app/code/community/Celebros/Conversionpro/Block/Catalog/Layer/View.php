<?php
class Celebros_Conversionpro_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
	
	protected $_loadedFilters = array();
	
	protected $_stateTags = array();
	
	/**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if (Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
            $this->_categoryBlockName        = 'conversionpro/catalogsearch_layer_filter_category';
            $this->_attributeFilterBlockName = 'conversionpro/catalogsearch_layer_filter_attribute';
            $this->_priceFilterBlockName     = 'conversionpro/catalogsearch_layer_filter_price';
            $this->_decimalFilterBlockName   = 'mage/catalog_layer_filter_decimal';
        }
    }

	/**
	 * Gets a filter label, and creates and returns a filter block for that label.
	 */
	protected function _createFilterBlock($label)
	{
		$helper = Mage::helper('conversionpro');
		if ($label == $helper->getSearchEngine()->getPriceFieldName()) {
			$filterBlockName = $this->_priceFilterBlockName;
			
			//Choose template to use according to the price selector type.
			$template = 'catalog/layer/filter.phtml';
			if ($helper->isPriceSliderEnabled()) {
				$template = 'conversionpro/catalog/layer/price-question.phtml';
			}
			$filterAttribute = new Celebros_Conversionpro_Model_Catalog_Layer_Filter_Price;
			$filterAttribute->setRequestVar('price');
			$filterAttribute->setAttributeCode('price');
			$filterAttribute->setFrontendLabel($label);
			$filterAttribute->setStoreLabel($label);
			
			return $this->getLayout()->createBlock($filterBlockName)
				->setLayer($this->getLayer())
				->setAttributeModel($filterAttribute)
				->setTemplate($template) //Setting our own template for a slider selector type.
				->init();
		} elseif (strtolower($label) == 'category') {
			return $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
				->setLayer($this->getLayer())
				->init();
		} else {
			$filterBlockName = $this->_attributeFilterBlockName;
			
			// Check whether or not multiselect is enabled, so as to set the right template to it.
			$template = 'catalog/layer/filter.phtml';
			if ($helper->isMultiselectEnabled()) {
				$template = 'conversionpro/catalog/layer/filter.phtml';
			}
			
			$filterAttribute = new Celebros_Conversionpro_Model_Catalog_Layer_Filter_Attribute;
			$filterAttribute->setRequestVar(strtolower($label));
			$filterAttribute->setAttributeCode(strtolower($label));
			$filterAttribute->setFrontendLabel($label);
			$filterAttribute->setStoreLabel($label);
			
			return $this->getLayout()->createBlock($filterBlockName)
				->setLayer($this->getLayer())
				->setAttributeModel($filterAttribute)
				->setTemplate($template) //Setting our own template for multi-select layers.
				->init();
		}
	}
	
	
    /**
     * Prepare child blocks
     *
     * @return Celebros_Conversionpro_Block_Catalog_Layer_View
     */
    protected function _prepareLayout()
    {
		$helper = Mage::helper('conversionpro');
        if ($helper->getIsEngineAvailableForNavigation()) {
			$stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
                ->setLayer($this->getLayer());

			//Running over each param in the request URL, and using the $questionTexts array to verify whether each one is a 
			// filter parameter or not.
			//For each parameter, we'll add a filter to the products collection, that will affect the search results.
			$params = Mage::app()->getRequest()->getParams();
			$questionTexts = $helper->getQuestionTexts();
			foreach ($params as $code => $values) {
				if ($code == 'cat') {
					$helper->getCurrentLayer()->getProductCollection()->addFqFilter(array(
						'category' => Mage::app()->getRequest()->getParam($code)
					));
				} elseif ($code == 'price') {
					$arr = array();
					$orig_filter = Mage::app()->getRequest()->getParam($code);
					preg_match_all('/_P(\d*)_(\d*)/', $orig_filter, $arr, PREG_PATTERN_ORDER);
					list($orig, $from, $to) = $arr;
					
					$helper->getCurrentLayer()->getProductCollection()->addFqFilter(array(
						'price' => $orig_filter
					));
				} elseif (array_key_exists($code, $questionTexts)) {
					$values = explode(',', Mage::app()->getRequest()->getParam($code));
					foreach ($values as $answerId) {
						$helper->getCurrentLayer()->getProductCollection()->addFqFilter(array(
							strtolower($code) => $answerId
						));
					}
				}
			}
			
			//When nav2search is enabled, and someone clicks on a category from the navigation menu,
			// there won't be a 'cat' parameter in the url, but we still want to register the category he picked
			// for use when querying the Quiser API.
			// Take into consideration that usually this is done in the category block/model and this code probably has no influence.
			$currentCategory = Mage::registry('current_category');
			if (!Mage::app()->getRequest()->getParams('cat') && isset($currentCategory)) {
				//Add the category filter differently for the different options under the nav2search config menu.
				if (Mage::helper('conversionpro')->getCelebrosConfigData('nav_to_search_settings/nav_to_search_search_by') == 'answer_id') {
					//This option adds a filter with the category\'s answer id.
					$helper->getCurrentLayer()->getProductCollection()->addFqFilter(array(
						'category' => $currentCategory->getId()
					));
				} else {
					//This option adds a search query string with the name of the category.
					$query = $helper->getCategoryRewriteQuery($currentCategory);
					$helper->getCurrentLayer()->getProductCollection()->addSearchFilter($query);
				}
			}
			
			$this->setChild('layer_state', $stateBlock);
		
            $this->getLayer()->apply();
        } else {
            parent::_prepareLayout();
        }

        return $this;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $helper = Mage::helper('conversionpro');
		if ($helper->getIsEngineAvailableForNavigation()) {
			return ($this->canShowOptions() || count($this->getFilters()));
		}
		
		return ($this->canShowOptions() || count($this->getLayer()->getState()->getFilters()));
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $helper = Mage::helper('conversionpro');
        if ($helper->getIsEngineAvailableForNavigation()) {
            return $helper->getCurrentLayer();
        }

        return parent::getLayer();
    }

	/**
     * Get all layer filters
     *
	 * We're getting the list of filters from Quiser's questions instead of Magento's data.
	 *
     * @return array
     */
    public function getFilters()
    {
		$helper = Mage::helper('conversionpro');
		if ($helper->getIsEngineAvailableForNavigation()) {
			//At first, we create a filter block for each returned question from the XML.
			$filterableAttributes = $helper->getQuestions();
			foreach ($filterableAttributes as $attribute) {
				if ($attribute->Id == 'PriceQuestion') {
					if (!array_key_exists('price_filter', $this->_loadedFilters)) {
						$this->_loadedFilters['price_filter'] = $this->_createFilterBlock($helper->getSearchEngine()->getPriceFieldName());
						$this->setChild('price_filter', $this->_loadedFilters['price_filter']);
					}
				} else {
					$code = strtolower($attribute->Text);
					if (!array_key_exists($code .  '_filter', $this->_loadedFilters)) {
						$this->_loadedFilters[$code .  '_filter'] = $this->_createFilterBlock($attribute->Text);
						$this->setChild($code . '_filter', $this->_loadedFilters[$code . '_filter']);
					}
				}
			}

			//Now, we go over the filters we've defined in the products collection.
			$searchParams = $helper->getCurrentLayer()->getProductCollection()->getExtendedSearchParams();
			unset($searchParams['query_text']);
			unset($searchParams['visibility']);
			foreach ($searchParams as $filter => $values) {
				//If one of these filters wasn't in the returned questions, create a block for it.
				if (!array_key_exists(strtolower($filter) . '_filter', $this->_loadedFilters)) {
					if ($filter == 'price') {
						$this->_loadedFilters['price_filter'] = $this->_createFilterBlock($helper->getSearchEngine()->getPriceFieldName());
						$this->setChild('price_filter', $this->_loadedFilters['price_filter']);
					} else { 
						$this->_loadedFilters[strtolower($filter) .  '_filter'] = $this->_createFilterBlock($filter);
						$this->setChild(strtolower($filter) . '_filter', $this->_loadedFilters[strtolower($filter) . '_filter']);
					}
				}

				//Create state tags for every active filter except for price and category, which we cover somewhere else.
				if ($filter != 'category' && $filter != 'price') {
				
					//Creating an attribute model for use in the state tag we'll be creating soon.
					$attribute = Mage::getResourceModel('catalog/product_attribute_collection')
						->addFieldToFilter('attribute_code', $filter)
						->getFirstItem();
					
					//These two lines simulate what would have happened had I just reinstantiated the block and assigned the
					// model to it, as is done in the commented section above.
					$attribute->setRequestVar($attribute->getAttributeCode());
					$attribute->setName($attribute->getStoreLabel());
					
					//Do this for each filter value (to cover multiselect filters)
					foreach ($values as $value) {
	
						//Get answer text according to the answer id.
						$answerText = $helper->getAnswerTextByAnswerId($filter, $value);
						
						//Only create a new state tag in case it wasn't already created.
						if (!array_key_exists($value, $this->_stateTags)) {
							//Create the state tag for this answer with all the info we've just gathered.
							$this->_stateTags[$value] = $helper->getCurrentLayer()->getState()->addFilter(Mage::getModel('conversionpro/catalog_layer_filter_item')
								->setFilter($attribute)
								->setLabel($answerText)
								->setValue($value)
								->setCount(0));
						}
					}
				}
			}
			
			//Now we start anew, and create a list of filters from the questions returned from the XML (to maintain the original order).
			$filters = array();
			$filterableAttributes = Mage::helper('conversionpro')->getQuestions();
			foreach ($filterableAttributes as $attribute) {
				if ($attribute->Id == 'PriceQuestion') {
					$filters['price'] = $this->getChild('price_filter');
				} elseif ($attribute->Text == 'Category') {
					$filters['category'] = $this->_getCategoryFilter();
				} else {
					$filters[strtolower($attribute->Text)] = $this->getChild(strtolower($attribute->Text) . '_filter');
				}
			}
			
			//Adding the selected questions that don't appear in the XML (because they don't have multiselect enabled).
			// These items won't appear, but we want to consider them in the filters count.
			foreach ($searchParams as $filter => $values) {
				if (!array_key_exists($filter, $filters)) {
					$filters[$filter] = $this->getChild(strtolower($filter) . '_filter');
				}
			}
			return $filters;
		}
		return parent::getFilters();
    }
}
