<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form select element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Solrsearchboostvalue extends Varien_Data_Form_Element_Abstract
{
    /**
     * Init Element
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
    	parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('select');
    }

    /**
     * Retrieve allow attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array('type', 'name', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled');
    }

    /**
     * Prepare value list
     *
     * @return array
     */
    protected function _prepareValues() {
        $options = array();
        $values  = array();
        //print_r($this->getOptions());
		//die(print_r($this->getValues()));
        if ($this->getValues()) {
            if (!is_array($this->getValues())) {
                $options = array($this->getValues());
            }
            else {
                $options = $this->getValues();
            }
        }
        elseif ($this->getOptions() && is_array($this->getOptions())) {
            $options = $this->getOptions();
        }
        foreach ($options as $k => $v) {
            if (is_string($v)) {
                $values[] = array(
                    'label' => $v,
                    'value' => $k
                );
            }
            elseif (isset($v['value'])) {
                if (!isset($v['label'])) {
                    $v['label'] = $v['value'];
                }
                $values[] = array(
                    'label' => $v['label'],
                    'value' => $v['value']
                );
            }
        }
        //die(print_r($values));
        return $this->getValues();
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();
		//die (print_r($values));
        if (!$values) {
            return '';
        }
		
        $html  = '<ul class="checkboxes">';
        foreach ($values as $k=>$value) {
            $html.= $this->_optionToHtml($k,$value);
        }
        $html .= '</ul>';
            ///. $this->getAfterElementHtml();

        return $html;
    }

    public function getChecked($value)
    {
    	if ($checked = $this->getValue()) {
        }
        elseif ($checked = $this->getData('checked')) {
        }
        else {
            return ;
        }
        if (!is_array($checked)) {
            $checked = array(strval($checked));
        }
        else {
            foreach ($checked as $k => $v) {
                $checked[$k] = strval($v);
            }
        }
        
        if (in_array(strval($value), explode(",",$checked[0]))) {
            return 'checked';
        }
        return ;
    }

    public function getDisabled($value)
    {
        if ($disabled = $this->getData('disabled')) {
            if (!is_array($disabled)) {
                $disabled = array(strval($disabled));
            }
            else {
                foreach ($disabled as $k => $v) {
                    $disabled[$k] = strval($v);
                }
            }
            if (in_array(strval($value), $disabled)) {
                return 'disabled';
            }
        }
        return ;
    }

    public function getOnclick($value)
    {
        if ($onclick = $this->getData('onclick')) {
            return str_replace('$value', $value, $onclick);
        }
        return ;
    }

    public function getOnchange($value)
    {
        if ($onchange = $this->getData('onchange')) {
            return str_replace('$value', $value, $onchange);
        }
        return ;
    }

//    public function getName($value)
//    {
//        if ($name = $this->getData('name')) {
//            return str_replace('$value', $value, $name);
//        }
//        return ;
//    }

    protected function _optionToHtml($k,$option)
    {
    	
    	$boostScrores = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20);
    	
    	$boostFields = Mage::getStoreConfig('webmods_solrsearch_boost_value/settings/enabled_fields', 0);
    	$boostFieldsArray = explode(',',$boostFields); 

    	$selecteBoostFieldsArray = array();
    	foreach ($boostFieldsArray as $item){
    		$tempArray = explode(':', $item);
    		$selecteBoostFieldsArray[$tempArray[0]][] = $tempArray[1];
    	}
    	//die(print_r($selecteBoostFieldsArray));
    	$id = $this->getHtmlId().'_'.$this->_escape($option['value']);
		//die($id);
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $k)->getData();
		
		if ($attribute['frontend_input'] == 'text'){
			
			$readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
			//$select = $read->select();
			//$select->columns('DISTINCT(`value`) as `value`');
			//$select->from('catalog_product_entity_varchar')->where('attribute_id = ?', $attribute['attribute_id']);
			
			$sqlQuery = "SELECT DISTINCT(`value`) as `value` FROM catalog_product_entity_varchar WHERE `attribute_id` = '".$attribute['attribute_id']."'";
			
			$results = $readConnection->fetchAll($sqlQuery);
			$option = array();
			foreach($results as $val){
				$option[] = $val['value'];
			}	
			
			$optionString = '';
			$optionValue = '';
			if (isset($selecteBoostFieldsArray[$attribute['attribute_code'].'_varchar']) && isset($selecteBoostFieldsArray[$attribute['attribute_code'].'_varchar'][0])){
			$optionString = $selecteBoostFieldsArray[$attribute['attribute_code'].'_varchar'][0];
			$optionValue = $attribute['attribute_code'].'_varchar:'.$optionString;
			}
			
			return '<li><b style="font-size:18px">'.$attribute['frontend_label'].'</b><br /><textarea id="'.$k.'_varchar'.'" onblur="$(\''.$id.$k.'\').value = \''.$k.'_varchar'.':\'+$(this).value">'.$optionString.'</textarea><div>Example: "Dell"^100 "Samsung"^80 "Acer"60</div><input type="hidden" id="'.$id.$k.'" value="'.$optionValue.'" name="groups[settings][fields][enabled_fields][value][]" /><li>';
		}
		
		//die(print_r($attribute->getData()));
        $html = '<li><b style="font-size:18px">'.$attribute['frontend_label'].'</b><br />';
        foreach ($option as $index=>$value){
        	if (!empty($value)){
        		$html .= $value.'<select style="width:100%" id="'.$id.$k.'" name="groups[settings][fields][enabled_fields][value][]">';
				$html .= '<option value="">Default</option>';
        		foreach ($boostScrores as $scoreValue){
        			$selected = "";
        			foreach ($selecteBoostFieldsArray[$k.'_text'] as $val){
        				//echo $val.'=='.$k.':'.$value.'^'.$scoreValue;
        				if($val == $value.'^'.$scoreValue){
        					$selected = 'selected="selected"';
        					break;
        				}
        			}
        			$html .= '<option '.$selected.' value="'.$k.'_text'.':'.$value.'^'.$scoreValue.'">'.$scoreValue.'</option>';
        		}        		
        		$html .= '</select>';        		
        	}
        }
        $html .= '</li>';
        /*
        foreach ($this->getHtmlAttributes() as $attribute) {
            if ($value = $this->getDataUsingMethod($attribute, $option['value'])) {
                if($attribute == "name"){
                	$html .= ' '.$attribute.'="'.$value.'[]"';
                }else{
            		$html .= ' '.$attribute.'="'.$value.'"';
                }
            }
        }
        $html .= ' value="'.$option['value'].'" />'
            . ' <label for="'.$id.'">' . $option['label'] . '</label></li>'
            . "\n";
        */
        return $html;
    }
}