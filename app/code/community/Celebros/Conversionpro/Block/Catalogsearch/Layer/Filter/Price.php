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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Catalog layer price filter
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Celebros_Conversionpro_Block_Catalogsearch_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{
    /**
     * Initialize Price filter module
     *
     */
    public function __construct()
    {
        parent::__construct();
        // This block runs on both catalog and search pages, so we don't know what to check for. That's why we're using 
		//  getIsEngineAvailable() instead of getIsEngineAvailableForNavigation().
		if (Mage::helper('conversionpro')->getIsEngineAvailable()) {
			$this->_filterModelName = 'conversionpro/catalog_layer_filter_price';
		} else {
			$this->_filterModelName = 'catalog/layer_filter_price';
		}
    }
	
    /**
     * Prepare filter process
     *
     * @return Mage_Catalog_Block_Layer_Filter_Price
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
	
	public function getPriceQuestion()
	{
		$priceQuestion = null;

		$helper = Mage::helper('conversionpro');
		$filters = $helper->getQuestions();

		//Adds question data to the session, if this request returned the price question.
		$price_field = Mage::getResourceSingleton('conversionpro/fulltext_engine')->getPriceFieldName();
		if (!$this->isPriceSlider()) {
			if (array_key_exists($price_field, $filters)) {
				$helper->setPriceQuestion($filters[$price_field]);
			} else {
				$helper->setPriceQuestion(null);
			}
		}

		$priceQuestion = $helper->getPriceQuestion();

		return $priceQuestion;
	}
	
	public function isAnsweredQuestion($question){
		$searchPathEntries = Mage::helper('conversionpro')->getSearchPathEntries();
		return isset($searchPathEntries[$question->Id]);
	}
	
	public function isPriceSlider()
	{
		return Mage::helper('conversionpro')->isPriceSliderEnabled();
	}
	
    public function getMaxPrice($priceQuestion){
    	$answerId = end($priceQuestion->Answers->Items)->Id;
    	$max = (int) preg_replace( '/_P\d*_/', "" ,$answerId);
        return $max;
    }
	
    public function getAnsweredPriceRange($priceQuestion){
    	$answeredPriceRange = array();
		$helper = Mage::helper('conversionpro');

		$previous_search = $helper->getPreviousSearch();

		if (isset($previous_search['filters']['price'])) {
			$answerId = $previous_search['filters']['price'][0];
			$tmp = preg_replace( '/_P/', "" ,$answerId);
			$answeredPriceRange[0] = (int) preg_replace( '/_\d*/', "" ,$tmp);
			$answeredPriceRange[1] = (int) preg_replace( '/_P\d*_/', "" ,$answerId);
		}

	    if(count($answeredPriceRange) == 0) {
	    	$answeredPriceRange[0] = 0;
	    	$answeredPriceRange[1] = $this->getMaxPrice($priceQuestion);
    	}
    	return $answeredPriceRange;
    }
	
	public function answerQuestionUrl($answer = null)
	{		
		//Basic url settings - persist the previous url, reset the page.
		//We're resetting the price question so we'll be able to replace it later on easily.
		$params['_current']     = true;
		$params['_use_rewrite'] = true;
		$params['_query']       = array(
			Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null, // exclude current page from urls
			$this->_filter->getRequestVar() => null
		);

		$url = Mage::getUrl('*/*/*', $params);
		//$url = str_replace($this->_filter->getRequestVar().'=&','',$url);
		
		return $url;
	}
	
	/*
	 * Returns the url of the product page, in case of a single search result (only for ajax calls).
	 * In case there's no results, or more than 1, it'll return an emtpy string.
	 */
	public function getSingleSearchResultUrl()
	{
		$helper = Mage::helper('conversionpro');
		if ($helper->hasSearchResults()) {
			$searchResults = $helper->getSearchResults();
			if($searchResults->Products && $searchResults->Products->Items
				&& count($searchResults->Products->Items) == 1) {

				return Mage::getModel('catalog/product')->load(
							$searchResults->Products->Items[0]->Field[Mage::helper('conversionpro/mapping')->getMapping('id')]
						)->getProductUrl();
			}
		}
		
		return '';
	}
}
