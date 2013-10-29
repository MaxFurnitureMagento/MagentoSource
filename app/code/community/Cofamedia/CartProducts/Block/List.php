<?php
class Cofamedia_CartProducts_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_products = false;
	public function __construct()
		{
			parent::__construct();
			$collection = Mage::getModel('cartproducts/products')->getCollection();
			
			if($in_cart = Mage::helper('checkout/cart')->getCart()->getQuoteProductIds())
				{
					$cart_items = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();
					$quote = array();
					foreach($cart_items as $citem)
						$quote[$citem->getProductId()] = $citem->getItemId();
					foreach($collection as $key => $item)
						{
							if(in_array($key, $in_cart))
								{
									$quote_item_id = $quote[$key];
									$remove_link = $this->getUrl('checkout/cart/delete',
																							 array('id' => $quote_item_id,
																											Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl()
																										)
																							);
									$item->setInCart(true);
									$item->setRemoveLink($remove_link);
//								$collection->removeItemByKey($key);
								}
						}
				}
			$this->_products = $collection;
    }

	public function getProducts()
		{
			return $this->_products;
		}
}