<?php
class Cofamedia_CartProducts_Model_Products extends Mage_Core_Model_Abstract
{
	public function getCollection()
		{
			$collection = Mage::getModel('catalog/product')->getCollection()
										->addAttributeToSelect('*')
										->addAttributeToFilter('status', 1)
										->addAttributeToFilter('type_id', 'cartproduct')
										->addAttributeToSort('cartproducts_position')
										;
			return $collection;
    }
}