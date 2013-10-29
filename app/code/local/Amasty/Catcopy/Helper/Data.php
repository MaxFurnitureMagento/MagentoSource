<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Catcopy
*/
class Amasty_Catcopy_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSearchReplaceCnt()
    {
        return 10;
    }
    
    public function suggestUrlKey($urlKey)
    {
        return $urlKey;
        
        /*if (preg_match('@([^\d]+)(\d+)$@', $urlKey, $matches))
        {
            if (isset($matches[2]))
            {
                $urlKey = substr($urlKey, 0, -strlen($matches[2]));
                $urlKey = $urlKey . ++$matches[2];
                return $urlKey;
            }
        }
        return $urlKey . '-1';*/
    }
    
    public function copyStoreData($fromCategoryId, $toCategoryId)
    {
        $attributes = Mage::getModel('eav/entity_attribute')
             ->getCollection()
             ->addFieldToSelect('attribute_code')
             ->addFieldToFilter('entity_type_id', array('eq' => 3))
             ->getData()
        ;
        
        foreach($attributes as $attribute){
            $dataToCopy[] = $attribute['attribute_code'];
        }
        $unsetArray = array('is_anchor', 'path', 'position', 'path_in_store', 'url_path', 'level');
        foreach($unsetArray as $field){
            unset($dataToCopy[$field]);
        }
    
        // with no store first (store_id = 0):
        $toCategory  = Mage::getModel('catalog/category')->load($toCategoryId);
        $customAttributes = $this->fieldsWithCustomValues($fromCategoryId, true);  
        foreach ($dataToCopy as $field)
        {
            if (array_key_exists($field, $customAttributes))
            {
               $toCategory->setData($field, $customAttributes[$field]);
            } else {
                $toCategory->setData($field, false);
            }                    
        }
        
        $this->searchAndReplace($toCategory);
        $toCategory->save();

        $stores = Mage::app()->getStores();
        if (!empty($stores))
        {
            foreach ($stores as $store){
                $toCategory   = Mage::getModel('catalog/category')->setStoreId($store->getId())->load($toCategoryId);
        
                $customAttributes = array();
                $customAttributes = $this->fieldsWithCustomValues($fromCategoryId, false);       
                foreach ($dataToCopy as $field)
                {
                    if (array_key_exists($field, $customAttributes))
                    {
                        $toCategory->setData($field, $customAttributes[$field]);
                    } else {
                        $toCategory->setData($field, false);
                    }                  
                }
                $this->searchAndReplace($toCategory);
                $toCategory->save();
            }
        } 
    }
    
    public function searchAndReplace($category)
    {
        $fieldsToReplaceIn = array(
            'name',
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
        );
        
        $search = Mage::app()->getRequest()->getParam('search');
        $replace = Mage::app()->getRequest()->getParam('replace');
        if ($search && $replace)
        {
            foreach ($fieldsToReplaceIn as $field)
            {       
                if (!is_null($category->getData($field)))
                {
                    $value = $category->getData($field);
                    if ($value)
                    {
                        foreach ($search as $i => $searchEntity)
                        {
                            if ($searchEntity && isset($replace[$i]))
                            {
                                $value = str_replace($searchEntity, $replace[$i], $value);
                            }
                        }
                        $category->setData($field, $value);
                    }
                }
            }   
        }
    }
        
    public function handleUrlRewrites($category)
    {
        $connection = Mage::getSingleton('core/resource') ->getConnection('core_write');
        $sql = 'DELETE FROM `' . $category->getResource()->getTable('core/url_rewrite') . '` WHERE category_id = "' . $category->getId() . '"';
        $connection->query($sql);
        Mage::getModel('catalog/url')->refreshCategoryRewrite($category->getId()); 
    }
    
    public function fieldsWithCustomValues($categoryId, $mainStore)
    {
        $customAttributes = array();
        $connection = Mage::getSingleton('core/resource') ->getConnection('core_write');
        $tableArray = array('catalog_category_entity_varchar', 'catalog_category_entity_text', 'catalog_category_entity_int', 'catalog_category_entity_decimal', 'catalog_category_entity_datetime');
        foreach($tableArray as $table){
            $tableName = Mage::getSingleton('core/resource')->getTableName($table);
            if($mainStore) { 
                $sql = 'SELECT attribute_id, value FROM `' . $tableName . '` WHERE entity_id = "' . $categoryId . '" and store_id = 0'; 
            } else {
                $sql = 'SELECT attribute_id, value FROM `' . $tableName . '` WHERE entity_id = "' . $categoryId . '" and store_id != 0'; 
            }
            $results = $connection->fetchAll($sql);
            foreach($results as $result) {
                $attributeName = Mage::getModel('eav/entity_attribute')
                     ->getCollection()
                     ->addFieldToSelect('attribute_code')
                     ->addFieldToFilter('attribute_id', array('eq' => (int)$result['attribute_id']))
                     ->getFirstItem()
                     ->getData()
                ;
                $customAttributes[$attributeName['attribute_code']] = $result['value'];          
            }
        }
        return $customAttributes;
    }
}