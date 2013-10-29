<?php
class MageDevelopers_MaxFurniture_CacheController extends Mage_Core_Controller_Front_Action
{
  public function topcartAction() {
  
    # TOPCART
    
    $quote = Mage::helper('checkout/cart')->getQuote();
    $items = $quote->getAllItems();
    $count = 0;
    foreach($items as $item)
      {
        if($item->getProduct()->getTypeId() == 'cartproduct') continue;
        elseif($item->getParentItemId()) continue;
        $count+= $item->getQty();
      }
    $total = Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getQuote()->getSubtotal());
    
    $topcart = '<p class="item-canvas">Items in cart: '.$count.' Total: '.$total.' </p>';
    
    $content = array(
        'topcart' => $topcart,
        );

    # FEATURED IN NAVIGATION
    
    $nav = new Mage_Catalog_Block_Navigation();
    $helper = Mage::helper('catalog/category');
    $categories = $helper->getStoreCategories();
    
    foreach($categories as $c) {
      $content['featured-'.$c->getId()] = $nav->getFeaturedProductsBox($c);
    }
    
    # ONSALE
    
    $onsale = new Cofamedia_Onsale_Block_Onsale();
    $content['onsale'] = $this->getLayout()->createBlock('onsale/onsale')->setTemplate('onsale/onsale.phtml')->toHtml();
    
    echo Zend_Json::encode($content);
  }
  
}
