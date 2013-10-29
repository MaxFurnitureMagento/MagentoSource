<?php
class Cofamedia_Splash_Model_Triggers
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'hover', 'label'=>Mage::helper('splash')->__('Hover')),
            array('value'=>'click', 'label'=>Mage::helper('splash')->__('Click')),
            array('value'=>'click_stop', 'label'=>Mage::helper('splash')->__('Click & Stop')),
        );
    }

}
