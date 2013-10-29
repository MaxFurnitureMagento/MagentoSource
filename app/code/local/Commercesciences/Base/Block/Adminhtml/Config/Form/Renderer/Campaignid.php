<?php
/*
 * Class that defines a renderer for the "commercesciences/required_param/email" group on the CS tab
 */
class Commercesciences_Base_Block_Adminhtml_Config_Form_Renderer_Campaignid extends Mage_Adminhtml_Block_System_Config_Form_Field
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
     * renders and returns the full HTML of the upper part (email form) on the configurations panel
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element){
        $html = '<script type="text/javascript">
				var google_conversion_id = 1015159350;
				var google_conversion_language = "en";
				var google_conversion_format = "3";
				var google_conversion_color = "ffffff";
				var google_conversion_label = "ak4ICIKM8AIQtrSI5AM";
				var google_conversion_value = 0;
				</script>
				<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
				</script>
				<noscript>
				<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1015159350/?value=0&amp;label=ak4ICIKM8AIQtrSI5AM&amp;guid=ON&amp;script=0"/>
				</div>
				</noscript>'.
		
				'<tr><td><script type="text/javascript">
                document.observe("dom:loaded", function() {

                    $$(".scalable.save").each(function(element){
                        element.writeAttribute("onclick",null);
                        element.observe("click", function(event){
                            $$(".scalable.save").each(function(element){
                                element.writeAttribute("disabled","disabled");
                            });
                            configForm.submit();
                        });

                    });

                });
                </script>
                ';

        try{

            $step = $this->getCsHelper()->getStep();
            $csConfig = Mage::getModel("commercesciences_base/config")->load("1");

            if(!$element->getValue()){
                $currentUser = Mage::getSingleton('admin/session');
                $currentUserEmail = $currentUser->getUser()->getEmail();

                $element->setValue($currentUserEmail);
            }
            $inputBox = $element->getElementHtml();
            $configEditBlock = Mage::getBlockSingleton("adminhtml/system_config_edit");
            /* @var Mage_Adminhtml_Block_Widget_Button $saveButton */
            $saveButton = $configEditBlock->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('adminhtml')->__('Create My Account &#187;'),
                'onclick'   => 'configForm.submit()',
                'class' => 'save',
            ));
            $html .= '<style>.gotoconfig {font-size: 15px;}</style>';
            if($step == Commercesciences_Base_Helper_Data::STEP_ZERO){
                $html .= '<div class="cswrapper form-list">';
                $html .= '<div class="csexp1">'.$this->__("You're almost there! Please enter your email below to activate your account (Note: this email would be used to provide you updates and reports).").'</div>';
                $html .= '<div class="csexp2"><div class="csexp2_left">Email Address</div><div class="csexp2_right">'.$inputBox.'</div></div>';
                $html .= '<div class="csexp3"><div class="csexp3_left"></div><div class="csexp2_right"><p class="note"><span>The personal bar will be hidden from your shoppers until you click "Go Live" from your Config Panel or your Magento dashboard.</span></p>'.$saveButton->toHtml();
                $html .= '</div></div>';
                $html .= '</div></td></tr>';

                return $html;
            }
            if($step == Commercesciences_Base_Helper_Data::STEP_ONE){
                $linkToConfBar = $csConfig->getCsUrl().'/magento/configureInitial?userID='.$csConfig->getUserId().'&securityToken='.$csConfig->getSecurityToken();

                $html .= '<div class="cswrapper form-list">';
                $html .= '<div class="csexp11">';
                $html .= '<div class="csexp11_1">'.$this->__("Congratulations! Your new Personal Bar account is now ready.").'</div>';
                $html .= '<div class="csexp11_2">'.$this->__("To configure the Personal Bar Experiences, Style and more, please click on \"Go to my Config Panel\" below.").'</div>';
                $html .= '<div class="csexp11_3">'.$this->__("Your personal Bar will remain hidden from your store visitors until you Publish it.").'</div>';
                $html .= '</div>';
                $html .= '<div class="csexp11_5">'.$this->__("Personal Bar Settings").'</div>';
                $html .= '<div class="csexp11_6">'.$this->__("The Personal Bar is fully customizable to fit your store's look & feel and to help you increase engagement and revenue.").'</div>';
                $html .= '<div class="csexp11_7"><a class="gotoconfig" href="'.$linkToConfBar.'" target="_blank">'.$this->__("Go to my Config Panel&#187;").'</a></div>';

                $html .= '</div>';

                return $html;
            }
            if($step == Commercesciences_Base_Helper_Data::STEP_TWO){
                $linkToConfBar = $csConfig->getCsUrl().'/magento/configureInitial?userID='.$csConfig->getUserId().'&securityToken='.$csConfig->getSecurityToken();

                $html .= '<div class="cswrapper form-list">';
                $html .= '<div class="csexp21">'.$this->__("Personal Bar Settings").'</div>';
                $html .= '<div class="csexp22">'.$this->__("The Personal Bar is fully customizable to fit your store’s look & feel and to help you increase engagement and revenue.").'</div>';
                $html .= '<div class="csexp23"><a class="gotoconfig" href="'.$linkToConfBar.'" target="_blank">'.$this->__("Go to my Config Panel&#187;").'</a></div>';
                $html .= '</div>';

                return $html;
            }

            return '';
        }catch(Exception $e){
            return '';
        }
    }

}