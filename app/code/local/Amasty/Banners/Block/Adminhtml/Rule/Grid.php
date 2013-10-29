<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */ 
class Amasty_Banners_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('ruleGrid');
      $this->setDefaultSort('pos');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('ambanners/rule')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $hlp =  Mage::helper('ambanners'); 
    
	$this->addColumn('rule_id', array(
      'header'    => $hlp->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'rule_id',
    ));
    
    $this->addColumn('is_active', array(
        'header'    => $hlp->__('Status'),
        'align'     => 'center',
        'width'     => '80px',
        'index'     => 'is_active',
        'type'      => 'options',
        'options'   => $hlp->getStatuses(),
    )); 
    
    $this->addColumn('from_date', array(
        'header'    => $hlp->__('Date Start'),
        'align'     => 'left',
        'width'     => '140px',
        'type'      => 'date',
        'default'   => '--',
        'index'     => 'from_date',
    ));
    
    $this->addColumn('to_date', array(
        'header'    => $hlp->__('Date Expire'),
        'align'     => 'left',
        'width'     => '140px',
        'type'      => 'date',
        'default'   => '--',
        'index'     => 'to_date',
    ));
    
    $this->addColumn('banner_position', array(
        'header'    => $hlp->__('Position'),
        'align'     => 'left',
        'width'     => '150px',
        'index'     => 'banner_position',
        'type'      => 'options',
        'options'   => $hlp->getPosition(),
    ));
    
    $this->addColumn('rule_name', array(
        'header'    => $hlp->__('Name'),
        'index'     => 'rule_name',
    ));

    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('rule_id');
    $this->getMassactionBlock()->setFormFieldName('rules');
    
    $actions = array(
        'massActivate'   => 'Activate',
        'massInactivate' => 'Inactivate',
        'massDelete'     => 'Delete',
    );
    foreach ($actions as $code => $label){
        $this->getMassactionBlock()->addItem($code, array(
             'label'    => Mage::helper('ambanners')->__($label),
             'url'      => $this->getUrl('*/*/' . $code),
             'confirm'  => ($code == 'massDelete' ? Mage::helper('ambanners')->__('Are you sure?') : null),
        ));        
    }
    return $this; 
  }
}