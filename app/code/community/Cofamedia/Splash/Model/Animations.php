<?php
class Cofamedia_Splash_Model_Animations
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('splash')->__('Fade')),
            array('value'=>'slide-left', 'label'=>Mage::helper('splash')->__('Slide to the left')),
            array('value'=>'slide-right', 'label'=>Mage::helper('splash')->__('Slide to the right')),
            array('value'=>'slide-top', 'label'=>Mage::helper('splash')->__('Slide to the top')),
            array('value'=>'slide-bottom', 'label'=>Mage::helper('splash')->__('Slide to the bottom')),
        );
    }

}
