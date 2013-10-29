<?php
/**
 * Celebros Qwiser - Magento Extension
 *
 * @category    Celebros
 * @package     Celebros_Conversionpro
 * @author		Omniscience Co. - Dan Aharon-Shalom (email: dan@omniscience.co.il)
 *
 */
class Celebros_Conversionpro_Model_System_Config_Source_Fileftp
{
    public function toOptionArray()
    {
    	return array(
            array('value' => 'file', 'label'=>Mage::helper('conversionpro')->__('File')),
            array('value' => 'ftp', 'label'=>Mage::helper('conversionpro')->__('FTP')),
        );
    }
}