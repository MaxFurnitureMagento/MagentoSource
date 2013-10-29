<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/
class Amasty_Feed_Block_Adminhtml_Profile_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('profileTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('amfeed')->__('Feed Options'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('amfeed')->__('General'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_profile_edit_tab_general')->toHtml(),
        ));

        $this->addTab('content', array(
            'label'     => Mage::helper('amfeed')->__('Content'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_profile_edit_tab_content')->toHtml(),
        ));
        
        $this->addTab('condition', array(
            'label'     => Mage::helper('amfeed')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_profile_edit_tab_condition')->toHtml(),
        ));
        
        $this->addTab('delivery', array(
            'label'     => Mage::helper('amfeed')->__('Delivery'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_profile_edit_tab_delivery')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}