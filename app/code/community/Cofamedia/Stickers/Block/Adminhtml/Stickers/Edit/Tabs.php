<?php
class Cofamedia_Stickers_Block_Adminhtml_Stickers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stickers_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('stickers')->__('Information'));
    }

}
