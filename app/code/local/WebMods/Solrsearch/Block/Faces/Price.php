<?php
/**
 * @category SolrBridge
 * @package WebMods_Solrsearch
 * @author	Hau Danh
 * @copyright	Copyright (c) 2011-2013 Solr Bridge (http://www.solrbridge.com)
 *
 */
class WebMods_Solrsearch_Block_Faces_Price extends Mage_Core_Block_Template
{
	public function __construct()
	{
		$this->setTemplate('solrsearch/standard/searchfaces/price.phtml');
	}
	
	public function getSolrData(){
		return $this->getParentBlock()->getSolrData();
	}
	
	/**
	 * Calculate price ranges
	 * @param array $priceRanges
	 * @param decimal $min
	 * @param decimal $max
	 * @return array:
	 */
	protected function calculatePriceRanges()
	{
	
		$solrData = $this->getSolrData();
	
		$priceRanges = array();
	
		if ( isset($solrData['facet_counts']['facet_ranges']['price_decimal']['counts']) && is_array($solrData['facet_counts']['facet_ranges']['price_decimal']['counts'])) {
			$priceRanges = $solrData['facet_counts']['facet_ranges']['price_decimal']['counts'];
		}
	
		$min = 0.0;
		if (isset($solrData['stats']['stats_fields']['price_decimal']['min'])) {
			$min = $solrData['stats']['stats_fields']['price_decimal']['min'];
		}
	
		$max = 0.0;
		if (isset($solrData['stats']['stats_fields']['price_decimal']['max'])) {
			$max = $solrData['stats']['stats_fields']['price_decimal']['max'];
		}
	
		$tempPriceRanges = array();
		$tempPriceRanges[] = $min;
		if (is_array($priceRanges)) {
			$index = 0;
			foreach ($priceRanges as $key=>$value) {
				if ($index > 0) {
					$tempPriceRanges[] = $key;
				}
				$index++;
			}
		}
		//$tempPriceRanges[] = $max;
	
		$returnPriceRanges = array();
		$index = 0;
		foreach ($tempPriceRanges as $item) {
			$start = $item;
			$end = $item;
				
			if (isset($tempPriceRanges[($index + 1)])) {
				$end = ($tempPriceRanges[($index + 1)] - 1);
				if (($index + 1) == (count($priceRanges) - 1)) {
					$end = $max;
				}
			}
			if ($index < (count($tempPriceRanges) - 1)) {
				$returnPriceRanges[] = array('start' => $start, 'end' => $end);
			}
			
			$index++;
		}
		
		return $returnPriceRanges;
	}
	
	protected function applyPriceRangeProductCount(){
		$priceRanges = $this->calculatePriceRanges();
		$appliedPriceRanges = array();
		$solrData = $this->getSolrData();
		$priceFacets = array();
	
		if ( isset($solrData['facet_counts']['facet_fields']['price_decimal']) && is_array($solrData['facet_counts']['facet_fields']['price_decimal'])) {
			$priceFacets = $solrData['facet_counts']['facet_fields']['price_decimal'];
		}
		
		$currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		
		$currencyPositionSetting = $this->helper('solrsearch')->getSettings('currency_position');
	
		foreach ($priceRanges as $range) {
			$start = floor(floatval($range['start']));
			$end = ceil(floatval($range['end']));
			
			if ($currencyPositionSetting > 0)
			{
				$formatted = $currencySign.'&nbsp;'.$start.' - '.$currencySign.'&nbsp;'.$end;
			}else {
				$formatted = $start.'&nbsp;'.$currencySign.' - '.$end.'&nbsp;'.$currencySign;
			}
			
			$rangeItemArray = array(
					'start' => $start,
					'end' => $end,
					'count' => 0,
					'formatted' => $formatted,
					'value' => $start.' TO '.$end,
			);
			foreach ($priceFacets as $price => $count) {
				$price = floor($price);
				if (floatval($price) >= floatval($start) && floatval($price) <= floatval($end)) {
					$rangeItemArray['count'] = ($rangeItemArray['count'] + $count);
				}
			}
				
			$appliedPriceRanges[] = $rangeItemArray;
		}
	
		return $appliedPriceRanges;
	}
	
	public function getFacetPriceRanges()
	{
		return $this->applyPriceRangeProductCount();
	}
	
	public function getFacesUrl($params = array())
	{
		return $this->getParentBlock()->getFacesUrl($params);
	}
}