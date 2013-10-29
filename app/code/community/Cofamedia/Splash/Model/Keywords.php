<?php
class Cofamedia_Splash_Model_Keywords extends Varien_Object
{
    public function toOptionArray()
    {
        return array(
            array('value'=>false, 'label'=>Mage::helper('splash')->__('No')),
            array('value'=>true, 'label'=>Mage::helper('splash')->__('Yes')),
        );
    }
}