<?php
class Cofamedia_CartProducts_Block_Total extends Mage_Checkout_Block_Cart_Totals
{
  protected $_template = 'cartproducts/total.phtml';
  protected $_store;

  protected function _construct()
    {
        $this->setTemplate($this->_template);
        $this->_store = Mage::app()->getStore();
    }

  public function getItems()
    {
      if($this->getRequest()->getControllerName() == 'cart') return array();
      
      $in_cart = Mage::helper('checkout/cart')->getCart()->getQuoteProductIds();
      $collection = Mage::getModel('cartproducts/products')->getCollection()
                    ;
//                     ->addAttributeToFilter('cartproducts_totals', 1)
      $items = array();
      foreach($collection as $p)
        {
          $id = $p->getId();
          if(!isset($in_cart[$id])) continue;
          $items[] = array(
            'title' => $p->getName(),
            'price' => $p->getPrice()
          );
        }

      return $items;
    }
  
  public function getCheckoutItems()
    {
      $in_cart = Mage::helper('checkout/cart')->getCart()->getQuoteProductIds();
      $collection = Mage::getModel('cartproducts/products')->getCollection()
                    ->addAttributeToFilter('cartproducts_checkout', 1)
                    ;
      
      $items = array();
      foreach($collection as $p)
        {
          $id = $p->getId();
          if(!isset($in_cart[$id])) continue;
          $items[] = array(
            'title' => $p->getName(),
            'price' => $p->getPrice()
          );
        }
      
      return $items;
    }
}
