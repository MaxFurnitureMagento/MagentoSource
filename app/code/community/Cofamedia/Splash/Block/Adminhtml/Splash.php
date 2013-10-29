<?php
class Cofamedia_Splash_Block_Adminhtml_Splash extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_splash';
    $this->_blockGroup = 'splash';
    
    $this->_headerText = Mage::helper('splash')->__('Cofamedia Splash Manager');
    parent::__construct();
    parent::_addButton('add', array(
		'label'     => Mage::helper('splash')->__('New Splash'),
		'onclick'   => 'setLocation(\''.$this->getUrl('*/*/edit').'\')',
        'class'     => 'add',
    ));    
  }
}