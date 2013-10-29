<?php
/**
 * Magento
 *
 * Override Mage_Checkout_Cart controller
 */


/**
 * Magento doesn't autoload controllers
 */
require_once("Mage/Checkout/controllers/CartController.php");


class MageDevelopers_MaxFurniture_CartController extends Mage_Checkout_CartController
{
    /**
     * Initialize product instance from request data
     * Overriden to include optional $id parametar
     *
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($id=0)
    {
        if($id==0){
          $productId = (int) $this->getRequest()->getParam('product');
        }else{
          $productId = $id;
        }
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    
    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
//         qq($params);
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            $related_products = $this->getRequest()->getParam('related_products');
            $related_qty = $this->getRequest()->getParam('related_qty');
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }
            
            $in_cart = $cart->getQuoteProductIds();
            if($product->getTypeId() == 'cartproduct') {
              $cart_product_id = $product->getId();
              if(in_array($cart_product_id, $in_cart)) {
                $this->_goBack();
                return;
              }
            }

            if($params['qty']) $cart->addProduct($product, $params);
            
            if (!empty($related_qty)) {
              foreach($related_qty as $pid=>$qty){
                if(intval($qty)>0){
                  $product = $this->_initProduct(intval($pid));
                  $related_params['qty'] = $filter->filter($qty);
                  if(isset($related_products[$pid])){
                    if($product->getTypeId() == 'bundle') {
                      $related_params['bundle_option'] = $related_products[$pid]['bundle_option'];
//                       qq($related_params);
//                       die('test');
                    } else {
                      $related_params['super_attribute'] = $related_products[$pid]['super_attribute'];
                    }
                  }
                  $cart->addProduct($product, $related_params);
                }
              }
            }
            
            $collection = Mage::getModel('cartproducts/products')->getCollection()
                          ->addAttributeToFilter('type_id', 'cartproduct')
                          ->addAttributeToFilter('cartproducts_selected', 1)
                          ;
                          
            foreach($collection as $p)
              {
                $id = $p->getId();
                if(isset($in_cart[$id])) continue;
                
                $cart = Mage::getSingleton('checkout/cart');
                $quote_id = $cart->getQuote()->getId();
                
                if(Mage::getSingleton('core/session')->getData("cartproducts-$quote_id-$id")) continue;
                
                $p->load($id);
                $cart->getQuote()->addProduct($p, 1);
              }
            
            if($cart->getQuote()->getShippingAddress()->getCountryId() == '') $cart->getQuote()->getShippingAddress()->setCountryId('US');
            $cart->getQuote()->setCollectShippingRates(true);
            $cart->getQuote()->getShippingAddress()->setShippingMethod('maxshipping_standard')->collectTotals()->save();
            
            $cart->save();
            
            Mage::getSingleton('checkout/session')->resetCheckout();


            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError() && $params['qty'] ){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
}
