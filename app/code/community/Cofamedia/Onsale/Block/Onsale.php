<?php 
class Cofamedia_Onsale_Block_Onsale extends Mage_Catalog_Block_Product_List
{
	private $_collection;

	public function __construct()
		{
			parent::__construct();

			$now = date("Y-m-d 00:00:00");
			$collection = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('*')
// 										->addAttributeToSelect('price')
										->addAttributeToSelect('special_to_date')
// 										->addAttributeToSelect('name')
										->addAttributeToFilter('status', 1)
										->addAttributeToFilter('visibility', array('neq' => 1))
										->addAttributeToFilter('special_price', array('neq' => ''))
										->addAttributeToFilter(array(
																								array('attribute'=>'special_to_date', 'date'=>true, 'gteq'=>$now),
																								array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))
																								), '', 'left')
										->addAttributeToFilter(array(
																								array('attribute'=>'special_from_date', 'date'=>true, 'lteq'=>$now),
																								array('attribute'=>'special_from_date', 'is' => new Zend_Db_Expr('null'))
																								),'','left')
										;
			if($limit = (int) Mage::getStoreConfig('onsale/configuration/limit'))
				$collection->getSelect()->limit($limit);

			$this->_collection = $collection;
		}

	public function getProductsOnSale()     
		{
			return $this->_collection;
		}

	public function getPerPage()     
		{
			if(!$pp = Mage::getStoreConfig('onsale/configuration/per_page')) $pp = 1;
			return $pp;
		}

	public function getAnimationSpeed()     
		{
			if(!$speed = Mage::getStoreConfig('onsale/configuration/animation_speed')) $speed = 1000;
			return $speed;
		}

	public function getPageCount()     
		{
			$count = count($this->_collection);
			$pp = $this->getPerPage();
			$pages = ceil($count / $pp);
			
			return $pages;
		}
}