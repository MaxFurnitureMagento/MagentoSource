<?php
class Cofamedia_CartProducts_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
	public function getPrice($product)
		{
      $product->load($product->getId());
//       Mage::log($product->debug(), null, 'test.log');
			$data = $product->getData();
			$total = $this->getCartTotal();
      
      # Fixed price
			$price = (float) parent::getPrice($product);
			
			# Percent price
      if($price_percent = $data['cartproducts_price_percent']) {
        $price_percent = $total * (float) $price_percent / 100;
      }
      # Range price
      if($price_range = $data['cartproducts_price_range'])
        {
          $range = explode(',', $price_range);
          foreach($range as $pair)
            {
              $pair = explode('=', $pair);
              if($total <= $pair[0])
                return $price_range = (float) $pair[1];
            }
        }
        
      $price = max($price, $price_percent);
      
      return $price;
		}
  
  public function getCartTotal()
    {
      $total = 0;
      
      if(Mage::app()->getStore()->isAdmin())
        {
          $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
          $collection = $quote->getAllItems();
        }
      else
        {
          $cart = Mage::getSingleton('checkout/cart');
          $collection = $cart->getItems();
        }

      foreach($collection as $i)
        {
          if($i->getProduct()->getTypeId() == 'cartproduct') continue;
          elseif($i->getParentItemId()) continue;
          $total+= $i->getBaseRowTotal();
        }
        
      return $total;
    }
}