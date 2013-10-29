<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

class Mxp_Menuadmin_Model_Mysql4_Menuadmin extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('menuadmin/menuadmin', 'menuadmin_id');
    }
}