<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/
class Amasty_Feed_Block_Adminhtml_Field_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('fieldTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('amfeed')->__('Field Options'));
    }

    protected function _beforeToHtml()
    {
        $script = "
            <script>
            function hide_feedattr(){
                
                var display = $('base_attr').value ? '' : 'none';
                
                $('transform').up('tr').setStyle({'display': display})
            }
            hide_feedattr();
            
            Event.observe($('base_attr'),'change', hide_feedattr)
            </script>
        ";
        $this->addTab('general', array(
            'label'     => Mage::helper('amfeed')->__('General'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_field_edit_tab_general')->toHtml().$script,
        ));
        $this->addTab('mapping', array(
            'label'     => Mage::helper('amfeed')->__('Mapping'),
            'content'   => $this->getLayout()->createBlock('amfeed/adminhtml_field_edit_tab_mapping')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}