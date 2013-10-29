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
 * Catalog attribute layer filter
 *
 * @category   Enterprise
 * @package    Celebros_Conversionpro
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Celebros_Conversionpro_Block_Catalogsearch_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Set model name
     */
    protected function _construct()
    {
        parent::_construct();
        // This block runs on both catalog and search pages, so we don't know what to check for. That's why we're using 
		//  getIsEngineAvailable() instead of getIsEngineAvailableForNavigation().
		if (Mage::helper('conversionpro')->getIsEngineAvailable()) {
			$this->_filterModelName = 'conversionpro/search_layer_filter_attribute';
		} else {
			$this->_filterModelName = 'catalog/layer_filter_attribute';
		}
    }
	
	public function initItems()
	{
		$this->getAttributeModel()->initItems();
	}

    /**
     * Set attribute model
     *
     * @return Celebros_Conversionpro_Block_Catalogsearch_Layer_Filter_Attribute
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
	
	public function isHierarchical()
	{
		Mage::helper('conversionpro')->isHierarchical($this->getAttributeModel()->getAttributeCode());
	}
	
	public function isMultiselectEnabled()
	{
		return Mage::helper('conversionpro')->isMultiselectEnabled();
	}
}
