<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com) 
 */
class Amasty_Notfound_Model_Mysql4_Error_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amnotfound/error');
    }
}