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

class Mxp_Menuadmin_Block_Menuadmin extends Mxp_Menuadmin_Block_Abstract
{


    protected function _toHtml()
    {
    	if(Mage::getStoreConfigFlag('menuadmin/general/enabled')){
            return parent::_toHtml();
    	}
        return '';
    }
}