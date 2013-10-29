<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Notfound_Model_Abstract extends Mage_Core_Model_Abstract
{
    protected $modelName = 'log';
    
    public function _construct()
    {    
        $this->_init('amnotfound/' . $this->modelName);
    }
    
    public function clear()
    {
        return $this->getResource()->clear();
    }
    
    public function getCountFrom($lastRun=0)
    {
        $this->collect($lastRun);
        $collection = $this->getCollection()
            ->addFieldToFilter('date', array('gt' => date('Y-m-d H:i:s', $lastRun)));
        return $collection->count();        
    }
    
    public function collect($lastRun=0)
    {
        return $this->getResource()->collect($lastRun);
    }    
}