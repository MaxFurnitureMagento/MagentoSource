<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */   
class Amasty_Notfound_Block_Adminhtml_Error extends Amasty_Notfound_Block_Adminhtml_Abstract
{
    public function __construct()
    {
        $this->_header    = 'System Errors';
        $this->_modelName = 'error';
        parent::__construct();
    }
}