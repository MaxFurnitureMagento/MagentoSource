<?php
class Cofamedia_Stickers_Model_Position
{
    const POSITION_TOP_LEFT = 1;
    const POSITION_TOP_RIGHT = 2;
    const POSITION_BOTTOM_RIGHT = 3;
    const POSITION_BOTTOM_LEFT = 4;

    static public function toOptionArray()
    {
        return array(
            self::POSITION_TOP_LEFT    		=> Mage::helper('stickers')->__('Top Left'),
            self::POSITION_TOP_RIGHT    	=> Mage::helper('stickers')->__('Top Right'),
            self::POSITION_BOTTOM_RIGHT   => Mage::helper('stickers')->__('Bottom Right'),
            self::POSITION_BOTTOM_LEFT    => Mage::helper('stickers')->__('Bottom Left'),
        );
    }
}