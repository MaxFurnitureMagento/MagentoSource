<?php
/*
 * Class that contains only standard construction call, for initializing the Mysql4 Model Object that used to run queries on the DB
*/
class Commercesciences_Base_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct ()
    {
        $this->_init('commercesciences_base/config', 'entity_id');
    }
}