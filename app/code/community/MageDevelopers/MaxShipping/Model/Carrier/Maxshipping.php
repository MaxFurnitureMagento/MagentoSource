<?php
class MageDevelopers_MaxShipping_Model_Carrier_Maxshipping extends Mage_Shipping_Model_Carrier_Abstract
{
	/**
	 * unique internal shipping method identifier
	 *
	 * @var string [a-z0-9_]
	 */
	protected $_code = 'maxshipping';

	/**
	 * Collect rates for this shipping method based on information in $request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $data
	 * @return Mage_Shipping_Model_Rate_Result
	 */

	public function getMethods(Mage_Shipping_Model_Rate_Request $request, $my_code=false)
		{
			$dest_country = $request->getDestCountryId();
			$dest_region = $request->getDestRegionId();
			$package_value = $request->getPackageValue();
			$shipping_price = 0;
			
// 			Mage::log($request->debug(), null, 'test.log');
// 			Mage::log($exception_regions, null, 'test.log');
// 			Mage::log($dest_country, null, 'test.log');
// 			Mage::log($dest_region, null, 'test.log');
			
			$i = 1;
			$max_price = 0;
			$items = array();
			$cart_products_price_reduction = 0;

			if($_items = $request->getAllItems())
			foreach($_items as $item)
				{
// 					if($item->getProductType() != 'simple') continue;
// 					Mage::log($i++, null, 'test.log');
// 					Mage::log($item->debug(), null, 'test.log');
					
					if($parent_item_id = $item->getParentItemId())
						{
							continue;
// 							$items[$item_id]['free_shipping'] = $item->getMaxFreeShipping();
// 							$items[$item_id]['primary_shipping'] = $item->getMaxPrimaryShip();
// 							$items[$item_id]['secondary_shipping'] = $item->getMaxSecondaryShip();
						}
					elseif($item->getProductType() == 'cartproduct') continue;
					
					$p = Mage::getModel('catalog/product')->load($item->getProductId());
					$item_id = $item->getItemId();
					$items[$item_id]['price'] = $item->getPrice();
          $items[$item_id]['sku'] = $item->getSku();
          $items[$item_id]['qty'] = $item->getQty();
					
					$items[$item_id]['free_shipping'] = $p->getMaxFreeShipping();
					$items[$item_id]['primary_shipping'] = $p->getMaxPrimaryShip();
					$items[$item_id]['secondary_shipping'] = $p->getMaxSecondaryShip();
					
					if($item->getPrice() >= $max_price)
						{
							$max_price = $item->getPrice();
							$max_id = $item_id;
						}
					
// 					$p = Mage::getModel('catalog/product')->load($item->getProductId());
// 					$tmp_price = (float) $p->getFreightShipTotal();
// 					Mage::log($p->getData(), null, 'test.log');
//          Mage::log($item->debug(), null, 'test.log');
				}

			foreach($items as $item_id => $item)
				{
          $qty = (int) $item['qty'];
          $max_done = false;
          for($i = 1; $i <= $qty; $i++)
            {
              if(!$max_done && ($item_id == $max_id))
                {
                  $shipping_price+= $item['free_shipping'] ? 0 : (float) $item['primary_shipping'];
                  $max_done = true;
                  continue;
                }
              $shipping_price+= $item['free_shipping'] ? 0 : (float) $item['secondary_shipping'];
            }
				}
// 199.5 + 105 + 210
			if($dest_country == 'CA') // Canada
				{
					$item_percent = ((float) Mage::getStoreConfig('carriers/maxshipping/canadian_percent')) / 100;
					$duty = ((float) Mage::getStoreConfig('carriers/maxshipping/canadian_duty')) / 100;
					$tax = ((float) Mage::getStoreConfig('carriers/maxshipping/canadian_tax')) / 100;

					$shipping_price+= $request->getPackagePhysicalValue() * $item_percent;
					$shipping_price+= $request->getPackagePhysicalValue() * $duty;
					$shipping_price+= $request->getPackagePhysicalValue() * $tax;
					$shipping_price+= (float) Mage::getStoreConfig('carriers/maxshipping/canadian_border_fee');
				}
			
			$methods = array();

// 			$label = Mage::getStoreConfig('carriers/maxshipping/title');
			
			$method = Mage::getModel('shipping/rate_result_method');
			$method->setMethodTitle('');
			$method->setCarrier($this->_code);
			$method->setMethod('standard');
			$method->setPrice($shipping_price);
			
			$methods[] = $method;
// 			Mage::log($methods, null, 'methods.log');
			return $methods;
		}
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
		{
			if(!Mage::getStoreConfig('carriers/'.$this->_code.'/active'))
				return false;
			
			$result = Mage::getModel('shipping/rate_result');
			foreach($this->getMethods($request) as $method)
				{
					$result->append($method);
				}
// 		Mage::log($result, null, 'methods.result.log');
			return $result;
		}
}