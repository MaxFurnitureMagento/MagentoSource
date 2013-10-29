<?php
class Cofamedia_Splash_Model_Buttons
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'thumbnails', 'label'=>Mage::helper('splash')->__('Thumbnails')),
            array('value'=>'numbers', 'label'=>Mage::helper('splash')->__('Numbers')),
            array('value'=>'letters', 'label'=>Mage::helper('splash')->__('Letters')),
        );
    }

}
