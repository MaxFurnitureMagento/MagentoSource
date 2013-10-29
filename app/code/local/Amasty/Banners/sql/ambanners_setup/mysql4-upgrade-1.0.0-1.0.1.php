<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
 	ALTER TABLE `{$this->getTable('ambanners/rule')}` MODIFY COLUMN `from_date` DATETIME DEFAULT NULL,
	 MODIFY COLUMN `to_date` DATETIME DEFAULT NULL;
	 
    ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `show_on_products` TEXT DEFAULT NULL AFTER `cats`;
    
    CREATE TABLE `{$this->getTable('ambanners/rule_products')}` (
  		`id` BIGINT UNSIGNED NOT NULL DEFAULT NULL AUTO_INCREMENT,
  		`rule_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  		`product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  		 PRIMARY KEY(`id`)
	)
	ENGINE = InnoDB;
	
	ALTER TABLE `{$this->getTable('ambanners/rule')}` CHANGE COLUMN `name` `rule_name` VARCHAR(255) NOT NULL DEFAULT '';
	ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `banner_type` VARCHAR(16)  NOT NULL DEFAULT 'image' COMMENT '0 - image, 1 - cms block, 2 - html page, 3 - products' AFTER `show_on_products`;
	ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `html_text` TEXT DEFAULT NULL AFTER `banner_type`;
	
");
$this->endSetup();
