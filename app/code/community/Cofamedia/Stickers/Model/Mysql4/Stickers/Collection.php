<?php
class Cofamedia_Stickers_Model_Mysql4_Stickers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
    protected $_previewFlag;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('stickers/stickers');
        
        $this->_map['fields']['stickers_id'] = 'main_table.stickers_id';
    }
    
    
		public function addAttributeToFilter($attribute, $condition, $value)
    {
      $this->getSelect()->where("main_table.$attribute $condition $value");

      return $this;
    }
}