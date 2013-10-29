<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('stickers/stickers')}`;
CREATE TABLE {$this->getTable('stickers/stickers')} (
  `stickers_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `thumbnail` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  PRIMARY KEY (`stickers_id`),
  KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->installEntities();

$attribute_id = $installer->getAttributeId('catalog_product', 'cm_product_stickers');
$query = 'UPDATE '.$installer->getTable('catalog/eav_attribute').' SET';
$query.= ' is_used_for_price_rules=1';
$query.= ',used_in_product_listing=1';
$query.= ',is_visible_on_front=1';
$query.= ',is_used_for_promo_rules=1';
$query.= ' WHERE attribute_id='.$attribute_id;
$installer->getConnection()->query($query);

$installer->endSetup(); 