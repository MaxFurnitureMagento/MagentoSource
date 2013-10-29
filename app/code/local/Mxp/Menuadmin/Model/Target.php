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

class Mxp_Menuadmin_Model_Target extends Varien_Object
{
    const TARGET_SELF	= 'self';
    const TARGET_BLANK	= '_blank';

    static public function getOptionArray()
    {
        return array(
            self::TARGET_SELF    => Mage::helper('menuadmin')->__('Self'),
            self::TARGET_BLANK   => Mage::helper('menuadmin')->__('New window')
        );
    }
}