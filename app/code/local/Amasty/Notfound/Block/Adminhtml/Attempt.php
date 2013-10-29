<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */   
class Amasty_Notfound_Block_Adminhtml_Attempt extends Amasty_Notfound_Block_Adminhtml_Abstract
{
    public function __construct()
    {
        $this->_header    = 'Failed Login Attempts';
        $this->_modelName = 'attempt';
        parent::__construct();
    }
}