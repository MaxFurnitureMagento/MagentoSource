<?php

$installer = $this;

$installer->startSetup();

$installer->installEntities();

$attribute_id = $installer->getAttributeId('catalog_product', 'md_qty_promotions');
$query = 'UPDATE '.$installer->getTable('catalog/eav_attribute').' SET';
$query.= ' is_used_for_price_rules=1';
$query.= ',used_in_product_listing=1';
$query.= ',is_visible_on_front=1';
$query.= ',is_used_for_promo_rules=1';
$query.= ' WHERE attribute_id='.$attribute_id;
$installer->getConnection()->query($query);

$installer->endSetup();