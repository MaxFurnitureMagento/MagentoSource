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

class Mxp_Menuadmin_Model_Type extends Varien_Object
{
    const TYPE_NORMAL		= 1;
    const TYPE_VERTICAL		= 2;
    const TYPE_HORIZONTAL	= 3;

    static public function getOptionArray()
    {
        return array(
            self::TYPE_NORMAL    => Mage::helper('menuadmin')->__('Normal'),
            self::TYPE_VERTICAL   => Mage::helper('menuadmin')->__('Category Inline'),
            self::TYPE_HORIZONTAL   => Mage::helper('menuadmin')->__('Category Subitem')
        );
    }
}