<?php
class MageDevelopers_MaxFurniture_Block_Import extends Mage_Adminhtml_Block_Widget_View_Container
{
  protected $_blockGroup = 'maxfurniture';
  public function __construct()
    {
      parent::__construct();
        
//       $this->setTemplate('widget/view/container.phtml');

      $this->_removeButton('edit');
      $this->_removeButton('back');

      $files = array(
        'crossitem.csv' => true,
        'department.csv' => true,
        'images.csv' => true,
        'longbullet.csv' => true,
        'masitem.csv' => true,
        'productoption.csv' => true,
        'webdata.csv' => true,
      );
//       $id = Mage::app()->getRequest()->getParam('id');
      $DIR = Mage::getBaseDir()."/custom/add_products/csv/";
      $folder = scandir($DIR);
      foreach($folder as $file) {
        if($file[0] == '.') continue;
        if(isset($files[$file])) unset($files[$file]);
      }
      
      $disabled = $files ? true : false;
      
      $class = $disabled ? 'save disabled' : 'save';
      
      $this->_addButton('start', array(
            'label'     => Mage::helper('adminhtml')->__('Start Import'),
            'class'     => $class,
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/start') . '\'',
            'disabled'  => $disabled,
      ));

      $this->_addButton('related', array(
            'label'     => Mage::helper('adminhtml')->__('Import Related Items'),
            'class'     => $class,
            'onclick'   => 'window.location.href=\'' . $this->getUrl('*/*/related') . '\'',
            'disabled'  => $disabled,
      ));

      $this->_headerText = Mage::helper('maxfurniture')->__('Import Products');
    }
}
