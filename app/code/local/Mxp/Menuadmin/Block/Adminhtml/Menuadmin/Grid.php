<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

class Mxp_Menuadmin_Block_Adminhtml_Menuadmin_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('menuadminGrid');
      $this->setDefaultSort('region');
      $this->setDefaultDir('DESC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('menuadmin/menuadmin')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('menuadmin_id', array(
          'header'    => Mage::helper('menuadmin')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'menuadmin_id',
      ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Store'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'store_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/store')->getCollection()->toOptionHash(),
            ));
        }

      $this->addColumn('region', array(
          'header'    => Mage::helper('menuadmin')->__('Region'),
          'align'     => 'left',
          'width'     => '110px',
          'index'     => 'region',
          'type'      => 'options',
          'options'   => Mage::getSingleton('menuadmin/region')->getOptionArray(),
      ));

      $this->addColumn('position', array(
          'header'    => Mage::helper('menuadmin')->__('Position'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'position',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('menuadmin')->__('Label'),
          'align'     =>'left',
          'index'     => 'title',
      ));


      $this->addColumn('status', array(
          'header'    => Mage::helper('menuadmin')->__('Status'),
          'align'     => 'left',
          'width'     => '100px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => Mage::getSingleton('menuadmin/status')->getOptionArray(),
      ));

      $this->addColumn('target', array(
          'header'    => Mage::helper('menuadmin')->__('Target'),
          'align'     => 'left',
          'width'     => '110px',
          'index'     => 'target',
          'type'      => 'options',
          'options'   => Mage::getSingleton('menuadmin/target')->getOptionArray(),
      ));

      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('menuadmin_id');
        $this->getMassactionBlock()->setFormFieldName('menuadmin');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('menuadmin')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('menuadmin')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('menuadmin/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('menuadmin')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('menuadmin')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

	public function getRowUrl($row)
	{
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}