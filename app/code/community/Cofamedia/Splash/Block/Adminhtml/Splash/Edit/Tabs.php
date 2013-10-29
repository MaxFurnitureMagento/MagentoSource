<?php
class Cofamedia_Splash_Block_Adminhtml_Splash_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('splash_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('splash')->__('Information'));
    }

}
