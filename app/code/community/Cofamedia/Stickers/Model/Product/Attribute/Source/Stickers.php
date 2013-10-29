<?php
class Cofamedia_Stickers_Model_Product_Attribute_Source_Stickers extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		if(!$this->_options)
			{
				$list = Mage::getModel('stickers/stickers')->getCollection()
								;
				
				$this->_options = array(
						array(
								'value' => '',
								'label' => '',
						)
				);
				
				foreach($list as $s)
					{
						$this->_options[] = array('value' => $s->getIdentifier(), 'label' => $s->getLabel());
					}
			}
		return $this->_options;
	}
  
  public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            'type'      => 'varchar(255)',
            'unsigned'  => false,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null
        );

        return $columns;
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            'type'      => 'index',
            'fields'    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}