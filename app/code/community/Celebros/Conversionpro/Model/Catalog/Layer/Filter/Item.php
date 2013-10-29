<?php
class Celebros_Conversionpro_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    /**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl()
    {		
		//Basic url settings - persist the previous url, reset the page. We'll fill in the answers further down.
		$params['_current']     = true;
		$params['_use_rewrite'] = true;
		$params['_query']       = array(
			Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
		);

		$helper = Mage::helper('conversionpro');
		
		//Get the list of selected answers from the session variable.
		$previous_search = $helper->getPreviousSearch();
		
		$request_var = $this->getFilter()->getRequestVar();
		if ($request_var == 'cat') {
			$request_var = 'category';
		}

		$answers = array();
		if (array_key_exists($request_var, $previous_search['filters'])) {
			$answers = $previous_search['filters'][$request_var];
		}

		$value = $this->getValue();
		
		if (in_array($value, $answers)) {
			//If the answer passed to this function was already selected, we'll remove it from the array.
			$answers = array_diff($answers, array($value));
		} else {
			//If the currently selected answer wasn't previously chosen, add it to the array.
			//We further split the behavior according to whether or not this question is hierarchical.
			//If we're not supposed to support multiseletions for any reason, we'll create an array with just one answer.
			//Otherwise, we'll add the new value onto the existing array.
			if ($helper->isHierarchical($request_var) || !$helper->isMultiselectEnabled()) {
				$answers = array($value);
			} else {
				$answers[] = $value;
			}
		}

		//Create a query parameter for this question with a comma delimeter to separate the answer ids.
		$params['_query'][$request_var] = (count($answers))
			? implode(',',$answers)
			: null;

		$url = Mage::getUrl('*/*/*', $params);

		return $url;
    }

    /**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl()
    {
		return $this->getUrl();
    }

    /**
     * Get url for "clear" link
     *
     * @return false|string
     */
    public function getClearLinkUrl()
    {
		$clearLinkText = $this->getFilter()->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        $urlParams = array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array($this->getFilter()->getRequestVar() => null),
            '_escape' => true,
        );
		
        return Mage::getUrl('*/*/*', $urlParams);
    }
	
	public function isSelected()
	{
		//Get the list of selected answers from the session variable.
		$previous_search = Mage::helper('conversionpro')->getPreviousSearch();
		
		if (array_key_exists($this->getFilter()->getRequestVar(), $previous_search['filters'])) {
			$answers = $previous_search['filters'][$this->getFilter()->getRequestVar()];
			if (in_array($this->getValue(), $answers)) {
				return true;
			}
		}
		
		return false;
	}
}
