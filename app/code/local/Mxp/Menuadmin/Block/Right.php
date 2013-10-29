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

class Mxp_Menuadmin_Block_Right extends Mxp_Menuadmin_Block_Abstract
{
    protected function _toHtml()
    {
    	if(Mage::getStoreConfigFlag('menuadmin/right/enabled')){
            return parent::_toHtml();
    	}
        return '';
    }

 	public function hasTitle(){
    	$title = Mage::getStoreConfig("menuadmin/right/titleblock");
    	if(!empty($title)){
    		return $title;
    	}
    	return false;
    }

 	public function getTitle(){
    	return Mage::getStoreConfig("menuadmin/right/titleblock");
    }

 	public function hasHome(){
    	return Mage::getStoreConfig("menuadmin/right/homemenu");
    }

}