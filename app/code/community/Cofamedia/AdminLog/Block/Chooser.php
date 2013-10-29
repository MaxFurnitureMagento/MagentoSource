<?php
class Cofamedia_AdminLog_Block_Chooser extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
	public $_logged, $_collection;
	public function __construct()
		{
			parent::__construct();
      $this->setId('adminlog');
			$this->_logged = Mage::getSingleton('admin/session')->getUser();
			
			$this->_collection = Mage::getModel('admin/user')->getCollection();
    }

	public function getChooserHtml()
		{
				$options = array();
				foreach($this->_collection as $u)
					{
						$options[] = array(
																'value' => $u->getUserId(),
																'label' => $u->getFirstname().' '.$u->getLastname(),
															);
					}

        $select = $this->getLayout()->createBlock('core/html_select')
            ->setData(array(
                'id'    => 'adminlog_chooser',
                'class' => 'select'
            ))
            ->setName('adminlog_chooser')
            ->setOptions($options)
            ->setValue($this->_logged->getUserId())
            ;

        return $select->getHtml();
		}
}