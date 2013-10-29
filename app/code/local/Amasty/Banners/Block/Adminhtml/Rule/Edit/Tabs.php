<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ruleTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ambanners')->__('Banners'));
    }

    protected function _beforeToHtml()
    {
        $tabs = array(
            'general'    => 'General',
            'banners'    => 'Banner Content',
            'conditions' => 'Cart Conditions',
        );
        
        foreach ($tabs as $code => $label){
            $label = Mage::helper('ambanners')->__($label);
            $content = $this->getLayout()->createBlock('ambanners/adminhtml_rule_edit_tab_' . $code)
                ->setTitle($label)
                ->toHtml();
                
            $this->addTab($code, array(
                'label'     => $label,
                'content'   => $content,
            ));
        }
        
        /*
         * Add Products Tab
         */
        $this->addTab('products', array(
			'label'     => Mage::helper('ambanners')->__('Products'),
            'title'     => Mage::helper('ambanners')->__('Products'),
            'class'     => 'ajax',
            'url'       => $this->getUrl('ambanners/adminhtml_products/products', array('_current' => true)),        
		));              
        
        return parent::_beforeToHtml();
    }
}