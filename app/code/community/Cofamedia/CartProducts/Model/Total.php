<?php
class Cofamedia_CartProducts_Model_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
  public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $address->addTotal(array(
            'code'  => $this->getCode(),
            'title' => Mage::helper('sales')->__('Cart Products'),
            'value' => 0
        ));
        return $this;
    }
}
