<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Source for cron hours
 *
 * @category    Find
 * @package     Find_Feed
 */
class WebMods_Solrsearch_Model_Adminhtml_System_Source_Config_BoostFields
{

    /**
     * Fetch options array
     * 
     * @return array
     */
    public function toOptionArray()
    {

    	$entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
		$catalogProductEntityTypeId = $entityType->getEntityTypeId();
		
		$boostFields = Mage::getStoreConfig('webmods_solrsearch_boost/settings/enabled_fields', 0);
		$boostFieldsArray = explode(",",$boostFields);
    	//get attribute settings
    	$attributesInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setEntityTypeFilter($catalogProductEntityTypeId)
		->setCodeFilter($boostFieldsArray)
		->addSetInfo()
		->getData();
    	//Get attribute values
    	$attributeValueArray = array();
    	
		foreach ($attributesInfo as $attsetting){
			$attributeArray = array();
			if ($attsetting['frontend_input'] == 'select' || $attsetting['frontend_input'] == 'text'){
				$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attsetting['attribute_code']);
				foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
					$attributeArray[$option['value']] = $option['label'];
				}
				$attributeValueArray[$attsetting['attribute_code']] = $attributeArray;
			}
		}		
        return $attributeValueArray;
    }
}
