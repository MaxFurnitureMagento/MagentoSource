<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('ambanners/rule')}` (
  `rule_id`     mediumint(8) unsigned NOT NULL auto_increment,
  `name`        varchar(255) NOT NULL default '',
  `is_active`   tinyint(1) unsigned NOT NULL default '0',
  `sort_order`  int(10) unsigned NOT NULL DEFAULT '0',
  `from_date`   date  DEFAULT NULL,
  `to_date`     date  DEFAULT NULL,
  `stores`      varchar(255) NOT NULL default '',
  `cust_groups` varchar(255) NOT NULL default '',
  `banner_position` tinyint(1) NOT NULL default '0',
  `banner_img`   varchar(255) NOT NULL default '',
  `banner_link`  varchar(255) NOT NULL default '',
  `banner_title` varchar(255) NOT NULL default '',
  `cms_block` varchar(255) NOT NULL default '',
  `conditions_serialized`  text,
  `cats`      text NOT NULL default '', 

  PRIMARY KEY  (`rule_id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('ambanners/attribute')}` (
  `attr_id` mediumint(8) unsigned NOT NULL auto_increment,
  `rule_id` mediumint(8) unsigned NOT NULL,
  `code`    varchar(255) NOT NULL default '',
  PRIMARY KEY  (`attr_id`),
  CONSTRAINT `FK_BANNERS_RULE` FOREIGN KEY (`rule_id`) REFERENCES {$this->getTable('ambanners/rule')} (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->endSetup();