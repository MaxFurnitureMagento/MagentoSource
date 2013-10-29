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

class Mxp_Menuadmin_Block_Top extends Mxp_Menuadmin_Block_Abstract
{
    protected function _toHtml()
    {
    	if(Mage::getStoreConfigFlag('menuadmin/top/enabled')){
            return parent::_toHtml();
    	}
        return '';
    }

 	public function hasHome(){
    	return Mage::getStoreConfig("menuadmin/top/homemenu");
    }

}