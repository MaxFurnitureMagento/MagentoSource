<?php
/*
 * Class that defines a renderer for the "commercesciences/about_the_bar" section on the CS tab
 */
class Commercesciences_Base_Block_Adminhtml_Config_Form_Renderer_Group_About extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_csHelper = null;

    /**
     * retrieve the module helper
     * @return Commercesciences_Base_Helper_Data
     */
    protected function getCsHelper(){
        if(!$this->_csHelper){
            $this->_csHelper = Mage::helper("commercesciences_base");
        }
        return $this->_csHelper;
    }

    /*
     * renders and returns the full HTML of the lower part (about) on the configurations panel
     * we don't show the about section if the step is not zero
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $step = $this->getCsHelper()->getStep();
        if($step != Commercesciences_Base_Helper_Data::STEP_ZERO){
            return '';
        }

        return parent::render($element);
    }
}