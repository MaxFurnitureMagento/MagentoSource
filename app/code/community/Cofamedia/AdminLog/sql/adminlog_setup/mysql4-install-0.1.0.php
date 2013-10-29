<?php

$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('adminlog/adminlog')}`;
CREATE TABLE {$this->getTable('adminlog/adminlog')} (
  `adminlog_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `order_id` smallint(6) NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `order_state` varchar(255) NOT NULL,
  `order_status` varchar(255) NOT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`adminlog_id`),
  KEY `identifier` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();