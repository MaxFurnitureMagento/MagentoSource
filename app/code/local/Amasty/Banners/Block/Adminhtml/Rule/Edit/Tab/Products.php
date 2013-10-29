<?php
/**
 * @copyright   Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Banners_Block_Adminhtml_Rule_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ambannersGridPr');
        $this->setUseAjax(true);
    }

    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')            
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('visibility',array('neq' => 1));
            
        $this->setCollection($collection);
        return parent::_prepareCollection();
    } 
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_set', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_set',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'right',
            'index'     => 'entity_id',
        ));     
            
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'width'     => '1',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
                
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/products', array('_current' => true));
    }

    public function getRowUrl($row)
    {
       return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getEntityId()));
    }
    
    protected function _getSelectedProducts()
    {
        $products = $this->getSelectedProducts();
        if (!is_array($products)) {
            $products = $this->getSavedProducts();
        }
        return $products;
    } 

    public function getSavedProducts()
    {
        return Mage::getModel('ambanners/rule')->getProducts($this->_getRuleId());        
    }   
    
    protected function _getRuleId()
    {
        return $this->getRequest()->getParam('id', 0);   
    }
}
