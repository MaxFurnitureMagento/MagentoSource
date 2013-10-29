<?php
/**
* @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com) 
*/  
class Amasty_Notfound_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amnotfound/log', 'log_id');
    }
    
    public function clear()
    {    
        $this->_getWriteAdapter()->raw_query('truncate table ' . $this->getMainTable());
    }
    
    public function collect($lastRun)
    {    
        return true;
    }    
}