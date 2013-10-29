<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Field extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amfeed/field');
    }
}