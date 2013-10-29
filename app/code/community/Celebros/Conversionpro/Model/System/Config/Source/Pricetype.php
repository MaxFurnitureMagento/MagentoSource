<?php
class Celebros_Conversionpro_Model_System_Config_Source_Pricetype
{
	public function toOptionArray()
    {
    	return array(
            array('value' => 'slider', 'label'=>Mage::helper('conversionpro')->__('Slider')),
            array('value' => 'textual', 'label'=>Mage::helper('conversionpro')->__('Textual')),
        );
    }
}