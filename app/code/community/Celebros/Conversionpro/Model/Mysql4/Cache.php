<?php

class Celebros_Conversionpro_Model_Mysql4_Cache extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the <module>_id refers to the key field in your database table.
        $this->_init('conversionpro/cache', 'cache_id');
    }
}