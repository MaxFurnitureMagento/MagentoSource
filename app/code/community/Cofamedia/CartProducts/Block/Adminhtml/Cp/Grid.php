<?php
class Cofamedia_CartProducts_Block_Adminhtml_Cp_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cpBlockGrid');
        $this->setDefaultSort('cartproducts_position');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
			$collection = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('sku')
										->addAttributeToSelect('name')
										->addAttributeToSelect('cartproducts_position')
										->addAttributeToSelect('short_description')
										->addAttributeToSelect('websites')
										->addAttributeToSelect('status')
										->addAttributeToFilter('type_id', 'cartproduct')
										;
			$collection->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
      $this->setCollection($collection);
      return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('cartproducts')->__('Name'),
            'align'     => 'left',
            'index'     => 'name'
        ));

        $this->addColumn('short_description', array(
            'header'    => Mage::helper('cartproducts')->__('Short Description'),
            'align'     => 'left',
            'index'     => 'short_description'
        ));

        $this->addColumn('cartproducts_position', array(
            'header'    => Mage::helper('cartproducts')->__('Position'),
            'align'     => 'left',
            'index'     => 'cartproducts_position'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('cartproducts')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
	
}