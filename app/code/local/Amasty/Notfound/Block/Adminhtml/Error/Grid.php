<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */    
class Amasty_Notfound_Block_Adminhtml_Error_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('logGrid');
        $this->setDefaultSort('date');
    }
    
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getModel('amnotfound/error')->getCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amnotfound'); 
        
        $this->addColumn('date', array(
            'header'    => $hlp->__('Date'),
            'index'     => 'date',
            'type'      => 'datetime', 
            'width'     => '170px',
            'gmtoffset' => true,
            'default'	=> ' ---- ',
        ));
        
        $this->addColumn('type', array(
            'header'    => $hlp->__('Type'),
            'width'     => '120px',
            'type'      => 'options',
            'options'   => array(
                Amasty_Notfound_Model_Mysql4_Error::TYPE_LOG    => $hlp->__('Logged Error'),
                Amasty_Notfound_Model_Mysql4_Error::TYPE_REPORT => $hlp->__('Page Crash'),
                Amasty_Notfound_Model_Mysql4_Error::TYPE_CRON   => $hlp->__('Cron Failure'),
            ),             
            'index'     => 'type',
        ));
        
        $this->addColumn('error', array(
            'header'            => $hlp->__('Error Description'),
            'index'             => 'error',
            'type'              => 'text',
            'nl2br'             => true,
            'truncate'          => 9000,
            'escape'            => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
  {
      return false;
  }

}