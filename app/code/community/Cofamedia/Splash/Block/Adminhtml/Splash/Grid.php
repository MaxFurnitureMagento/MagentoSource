<?php
class Cofamedia_Splash_Block_Adminhtml_Splash_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('splashBlockGrid');
        $this->setDefaultSort('splash_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
			$collection = Mage::getModel('splash/splash')->getCollection();
      /* @var $collection Mage_Cms_Model_Mysql4_Block_Collection */
      $this->setCollection($collection);
      return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('heading', array(
            'header'    => Mage::helper('splash')->__('Heading'),
            'align'     => 'left',
            'index'     => 'heading'
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('splash')->__('Position'),
            'align'     => 'left',
            'index'     => 'position'
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('splash')->__('Status'),
            'index'     => 'active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('splash')->__('Disabled'),
                1 => Mage::helper('splash')->__('Enabled')
            ),
        ));

        $this->addColumn('date_from', array(
            'header'    => Mage::helper('splash')->__('From Date'),
            'index'     => 'date_from',
            'type'      => 'datetime',
        ));

        $this->addColumn('date_to', array(
            'header'    => Mage::helper('splash')->__('To Date'),
            'index'     => 'date_to',
            'type'      => 'datetime',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('splash')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('splash_id' => $row->getId()));
    }
	
}