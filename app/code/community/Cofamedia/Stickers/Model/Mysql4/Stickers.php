<?php
class Cofamedia_Stickers_Model_Mysql4_Stickers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
      $this->_init('stickers/stickers', 'stickers_id');
    }
    
    public function checkIdentifier($stickers_id)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'stickers_id')
            ->where('main_table.stickers_id=?', $stickers_id)
            ;

        return $this->_getReadAdapter()->fetchOne($select);
    }    
}