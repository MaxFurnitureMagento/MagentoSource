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

class Mxp_Menuadmin_Block_Left extends Mxp_Menuadmin_Block_Abstract
{
    protected function _toHtml()
    {
		/* tim kiv added code not to show up left menu on check out tim.kiv@webdecade.com  www.webdecade.com*/
		if ($_SERVER['REQUEST_URI'] != "/checkout/onepage/") {
    	if(Mage::getStoreConfigFlag('menuadmin/left/enabled')){
            return parent::_toHtml();
    	}
		}
        return '';
    }

 	public function hasTitle(){
		
    	$title = Mage::getStoreConfig("menuadmin/left/titleblock");
    	if(!empty($title)){
    		return $title;
    	}
    	return false;
    }

 	public function getTitle(){
		
    	return Mage::getStoreConfig("menuadmin/left/titleblock");
    }

 	public function hasHome(){
		
		
			
    		return Mage::getStoreConfig("menuadmin/left/homemenu");
		
		
    }

}