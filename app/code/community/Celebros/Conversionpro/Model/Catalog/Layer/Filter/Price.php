<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Celebros_Conversionpro
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Layer price filter
 *
 * @category    Enterprise
 * @package     Celebros_Conversionpro
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Celebros_Conversionpro_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    const CACHE_TAG = 'MAXPRICE';

    /**
     * Whether current price interval is divisible
     *
     * @var bool
     */
    protected $_divisible = true;

    /**
     * Ranges faceted data
     *
     * @var array
     */
    protected $_facets = array();
	
    /**
     * Return cache tag for layered price filter
     *
     * @return string
     */
    public function getCacheTag()
    {
        return self::CACHE_TAG;
    }

    /**
     * Get facet field name based on current website and customer group
     *
     * @return string
     */
    protected function _getFilterField()
    {
        $engine = Mage::getResourceSingleton('conversionpro/fulltext_engine');
        $priceField = $engine->getPriceFieldName();

        return $priceField;
    }

	public function getItemsCount()
    {
        //Refresh # of items to get an acurate # of results.
		$this->_initItems();
		
		//If a price was already chosen (and we're using the slider selector), display the selector.
		$previous_search = Mage::helper('conversionpro')->getPreviousSearch();
		if (isset($previous_search['filters']['price']) && Mage::helper('conversionpro')->isPriceSliderEnabled()) {
			return 1;
		}
		
		//If a price wasn't selected, use the # of results to decide whether to display it or not.
		return count($this->getItems());
    }
	
    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
    	$helper = Mage::helper('conversionpro');
    	$filters = $helper->getQuestions();
    	
    	//Adds question data to the session, if this request returned the price question.
    	$price_field = Mage::getResourceSingleton('conversionpro/fulltext_engine')->getPriceFieldName();
    	if (!$helper->isPriceSliderEnabled()) {
    		if (array_key_exists($price_field, $filters)) {
    			$helper->setPriceQuestion($filters[$price_field]);
    		} else {
    			$helper->setPriceQuestion(null);
    		}
    	}
    	
    	$question = $helper->getPriceQuestion();

    	if(!isset($question) || count($question) == 0) return array();
		
		$data = array();
    	if($question->Answers->Count > 0) {
    		foreach ($question->Answers->Items as $answer) {
    			$answerId = $answer->Id;
    			$from = $helper->getPriceFrom($answerId);
    			$to = $helper->getPriceTo($answerId);
    			$priceRange = "_P{$from}_{$to}";
    
    			$data[] = array(
    					'label' => $answer->Text,
    					'value' => $priceRange,
    					'count' => $answer->ProductCount,
    					'from'  => $from,
    					'to'    => $to,
    			);
    		}
    	}
    
    	if($question->ExtraAnswers->Count > 0) {
    		foreach ($question->ExtraAnswers->Items as $answer) {
    			$answerId = $answer->Id;
    			$from = $helper->getPriceFrom($answerId);
    			$to = $helper->getPriceTo($answerId);
    			$priceRange = "_P{$from}_{$to}";
    
    			$data[] = array(
    					'label' => $answer->Text,
    					'value' => $priceRange,
    					'count' => $answer->ProductCount,
    					'from'  => $from,
    					'to'    => $to,
    			);
    		}
    	}
		if (!is_array($data)) {
			Mage::log('error from price');
		}
    	return $data;
    }

    /**
     * Get maximum price from layer products set using cache
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
		$searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams . '_' . $this->getCurrencyRate())));
        $cacheKey = 'MAXPRICE_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            $stats = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());

			//This replaces the search I commented out below, and gets the right max price from the db.
			$max = Mage::getModel('catalog/product')->getCollection()
				->addFieldToFilter('entity_id', array('in' => $stats['ids']))
				->addAttributeToSelect('price')
				->addAttributeToSort('price', 'desc')
				->getFirstItem()->getPrice();
            //$max = $stats[$this->_getFilterField()]['max'];
			
            if (!is_numeric($max)) {
                $max = parent::getMaxPriceInt();
            } else {
                $max = floor($max * $this->getCurrencyRate());
            }

            $cachedData = $max;
            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        return $cachedData;
    }

    /**
     * Apply filter value to product collection based on filter range and selected value
     *
     * @deprecated since 1.12.0.0
     * @param int $range
     * @param int $index
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    protected function _applyToCollection($range, $index)
    {
        $to = $range * $index;
        if ($to < $this->getMaxPriceInt()) {
            $to -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
        }

        $value = array(
            $this->_getFilterField() => array(
                'from' => ($range * ($index - 1)),
                'to'   => $to
            )
        );

		$this->getLayer()->getProductCollection()->addFqFilter($value);
        
		return $this;
    }

    /**
     * Apply price range filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    protected function _applyPriceRange()
    {
		//Setting the name of the price filter according to the price field name that's passed from the Quiser XML.
		$this->getAttributeModel()->setStoreLabel(Mage::helper('conversionpro')->getSearchEngine()->getPriceFieldName());
		
		//Getting the value of the price filter that's passed from the HTTP request URL.
		$orig_filter = Mage::app()->getRequest()->getParam($this->getRequestVar());
		if ($orig_filter) {

			//Getting the from and to values from the filter in the request URL.
			$arr = array();
			preg_match_all('/_P(\d*)_(\d*)/', $orig_filter, $arr, PREG_PATTERN_ORDER);
			list($orig, $from, $to) = $arr;
			
			//We now transform the structure of the filter in the URL to that which is expected of a price answer in Conversion Pro.
			//$orig_filter = "_P" . str_replace("-", "_", $orig_filter);

			// For some reason, this class runs twice, and not as a singelton, so we have no way of monitoring
			// whether the state filter was already added or not by using some class parameter. Instead, 
			// we're going over all the filters to see if any has 'Price' for the name, and only adding the state 
			// tag if we can't find one.
			$isFilterApplied = false;
			
			$filters = $this->getLayer()->getState()->getFilters();
			foreach ($filters as $filter) {
				if ($filter->getName() == $this->getName()) {
					$isFilterApplied = true;
				}
			}
			
			//If there's no state tag for the price yet, create it.
			if (!$isFilterApplied) {
				$this->getLayer()->getState()->addFilter($this->_createItem(
					$this->_renderRangeLabel(empty($from[0]) ? 0 : $from[0], $to[0]),
					$orig_filter
				));
			}
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
	
	/**
     * Apply price range filter
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
		$this->_applyPriceRange();
		
        return $this;
    }

    /**
     * Get comparing value according to currency rate
     *
     * @param float|null $value
     * @param bool $decrease
     * @return float|null
     */
    protected function _prepareComparingValue($value, $decrease = true)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($decrease) {
            $value -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 2;
        } else {
            $value += Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 2;
        }

        $value /= $this->getCurrencyRate();
        if ($value < 0) {
            $value = null;
        }

        return $value;
    }

    /**
     * Load range of product prices
     *
     * @param int $limit
     * @param null|int $offset
     * @param null|int $lowerPrice
     * @param null|int $upperPrice
     * @return array|false
     */
    public function loadPrices($limit, $offset = null, $lowerPrice = null, $upperPrice = null)
    {
        $lowerPrice = $this->_prepareComparingValue($lowerPrice);
        $upperPrice = $this->_prepareComparingValue($upperPrice);
        if (!is_null($upperPrice)) {
            $upperPrice -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
        }
        $result = $this->getLayer()->getProductCollection()->getPriceData($lowerPrice, $upperPrice, $limit, $offset);
        if (!$result) {
            return $result;
        }
        foreach ($result as &$v) {
            $v = round((float)$v * $this->getCurrencyRate(), 2);
        }
        return $result;
    }

    /**
     * Load range of product prices, preceding the price
     *
     * @param float $price
     * @param int $index
     * @param null|int $lowerPrice
     * @return array|false
     */
    public function loadPreviousPrices($price, $index, $lowerPrice = null)
    {
        $originLowerPrice = $lowerPrice;
        $lowerPrice = $this->_prepareComparingValue($lowerPrice);
        $price = $this->_prepareComparingValue($price);
        if (!is_null($price)) {
            $price -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
        }
        $countLess = $this->getLayer()->getProductCollection()->getPriceData($lowerPrice, $price, null, null, true);
        if (!$countLess) {
            return false;
        }

        return $this->loadPrices($index - $countLess + 1, $countLess - 1, $originLowerPrice);
    }

    /**
     * Load range of product prices, next to the price
     *
     * @param float $price
     * @param int $rightIndex
     * @param null|int $upperPrice
     * @return array|false
     */
    public function loadNextPrices($price, $rightIndex, $upperPrice = null)
    {
        $lowerPrice = $this->_prepareComparingValue($price);
        $price = $this->_prepareComparingValue($price, false);
        $upperPrice = $this->_prepareComparingValue($upperPrice);
        if (!is_null($price)) {
            $price += Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
        }
        if (!is_null($upperPrice)) {
            $upperPrice -= Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE / 10;
        }
        $countGreater = $this->getLayer()->getProductCollection()->getPriceData($price, $upperPrice, null, null, true);
        if (!$countGreater) {
            return false;
        }

        $result = $this->getLayer()->getProductCollection()->getPriceData(
            $lowerPrice,
            $upperPrice,
            $rightIndex - $countGreater + 1,
            $countGreater - 1,
            false,
            'desc'
        );
        if (!$result) {
            return $result;
        }
        foreach ($result as &$v) {
            $v = round((float)$v * $this->getCurrencyRate(), 2);
        }
        return $result;
    }
	
	/**
     * Retrieve layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        //Making sure we're always getting the conversion pro version of the layer model 
		// (to support the call to getExtendedSearchParams)
		
		//$layer = $this->_getData('layer');
		//Mage::log(get_class($layer));
        //if (is_null($layer)) {
            $layer = Mage::helper('conversionpro')->getCurrentLayer();
            $this->setData('layer', $layer);
        //}
		//Mage::log(get_class($layer));
		return $layer;
	}
}
