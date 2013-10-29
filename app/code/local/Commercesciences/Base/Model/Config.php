<?php
/*
 * Class that contains only standard construction call, for initializing the Model Object that implements calls to DB via the coresponding Mysql4 object
*/
class Commercesciences_Base_Model_Config extends Mage_Core_Model_Abstract
{
    public function _construct ()
    {
        parent::_construct();
        $this->_init('commercesciences_base/config');
    }

}