<?php
class Cofamedia_CartProducts_Model_Observer
{
  public function removeProductFromCart(Varien_Event_Observer $observer)
    {
      $item = $observer->getEvent()->getQuoteItem();
      $quote_id = $item->getQuoteId();
      $product_id = $item->getProductId();
      
      $collection = Mage::getModel('cartproducts/products')->getCollection()
                    ->addAttributeToFilter('cartproducts_selected', 1)
                    ->addAttributeToFilter('type_id', 'cartproduct')
                    ->addAttributeToFilter('entity_id', $product_id)
                    ;
      foreach($collection as $cp)
        Mage::getSingleton('core/session')->setData("cartproducts-$quote_id-$product_id", true);
    }
}