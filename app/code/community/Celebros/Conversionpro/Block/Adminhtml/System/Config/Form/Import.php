<?php
class Celebros_Conversionpro_Block_Adminhtml_System_Config_Form_Import extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('conversionpro/system/config/import.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return Mage::helper('adminhtml')->getUrl('conversionpro/adminhtml_mapping/importSettings');
    }
 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'conversionpro_import',
            'label'     => $this->helper('adminhtml')->__('Import Conversion Pro Settings'),
            'onclick'   => 'javascript:conversionproImport(); return false;'
		));
 
        return $button->toHtml();
    }
}