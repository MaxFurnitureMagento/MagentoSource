<?php
class Cofamedia_CartProducts_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function formatPrice($item)
		{
			if($item->getProduct()->getCartproductsPriceType() == 1)
				{
					$price = round($item->getProduct()->getData('price'), 2);
					return "$price%";
				}
			
			return Mage::helper('checkout')->formatPrice($item->getCalculationPrice());
		}
}