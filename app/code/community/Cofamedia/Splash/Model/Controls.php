<?php
class Cofamedia_Splash_Model_Controls
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'progress', 'label'=>Mage::helper('splash')->__('Progress')),
            array('value'=>'pause', 'label'=>Mage::helper('splash')->__('Pause')),
        );
    }

}
