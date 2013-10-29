<?php
class Celebros_Conversionpro_Model_System_Config_Source_Navigationtosearch
{
    public function toOptionArray()
    {
    	return array(
    		array('value' => 'answer_id', 'label'=>Mage::helper('conversionpro')->__('Answer Ids')),
            array('value' => 'textual', 'label'=>Mage::helper('conversionpro')->__('Textual Queries'))
        );
    }
}