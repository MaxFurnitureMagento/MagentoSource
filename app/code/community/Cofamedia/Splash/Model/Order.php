<?php
class Cofamedia_Splash_Model_Order
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'first', 'label'=>Mage::helper('splash')->__('First X')),
            array('value'=>'random', 'label'=>Mage::helper('splash')->__('Random')),
            array('value'=>'keywords', 'label'=>Mage::helper('splash')->__('By Keywords')),
        );
    }

}
