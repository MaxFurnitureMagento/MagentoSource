<?php
class Cofamedia_CartProducts_Model_Attributesets
{
    public function toOptionArray()
    {
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();
        return $sets;
    }

}