<?php
class Cofamedia_Stickers_Block_Adminhtml_Stickers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stickersBlockGrid');
        $this->setDefaultSort('stickers_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
			$collection = Mage::getModel('stickers/stickers')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('label', array(
            'header'    => Mage::helper('stickers')->__('Label'),
            'align'     => 'left',
            'index'     => 'label'
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('stickers')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('stickers_id' => $row->getId()));
    }
	
}