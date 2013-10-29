<?php
class Cofamedia_CartProducts_Model_Product_Attribute_Source_Pricetype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
		public function getAllOptions()
		{
				$this->_options = array(
						array(
								'value' => 0,
								'label' => 'Fixed',
						),
						array(
								'value' => 1,
								'label' => 'Percent',
						)
				);
				return $this->_options;
		}
}