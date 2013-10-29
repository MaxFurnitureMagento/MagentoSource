<?php
/*
 * Class that defines a renderer for the "commercesciences/about_the_bar/email" section on the CS tab
 */
class Commercesciences_Base_Block_Adminhtml_Config_Form_Renderer_About extends Mage_Adminhtml_Block_System_Config_Form_Field
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

    /**
     * renderer for the child of the "About" Group (the child name is also "About")
     * @see Commercesciences_Base_Block_Adminhtml_Config_Form_Renderer_Group_About
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element){
        $html = '	<div class="cs_wrap">
                        <div id="main" style="">

                        <div class="cs_key-text-block">
                            <div class="cs_container">
                                <div class="cs_row">
									<h3>The Personal Bar<span class="cs_sans-serif cs_tm">™</span> Is a free E-Commerce add-on, floating at the bottom of your site\'s pages,</h3>
									<h3>designed to ease navigation, increase engagement and revenue</h3>
                                </div>
                            </div>
                        </div>

                        <div class="cs_bar-guide-block">
                            <div class="cs_bar-guide-img"></div>
                        </div>
                    </div>
                    </div>';


        return $html;
    }

}