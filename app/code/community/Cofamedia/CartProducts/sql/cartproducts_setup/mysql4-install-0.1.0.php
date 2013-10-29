<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$fieldList = array(
    'price',
//     'special_price',
//     'special_from_date',
//     'special_to_date',
//     'minimal_price',
//     'cost',
//     'tier_price',
//     'weight',
    'tax_class_id'
);

foreach ($fieldList as $field)
	{
		$applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('cartproduct', $applyTo))
			{
				$applyTo[] = 'cartproduct';
				$installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
			}
	}

$installer->installEntities();

// $attribute_id = $installer->getAttributeId('catalog_product', 'cartproducts_position');
// $query = 'UPDATE '.$installer->getTable('catalog/eav_attribute').' SET';
// $query.= ' used_in_product_listing=1';
// $query.= ',is_visible_on_front=1';
// $query.= ',apply_to=\'cartproduct\'';
// $query.= ' WHERE attribute_id='.$attribute_id;
// $installer->getConnection()->query($query);
// 
// $attribute_id = $installer->getAttributeId('catalog_product', 'cartproducts_price_type');
// $query = 'UPDATE '.$installer->getTable('catalog/eav_attribute').' SET';
// $query.= ' used_in_product_listing=1';
// $query.= ',is_visible_on_front=1';
// $query.= ',apply_to=\'cartproduct\'';
// $query.= ' WHERE attribute_id='.$attribute_id;
// $installer->getConnection()->query($query);

$installer->endSetup();