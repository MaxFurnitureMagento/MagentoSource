<?php 
class Cofamedia_HomeCategories_Block_List extends Mage_Core_Block_Template
{
	private $_collection;

	public function __construct()
		{
			parent::__construct();
			$collection = Mage::getModel('catalog/category')->getCollection()
										->addAttributeToSelect('*')
// 										->addAttributeToSelect('price')
										->addAttributeToFilter('home_position', array('neq' => '', 'neq' => 0))
										->setOrder('home_position', 'asc')
										;

			if($limit = (int) Mage::getStoreConfig('homecategories/configuration/limit'))
				$collection->getSelect()->limit($limit);

			$this->_collection = $collection;
		}

	public function getCategories()     
		{
			return $this->_collection;
		}
}