<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ambanners/rule', 'rule_id');
    }

    public function massChangeStatus($ids, $status)
    {
        $db = $this->_getWriteAdapter();
        $db->update($this->getMainTable(),
            array('is_active' => $status), 'rule_id IN(' . implode(',', $ids) . ') ');
            
        return true;
    }    
    
    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $read = $this->_getReadAdapter();
        $tbl   = $this->getTable('ambanners/attribute');
        
        $select = $read->select()->from($tbl, new Zend_Db_Expr('DISTINCT code'));
        return $read->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $id rule id
     * @param mixed $attributes
     * return Amasty_Shiprestriction_Model_Mysql4_Rule
     */
     
    public function saveAttributes($id, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $tbl   = $this->getTable('ambanners/attribute');
        
        $write->delete($tbl, array('rule_id=?' => $id));
        
        $data = array();
        foreach ($attributes as $code){
            $data[] = array(
                'rule_id' => $id,
                'code'    => $code,
            );
        }
        $write->insertMultiple($tbl, $data);
        
        return $this;
    }       

    public function getProducts($ruleId)
    {
        $db = $this->_getReadAdapter(); 

        $sql = $db->select()
            ->from($this->getTable('ambanners/rule_products'), 'product_id')
            ->where('rule_id = ?', $ruleId);
        
        return $db->fetchCol($sql);      
    }    

    public function assignProducts($rule_id, $productIds)
    {
        $db = $this->_getWriteAdapter();
        
        $rule_id = intVal($rule_id);
        $db->delete($this->getTable('ambanners/rule_products'), "rule_id=$rule_id"); 
        
        if (!$productIds)
            return false;
            
        $sql = 'INSERT INTO `' . $this->getTable('ambanners/rule_products') . '` (`rule_id`, `product_id`) VALUES ';
        foreach ($productIds as $id) {
            $id  = intVal($id);
            $sql .= "($rule_id , $id),";
        }
        $db->raw_query(substr($sql, 0, -1));
        
        return true;
    }       
}