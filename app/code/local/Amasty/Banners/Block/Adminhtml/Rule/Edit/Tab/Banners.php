<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule_Edit_Tab_Banners extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Amasty_Banners_Helper_Data */
        $hlp = Mage::helper('ambanners');
        $model = Mage::registry('ambanners_rule');
        
        $fieldset = $form->addFieldset('position', array('legend'=> $hlp->__('Banner Position and Type')));
        $fieldset->addField('banner_position', 'select', array(
            'label'     => $hlp->__('Position'),
            'name'      => 'banner_position',
            'options'   => $hlp->getPosition(),
        	'note'      => $hlp->__('Where to display this banner'),
        ));
       
        $fieldset->addField('cats', 'multiselect', array(
            'label'     => $hlp->__('Categories'),
            'name'      => 'cats',
            'values'    => $this->getTree(),
            'note'      => $hlp->__('Leave empty to show in all categories'),
        ));
        
        $fieldset->addField('show_on_products', 'textarea', array(
            'label'     => $hlp->__('Product SKUs'),
            'name'      => 'show_on_products',
        	'style' 	  => 'width:500px; height:50px;',
            'note'      => $hlp->__('Comma separted list of product SKUs where this banner should be displayed. Leave field blank to display banner on all products'),
        ));
        
        $fieldset->addField('banner_type', 'select', array(
            'label'     => $hlp->__('Banner Type'),
            'title'     => $hlp->__('Banner Type'),
            'name'      => 'banner_type',
            'required'  => true,
            'options'   => $hlp->getBannerTypes(), 
        	'note'      => $hlp->__('What kind of content banner will display'),
        ));
        
        $fieldset->addField('show_products', 'select', array(
            'label'     => $hlp->__('Show Products'),
            'title'     => $hlp->__('Show Products'),
            'name'      => 'show_products',
            'required'  => true,
            'options'   => $hlp->showProductsListOptions(), 
        	'note'      => $hlp->__('Choose Yes to show selected products below banner'),
        ));
        
        
        $fieldset = $form->addFieldset('image', array('legend'=> $hlp->__('Image')));
        
		$fieldset->addField('banner_img', 'file', array(
            'label'     => $hlp->__('Image'),
            'name'      => 'banner_img',
            'after_element_html' => $this->getImageHtml($model->getBannerImg()),
        )); 
        
        $fieldset->addField('banner_link', 'text', array(
            'label'     => $hlp->__('Link'),
            'name'      => 'banner_link',
        ));
        
        $fieldset->addField('banner_title', 'text', array(
            'label'     => $hlp->__('Title'),
            'name'      => 'banner_title',
        ));
                
        $cmsBlocks = Mage::getModel('cms/block')->getCollection();
        $values = array(array(
            'value' => '',
            'label' => '',
        ));
        
        foreach ($cmsBlocks as $block) {
            $values[] = array(
                'value' => $block->getIdentifier(),
                'label' => $block->getTitle(),
            );
        }
        
        $fieldset = $form->addFieldset('cms', array('legend'=> $hlp->__('CMS Block')));
        $fieldset->addField('cms_block', 'select', array(
          'name'      => 'cms_block',
          'label'     => $hlp->__('Use CMS block'),
          'title'     => $hlp->__('Use CMS block'),
          'values'    => $values,
        ));
		
        $fieldset = $form->addFieldset('html', array('legend'=> $hlp->__('HTML Text')));
        $fieldset->addField('html_text', 'editor', array(
          'name'      => 'html_text',
          'label'     => $hlp->__('HTML Text'),
          'title'     => $hlp->__('HTML Text'),
          'style' 	  => 'width:700px; height:500px;', 
		  'wysiwyg'   => true, 
       	  'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
        ));
        
        $form->setValues($model->getData()); 
        
        $fldAttr = $form->addFieldset('attributes', array('legend'=> $hlp->__('Choose attributes of product to show banner on its page')));
        $this->prepareAttributes($fldAttr, $model); 
        
        
        return parent::_prepareForm();
    }
    
    
    /**
     * Render required attributes
     * @param Varien_Data_Form_Element_Fieldset $fldAttr
     * @param Amasty_Banners_Model_Rule $model
     */
    protected function prepareAttributes($fldAttr, $model)
    {
    	$hlp = Mage::helper('ambanners');
    	
		/*
		 * Add Empty Fields user for new conditions
		 */    	
    	$fieldSet = $this->getForm()->addFieldset('attributestmp', array('legend'=> $hlp->__('Attribute Tmp')));
        $fieldSet->addField('attr_code[][]', 'select', array(
            'label'     => $hlp->__('Has attribute'),
            'name'      => 'attr_code[]',
            'values'    => $this->getAttributes(),
            'onchange'  => 'showOptions(this)',
            )
        );
        
        $fieldSet->addField('attr_value[][]', 'text', array(
                'label'     => $hlp->__('Attribute value is'),
                'name'      => 'attr_value[]',
		));
        
    	$array = $model->getAttributesAsArray();
		foreach ($array as $attributeCode => $attributeValue) {
    		if (empty($attributeCode)) {
    			continue;
    		}
    		
    		if (is_array($attributeValue)) {
    			
    			foreach ($attributeValue as $i => $value) {
		    		/*
		    		 * Add Attribute Names
		    		 */
    				$elementCode = $attributeCode . '-' . $value . '-' . $i;
    				
		    		$fldAttr->addField('attr_code[' . $elementCode . ']', 'select', 
		    			array(
							'label'     => $hlp->__('Has attribute'),
					        'name'      => 'attr_code[' . $elementCode . ']',
					        'values'    => $this->getAttributes(),
					        'onchange'  => 'showOptions(this)',
					   		'value' 	=> $attributeCode,
		    				'note'      => $hlp->__('If attribute is related to configurable products, please make sure that attribute is used in layered navigation'),
			    		    'after_element_html' => '<a href="#" onclick="landingRemove(this);return false;" title="' . $hlp->__('Remove') . '">' . $hlp->__('X') . '</a>'
						)
				    );
				        
					/*
				     * Add Attribute Options
				     */
					$attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attributeCode);
					
			        if ('select' === $attribute->getFrontendInput() || 'multiselect' === $attribute->getFrontendInput()) {
						$options = $attribute->getFrontend()->getSelectOptions();
			            $fldAttr->addField('attr_value[' . $elementCode . ']', 'select', array(
							'label'     => $hlp->__('Attribute value is'),
			                'name'      => 'attr_value[' . $elementCode . ']',
			                'values'    => $options,
			                'value'    => $value,
						));
		            } else {
		                $fldAttr->addField('attr_value[' . $elementCode . ']', 'text', array(
							'label'     => $hlp->__('Attribute value is'),
		                	'name'      => 'attr_value[' . $elementCode . ']',
		                	'value'    => $value,
		                ));
		            }
    			}
    		}
		}
    } 
    
    private function getImageHtml($img)
    {
        $html = '';
        if ($img){
            $html .= '<p style="margin-top: 5px">';
            $html .= '<img src="'.Mage::getBaseUrl('media') . 'ambanners/' . $img .'" alt=""/>';
            $html .= '</p>';
        } 
        return $html;     
    }
    
    protected function getTree()
    {
        $rootId = Mage::app()->getStore(0)->getRootCategoryId();         
        $tree = array();
        $collection = Mage::getModel('catalog/category')
            ->getCollection()->addNameToResult();
            
        $pos = array();
        foreach ($collection as $cat){
            $path = explode('/', $cat->getPath());
            if ((!$rootId || in_array($rootId, $path)) && $cat->getLevel()){
                $tree[$cat->getId()] = array(
                    'label' => str_repeat('--', $cat->getLevel()) . $cat->getName(), 
                    'value' => $cat->getId(),
                    'path'  => $path,
                );
            }
            $pos[$cat->getId()] = $cat->getPosition();
        }
        
        foreach ($tree as $catId => $cat) {
            $order = array();
            foreach ($cat['path'] as $id) {
        		$order[] = isset($pos[$id]) ? $pos[$id] : 0;
            }
            $tree[$catId]['order'] = $order;
        }
        
        usort($tree, array($this, 'compare'));
        array_unshift($tree, array('value'=>'', 'label'=>''));
        
        return $tree;
    }
    
    /**
     * Compares category data. Must be public as used as a callback value
     *
     * @param array $a
     * @param array $b
     * @return int 0, 1 , or -1
     */
     
    public function compare($a, $b)
    {
        foreach ($a['path'] as $i => $id){
            if (!isset($b['path'][$i])){ 
                // B path is shorther then A, and values before were equal
                return 1;
            }
            if ($id != $b['path'][$i]){
                // compare category positions at the same level
                $p = isset($a['order'][$i]) ? $a['order'][$i] : 0;
                $p2 = isset($b['order'][$i]) ? $b['order'][$i] : 0;
                return ($p < $p2) ? -1 : 1;
            }
        }
        // B path is longer or equal then A, and values before were equal
        return ($a['value'] == $b['value']) ? 0 : -1;
    }
    
	protected function getAttributes()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setItemObjectClass('catalog/resource_eav_attribute')
            ->setEntityTypeFilter(Mage::getResourceModel('catalog/product')->getTypeId())
        ;
            
        $options = array(''=>'');
		foreach ($collection as $attribute){
		    $label = $attribute->getFrontendLabel();
			if ($label){ // skip system attributes
			    $options[$attribute->getAttributeCode()] = $label;
			}
		}
		asort($options);
        
		return $options;
    }  
}
