<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected $_addPrice = array();
    protected $_addUrl = false;
    protected $_addQty = false;
    protected $_addParentId = false;
    protected $_addIsInStock = false;
    protected $_addCategory = false;
    protected $_addTax = false;
    protected $_addStockAvailability = false;
    
    
    protected $_qtyConds = array();
    protected $_catConds = array();
    protected $_priceConds = array();
    protected $_taxConds = array();
    
    protected $_joined = array();
    
    protected function _joinCustomAttribute($attribute, $storeId){
        if (!in_array($attribute, $this->_joined)){
            $this->_joined[] = $attribute;
            $this->joinAttribute($attribute, 'catalog_product/' . $attribute, 'entity_id', null, 'left', $storeId);
        }
    }
    
    
    protected function _parseAttributeField($fields, $key, $storeId, $fieldAttr, &$attr, &$notJoin){
        if ('qty' == $fields[$fieldAttr][$key]) {
            $this->_addQty = true;
        } elseif ('parent_id' == $fields[$fieldAttr][$key]) {
            $this->_addParentId = true;
        } elseif ('url' == $fields[$fieldAttr][$key]) {
            $this->_addUrl = true;
        } elseif (in_array($fields[$fieldAttr][$key], array('final_price', 'min_price', 'tier_price'))) {
                $this->_addPrice[] = $fields[$fieldAttr][$key];
        } elseif ('tax_percents' == $fields[$fieldAttr][$key]) {
            $this->_addTax = true;
        } elseif ('is_in_stock' == $fields[$fieldAttr][$key]) {
            $this->_addIsInStock = true;
        } elseif (in_array($fields[$fieldAttr][$key], array('category_id', 'category_name', 'categories'))) {
            $this->_addCategory = true;
        } elseif ('stock_availability' == $fields[$fieldAttr][$key]) {
            $this->_addStockAvailability = true;
        } else if ('sale_price_effective_date' == $fields[$fieldAttr][$key]){
            $this->_joinCustomAttribute('special_from_date', $storeId);
//            if (!in_array($fields[$fieldAttr][$key], 'special_from_date')){
//                $attr[] = 'special_from_date';
//                $this->joinAttribute('special_from_date', 'catalog_product/special_from_date', 'entity_id', null, 'left', $storeId);
//            }
            $this->_joinCustomAttribute('special_to_date', $storeId);
//            if (!in_array($fields[$fieldAttr][$key], 'special_to_date')){
//                $attr[] = 'special_to_date';
//                $this->joinAttribute('special_to_date', 'catalog_product/special_to_date', 'entity_id', null, 'left', $storeId);
//            }
            
            
        } elseif ( !in_array($fields[$fieldAttr][$key], $notJoin)) {
            
            if ($fields[$fieldAttr][$key] == 'price' || $fields[$fieldAttr][$key] == 'special_price'){
                $this->_addPrice[] = 'min_price';
            }
            
            $this->_joinCustomAttribute($fields[$fieldAttr][$key], $storeId);
//            $attr[] = $fields[$fieldAttr][$key];
//            $this->joinAttribute($fields[$fieldAttr][$key], 'catalog_product/' . $fields[$fieldAttr][$key], 'entity_id', null, 'left', $storeId);
        }
    }
    
    public function parseFields($fields, $storeId)
    {
        $attr = array();
        $notJoin = array('entity_id', 'sku', 'created_at');
        $types = $fields['type'];
        foreach ($types as $key => $type) {
            switch ($type) {
                case 'attribute':
                    $this->_parseAttributeField($fields, $key, $storeId, 'attr', $attr, $notJoin);
                    break;
                case 'custom_field':
                    $field = Mage::getModel('amfeed/profile')->getCustomField($fields['custom'][$key]);
                    // parse `Default value`
                    //$defVal = $field->getDefaultValue();
                    if ('qty' == $field->getBaseAttr()) {
                        $this->_addQty = true;
                    } elseif ('parent_id' == $field->getBaseAttr()) {
                        $this->_addParentId = true;
                    } elseif ('url' == $field->getBaseAttr()) {
                        $this->_addUrl = true;
                    } elseif (in_array($field->getBaseAttr(), array('final_price', 'min_price', 'tier_price'))) {
                        $this->_addPrice[] = $field->getBaseAttr();
                    } elseif ('tax_percents' == $field->getBaseAttr()) {
                        $this->_addTax = true;
                    } elseif ('is_in_stock' == $field->getBaseAttr()) {
                        $this->_addIsInStock = true;
                    } elseif (in_array($field->getBaseAttr(), array('category_id', 'category_name', 'categories'))) {
                        $this->_addCategory = true;
                    } elseif ('stock_availability' == $field->getBaseAttr()) {
                        $this->_addStockAvailability = true;
                    } else if ('sale_price_effective_date' == $field->getBaseAttr()){
                        
                        $this->_joinCustomAttribute('special_from_date', $storeId);
//                        
//                        if (!in_array($field->getBaseAttr(), 'special_from_date')){
//                            $attr[] = 'special_from_date';
//                            $this->joinAttribute('special_from_date', 'catalog_product/special_from_date', 'entity_id', null, 'left', $storeId);
//                        }
                        $this->_joinCustomAttribute('special_to_date', $storeId);
//                        if (!in_array($field->getBaseAttr(), 'special_to_date')){
//                            $attr[] = 'special_to_date';
//                            $this->joinAttribute('special_to_date', 'catalog_product/special_to_date', 'entity_id', null, 'left', $storeId);
//                        }
                        
                    } elseif ($field->getBaseAttr() && ('created_at' !== $field->getBaseAttr())) {
                        
                        $this->_joinCustomAttribute($field->getBaseAttr(), $storeId);
                        
//                        $attr[] = $field->getBaseAttr();
//                        $this->joinAttribute($field->getBaseAttr(), 'catalog_product/' . $field->getBaseAttr(), 'entity_id', null, 'left', $storeId);
                    } elseif (!$field->getBaseAttr()){
                        
                        $regex = "#{(.*?)}#";
        
                        preg_match_all($regex, $field->getDefaultValue(), $mathes);
                        
                        $attributes = $mathes[1];
                        
                        foreach($attributes as $placeholder){
                            $attribute = Mage::helper('amfeed')->getCustomFieldAttribute($placeholder);
                            
                            $attributeObj = $this->getAttribute($attribute);
                            if ($attributeObj){
//                                $attr[] = $attribute;
                                $this->_joinCustomAttribute($attribute, $storeId);
//                                $this->joinAttribute($attribute, 'catalog_product/' . $attribute, 'entity_id', null, 'left', $storeId);
                            }
                        }
                    }
                    
                    break;
            }
        }
        
    }
    
    public function addBaseFilters($storeId, $disabled, $stock, $prodTypes)
    {
        $this->addStoreFilter($storeId);
        // exclude disabled products
        if ($disabled) {
            $this->addAttributeToSelect('status');
            $this->addFieldToFilter('status', array('eq' => '1'));
        }
        // exclude `Out of Stock` products
        if ($stock) {
            $this->joinField('is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '({{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock = 0) OR {{table}}.is_in_stock=1',
                'inner');
        } elseif ($this->_addIsInStock) {
            $this->joinField('is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                null,
                'left');
        }
        // product types filter
        $where = '';
        foreach ($prodTypes as $prodType) {
            if (0 == strlen($where)) {
                $where .= '( e.type_id = \''.$prodType.'\' )';
            } else {
                $where .= ' or ( e.type_id = \''.$prodType.'\' )';
            }
        }
        $this->getSelect()->where($where);
        $this->getSelect()->group('e.entity_id');
    }
    
    public function parseAndAddAdvancedFilters($conds)
    {
        if ($conds['attr']) {
            foreach ($conds['attr'] as $key => $attribute) {
                if (in_array($conds['op'][$key], array('like', 'nlike'))) {
                    $conds['val'][$key] = '%' . $conds['val'][$key] . '%';
                }
                if ('qty' == $attribute) {
                    $this->_addQty = true;
                    $this->_qtyConds['qty'][] = array($conds['op'][$key] => explode(',', $conds['val'][$key]));
                } elseif (in_array($attribute, array('category_id', 'category_name'))) {
                    $this->_addCategory = true;
                    $this->_catConds[$attribute][] = array($conds['op'][$key] => explode(',', $conds['val'][$key]));
                } elseif (in_array($attribute, array('final_price', 'min_price', 'tier_price'))) {
                    $this->_addPrice = true;
                    $this->_priceConds[$attribute][] = array($conds['op'][$key] => explode(',', $conds['val'][$key]));
                } elseif ('tax_percents' == $attribute) {
                    $this->_addTax = true;
                    $this->_taxConds[$attribute][] = array($conds['op'][$key] => explode(',', $conds['val'][$key]));
                } else {
                    
                    $attributeObj = Mage::getResourceModel('catalog/product')
                        ->getAttribute($attribute);
                    
                    if ($attributeObj->getFrontendInput() == 'select'){
                        $options = $attributeObj->getSource()->getOptionArray();
                        
                        if (in_array($conds['val'][$key], $options)){
                            $ind = array_search($conds['val'][$key], $options);
                            $conds['val'][$key] = $ind;
                            
                        }
                        
//                        $conds['val'][$key] = $attributeObj->getSource()->getOptionId();
//                        var_dump($attribute, );
//                    exit(1);
                    }
                    
                    $this->addFieldToFilter($attribute, array($conds['op'][$key] => explode(',', $conds['val'][$key])));
                }
            }
        }
    }
    
    public function addUrlToSelect($storeId, $useCategory)
    {
//        if (Mage::getStoreConfig('amfeed/system/parent_url') && $parentUrl) {
//            $cols = array();
//            if ($this->_addParentId) {
//                $cols = array('parent_id' => 'relation_table_p_url.parent_id');
//                $this->_addParentId = false;
//            }
//            $this->getSelect()
//                 ->joinLeft(array('relation_table_p_url' => $this->getTable('catalog/product_relation')),
//                            'relation_table_p_url.child_id = e.entity_id',
//                            $cols);
//            $this->getSelect()
//                 ->joinLeft(array('url_table' => $this->getTable('core/url_rewrite')),
//                            '(url_table.product_id = IFNULL(relation_table_p_url.parent_id, e.entity_id)) AND (url_table.store_id = \'' . $storeId . '\' and url_table.category_id = category_id)',
//                            array('url' => 'CONCAT(\'' . Mage::getBaseUrl() . '\', request_path)'));
//        } else {
        
            $this->joinField('url',
                             'core/url_rewrite',
                             'CONCAT(\'' . Mage::getBaseUrl() . '\', IF(url_rewrite_id IS NULL, CONCAT(\'catalog/product/view/id/\', e.entity_id), request_path))',
//                             'CONCAT(\'' . Mage::getBaseUrl() . '\', request_path)',
                             'product_id=entity_id',
                             '{{table}}.store_id = \'' . $storeId . '\' and {{table}}.category_id  ' . ($useCategory ? '=category_id' : 'is null') . '',
                             'left');
//        }
    }
    
    function addStockAvailabilitySelect(){
        $this->addAttributeToSelect($attribute);
    }
    
    public function addParentIdToSelect()
    {
        $this->getSelect()
             ->joinLeft(array('relation_table' => $this->getTable('catalog/product_relation')),
                        'relation_table.child_id = e.entity_id',
                        array('parent_id' => 'relation_table.parent_id'));
    }
    
    public function addPriceToSelect($storeId)
    {
        $joinType = 'left';
        $joinConds = '';
        
        if ($this->_priceConds) {
            $joinType = 'inner';
            foreach ($this->_priceConds as $code => $arrayConds) {
                foreach ($arrayConds as $op => $val) {
                    $joinConds .= ' AND ' . $this->_getConditionSql('{{table}}.' . $code, array($op => $val));
                }
            }
        }
        $joinFields = array();
        
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (!empty($this->_addPrice)) {
            foreach($this->_addPrice as $field){
                $joinFields[] = 'price_tbl.'.$field;
            }
        } else {
            $joinFields['tax_class_id'] = 'price_tbl.tax_class_id';
        }
        
        if (is_null($customerGroupId)) {
            $customerGroupId = 0;
        }
                        
        $this->joinTable(array('price_tbl' => 'catalog/product_index_price'),
            'entity_id=entity_id',
            $joinFields,
            '{{table}}.website_id = \'' . $websiteId . '\' and {{table}}.customer_group_id = \'' . $customerGroupId . '\'' . $joinConds,
            $joinType);
    }
    
    public function addTaxPercentsToSelect($storeId)
    {
        if (empty($this->_addPrice) && empty($this->_priceConds)) {
            $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if (is_null($customerGroupId)) {
                $customerGroupId = 0;
            }
            $this->joinTable('catalog/product_index_price',
                'entity_id=entity_id',
                array('tax_class_id'),
                '{{table}}.website_id = \'' . $websiteId . '\' and {{table}}.customer_group_id = \'' . $customerGroupId . '\'',
                'left');
        }
        
        $this->getSelect()
             ->joinLeft(array('tax_table' => $this->getTable('tax/tax_calculation')),
                        'tax_table.product_tax_class_id = ' . $this->getTable('catalog/product_index_price') . '.tax_class_id',
                        array())
             ->joinLeft(array('rate_table' => $this->getTable('tax/tax_calculation_rate')),
                        'rate_table.tax_calculation_rate_id = tax_table.tax_calculation_rate_id',
                        array('tax_percents' => 'rate_table.rate'));
        $joinConds = array();
        if ($this->_taxConds) {
            foreach ($this->_taxConds as $code => $arrayConds) {
                foreach ($arrayConds as $op => $val) {
                    $joinConds[] = ' AND ' . $this->_getConditionSql('rate_table.rate', array($op => $val));
                }
            }
        }
        $where = array_merge($this->getSelect()->getPart(Zend_Db_Select::WHERE), $joinConds);
        $this->getSelect()->setPart(Zend_Db_Select::WHERE, $where);
    }
    
    public function addQtyToSelect()
    {
        if (empty($this->_qtyConds)) {
            $this->_qtyConds = null;
            $joinType = 'left';
        } else {
            $joinType = 'inner';
        }
        
        
        $this->getSelect()->join(
                array('am_stock_item' => $this->getTable('cataloginventory/stock_item')), 
                'am_stock_item.product_id=e.entity_id', 
                array('qty', 'IF(am_stock_item.qty = 0, "Out of Stock", "In Stock") as stock_availability'),
                $this->_qtyConds, 
                $joinType);

        
//        $this->joinTable(array('am_stock_item' => 'cataloginventory/stock_item'), 
//                'product_id=entity_id', 
//                array('qty', 'IF(am_stock_item.qty = 0, "Out of Stock", "In Stock") as stock_availability'),
//                $this->_qtyConds, 
//                $joinType);
        
        
        

        if (count($this->_qtyConds['qty']) > 1) {
            $from = $this->getSelect()->getPart(Zend_Db_Select::FROM);
            $temp = $from['_table_qty']['joinCondition'];
            $from['_table_qty']['joinCondition'] = str_replace(' or ', ' and ', $temp);
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $from);
        }
    }
    
    public function addCategoryToSelect($storeId)
    {
        if ($this->_catConds) {
            $joinConds = array();
        
        $this->getSelect()
             ->joinLeft(array('cat_prod' => $this->getTable('catalog/category_product')),
                        'e.entity_id = cat_prod.product_id', array('cat_prod_product_id' => 'cat_prod.product_id'));
        
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $this->getSelect()
                ->joinLeft(array('cat_flat' => $this->getTable('catalog/category_flat'). '_store_'.$storeId ),
                'cat_flat.entity_id = cat_prod.category_id', array(

                )
            );


                foreach ($this->_catConds as $code => $arrayConds) {
                    foreach ($arrayConds as $op => $val)
                        if ('category_id' == $code) {
                            $joinConds[] = ' AND ' . $this->_getConditionSql('cat_flat.entity_id', array($op => $val));
                        } else {
                            $joinConds[] = ' AND ' . $this->_getConditionSql('cat_flat.name', array($op => $val));
                        }
                }

        } else {
        
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_category', 'name');
            
            $this->getSelect()
                ->joinLeft(array('cat_varchar' => $this->getTable('catalog_category_entity_varchar')),
                           '(cat_varchar.entity_id = cat_prod.category_id) AND ' .
                           '(cat_varchar.store_id = ' . $storeId . ') AND ' .
                           '(cat_varchar.attribute_id = ' . $attributeId . ') ', array('cat_varchar_entity_id' => 'cat_varchar.entity_id' ))
                ->joinLeft(array('cat_varchar_def' => $this->getTable('catalog_category_entity_varchar')),
                           '(cat_varchar_def.entity_id = cat_prod.category_id) AND ' .
                           '(cat_varchar_def.store_id = 0) AND ' .
                           '(cat_varchar_def.attribute_id = ' . $attributeId . ') ',
                           array(
                           ));




            foreach ($this->_catConds as $code => $arrayConds) {
                foreach ($arrayConds as $op => $val)
                    if ('category_id' == $code) {
                        $joinConds[] = ' AND ' . $this->_getConditionSql('IFNULL(cat_varchar.entity_id, cat_varchar_def.entity_id)', array($op => $val));
                    } else {
                        $joinConds[] = ' AND ' . $this->_getConditionSql('IFNULL(cat_varchar.value, cat_varchar_def.value)', array($op => $val));
                    }
            }

        }

        $where = array_merge($this->getSelect()->getPart(Zend_Db_Select::WHERE), $joinConds);
        $this->getSelect()->setPart(Zend_Db_Select::WHERE, $where);
    }
    }
    
    public function getCountProducts()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        
        $total = $this->getConnection()->fetchOne($countSelect);
        return intval($total);
    }
    
    public function initByFeed($feed)
    {
        if ($this->isEnabledFlat()){
            $this->addAttributeToSelect(array('sku'));
        }
        
        if (($feed->getType() == Amasty_Feed_Model_Profile::TYPE_CSV) || ($feed->getType() == Amasty_Feed_Model_Profile::TYPE_TXT)) {
            $fields = unserialize($feed->getCsv());
        }
        
        if ($feed->getType() == Amasty_Feed_Model_Profile::TYPE_XML) {
            $feedXML = Mage::helper('amfeed')->parseXml($feed->getXmlBody());
            
            $fields = $feedXML['fields'];
        }
        
        $this->parseFields($fields, $feed->getStoreId());
        // base filters
        $this->addBaseFilters($feed->getStoreId(), $feed->getCondDisabled(), $feed->getCondStock(), explode(',', $feed->getCondType()));
        
        // advanced filters
        $this->parseAndAddAdvancedFilters(unserialize($feed->getCondAdvanced()));
        
        // unusual fields and filters
        if ($this->_addUrl) { // add url
            $this->addUrlToSelect($feed->getStoreId(), $feed->getFrmDontUseCategoryInUrl() == 0);
        }
        
//        if ($this->_addParentId) { // add parent id for simple products, which are children of configurable products
            $this->addParentIdToSelect();
//        }
        
        if (!empty($this->_addPrice) || !empty($this->_priceConds)) { // add price
            $this->addPriceToSelect($feed->getStoreId());
        }
        
        if ($this->_addTax) { // add tax percents
            $this->addTaxPercentsToSelect($feed->getStoreId());
        }
        
        if ($this->_addQty || $this->_addStockAvailability) { // add qty
            $this->addQtyToSelect();
        }
        
        if ($this->_addCategory) { // add category
            $this->addCategoryToSelect($feed->getStoreId());
        }
//        print $this->getSelect();
//        exit(1);
        return $this;
    }
}