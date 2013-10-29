<?php
class Cofamedia_Stickers_Block_Adminhtml_Stickers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_stickers';
    $this->_blockGroup = 'stickers';
    
    $this->_headerText = Mage::helper('stickers')->__('Cofamedia Product Stickers Manager');
    parent::__construct();
    parent::_addButton('add', array(
		'label'     => Mage::helper('stickers')->__('New Sticker'),
		'onclick'   => 'setLocation(\''.$this->getUrl('*/*/edit').'\')',
        'class'     => 'add',
    ));    
  }
}