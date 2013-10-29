<?php
/*
 * Class that defines a renderer for the "commercesciences/required_param/is_active" group on the CS tab
 */
class Commercesciences_Base_Block_Adminhtml_Config_Form_Renderer_Isactive extends Mage_Adminhtml_Block_System_Config_Form_Field
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
     * renders and returns the full HTML of the is_active part on the configurations panel
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element){
        try{

            $step = $this->getCsHelper()->getStep();
            if($step == Commercesciences_Base_Helper_Data::STEP_ZERO || $step == Commercesciences_Base_Helper_Data::STEP_ONE){
                return '';
            }

            $activeState = $this->getCsHelper()->getActiveState();
            if($activeState['error'] != false){
                throw new Exception($activeState['error']);
            }
            $visible = -1;
            if($activeState['data']=='Hidden'){
                $visible = 0;
            }elseif($activeState['data']=='Visible'){
                $visible = 1;
            }else{
                throw new Exception($this->__("Error ocurred. Your updates weren't saved. Please contact ComemrceScience for support (error id: 005)"));
            }

            $element->setValue($visible);
            $selectBox = $element->getElementHtml();

            $configEditBlock = Mage::getBlockSingleton("adminhtml/system_config_edit");
            /* @var Mage_Adminhtml_Block_Widget_Button $saveButton */
            $saveButton = $configEditBlock->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Save Config'),
                'onclick'   => 'configForm.submit()',
                'class' => 'save',
            ));

            if($step == Commercesciences_Base_Helper_Data::STEP_ONE){
                $html = '<div class="cswrapper form-list">';
                $html .= '<div class="csexp12"><div class="csexp12_left">Enabled?</div><div class="csexp12_right">'.$selectBox.$saveButton->toHtml().'</div></div>';
                $html .= '</div></td></tr>';

                return $html;
            }
            if($step == Commercesciences_Base_Helper_Data::STEP_TWO){
                $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
                $html = '<div class="cswrapper form-list">';
                $html .= '<div class="csexp24"><div class="csexp24_left">Is Live on my store?</div><div class="csexp24_right">'.$selectBox.$saveButton->toHtml().'</div></div>';
                $html .= '<div class="csexp25"><div class="csexp25_left">'.$this->__("Have questions? contact our").'</div> <a class="csexp25_left2" target="_blank" href="' . $csConfig->getCsUrl() . '/contact">'.$this->__("support team").'</a></div>';
                $html .= '</div></td></tr>';

                return $html;
            }
        }catch(Exception $e){
            return '';
        }

    }
}
