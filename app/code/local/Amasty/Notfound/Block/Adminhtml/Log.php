<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */   
class Amasty_Notfound_Block_Adminhtml_Log extends Amasty_Notfound_Block_Adminhtml_Abstract
{
    public function __construct()
    {
        $this->_header    = 'Not Found Pages';
        $this->_modelName = 'log';
        parent::__construct();
    }
}