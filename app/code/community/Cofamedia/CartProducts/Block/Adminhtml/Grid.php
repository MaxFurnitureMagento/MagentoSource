<?php
class Cofamedia_CartProducts_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_cp';
    $this->_blockGroup = 'cartproducts';
    $this->_headerText = Mage::helper('cartproducts')->__('Cart Produsts Manager');
    
		parent::__construct();
    
    if($attribute_set = Mage::getStoreConfig('cartproducts/configuration/attribute_set'))
			{
				parent::_addButton('add', array(
				'label'     => Mage::helper('cartproducts')->__('New Cart Product'),
				'onclick'   => 'setLocation(\''.$this->getUrl('adminhtml/catalog_product/new', array('set' => $attribute_set, 'type' => 'cartproduct')).'\')',
						'class'     => 'add',
				));
			}
		else
			{
				Mage::getSingleton('adminhtml/session')->addNotice('If you set default attribute set for cart products in configuration, you will be able to add them from here.');
				parent::removeButton('add');
			}
  }
}