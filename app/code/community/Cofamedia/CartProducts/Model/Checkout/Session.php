<?php
class Cofamedia_Cartproducts_Model_Checkout_Session extends Mage_Checkout_Model_Session
{
  public function loadCustomerQuote()
    {
      if (!Mage::getSingleton('customer/session')->getCustomerId()) {
          return $this;
      }
      $customerQuote = Mage::getModel('sales/quote')
          ->setStoreId(Mage::app()->getStore()->getId())
          ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());

      if ($customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
          if ($this->getQuoteId()) {
              
              $in_cart = $this->getQuote()->getAllItems();
              $old_cart = $customerQuote->getAllItems();
              $oldies = array();
              foreach($old_cart as $item)
                if($item->getProduct()->getTypeId() == 'cartproduct')
                  $oldies[] = $item->getProductId();
                
              foreach($in_cart as $item)
                {
                  $product_id = $item->getProductId();
                  if(!in_array($product_id, $oldies)) continue;
                  $item_id = $item->getId();
                  
                  $this->getQuote()->removeItem($item_id);
                }
              $customerQuote->merge($this->getQuote())
                  ->collectTotals()
                  ->save();
          }

          $this->setQuoteId($customerQuote->getId());

          if ($this->_quote) {
              $this->_quote->delete();
          }
          $this->_quote = $customerQuote;
      } else {
          $this->getQuote()->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
              ->save();
      }
      return $this;
    }
}