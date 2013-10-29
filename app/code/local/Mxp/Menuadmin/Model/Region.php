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

class Mxp_Menuadmin_Model_Region extends Varien_Object
{
    const REGION_TOP	= 'top';
    const REGION_LEFT	= 'left';
    const REGION_RIGHT	= 'right';
    const REGION_BOTTOM	= 'bottom';

    static public function getOptionArray()
    {
        return array(
            self::REGION_TOP    => Mage::helper('menuadmin')->__('Top'),
            self::REGION_LEFT   => Mage::helper('menuadmin')->__('Left'),
            self::REGION_RIGHT   => Mage::helper('menuadmin')->__('Right'),
            self::REGION_BOTTOM   => Mage::helper('menuadmin')->__('Bottom')
        );
    }
}