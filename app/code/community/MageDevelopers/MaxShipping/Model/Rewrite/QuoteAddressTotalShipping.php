<?php
class MageDevelopers_MaxShipping_Model_Rewrite_QuoteAddressTotalShipping extends Mage_Sales_Model_Quote_Address_Total_Shipping
{
  public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
      $amount = $address->getShippingAmount();
      $title = Mage::helper('sales')->__('Shipping &amp; Handling');
//         if ($address->getShippingDescription()) {
//             $title .= ' (' . $address->getShippingDescription() . ')';
//         }
      $address->addTotal(array(
          'code' => $this->getCode(),
          'title' => $title,
          'value' => $address->getShippingAmount()
      ));
      return $this;
    }
}