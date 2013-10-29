<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('splash/splash')}`;
CREATE TABLE {$this->getTable('splash/splash')} (
  `splash_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `thumbnail` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `content` mediumtext,
  `description` mediumtext,
  `meta_keywords` text NOT NULL,
  `date_from` datetime DEFAULT NULL,
  `date_to` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `position` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`splash_id`),
  KEY `identifier` (`heading`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('splash/splash_store')}`;
CREATE TABLE {$this->getTable('splash/splash_store')} (
  `splash_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`splash_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Splash to Stores';

INSERT INTO `core_config_data` SET scope='default', path='splash/configuration/default_button', value='default/button.png';
");

$installer->endSetup(); 