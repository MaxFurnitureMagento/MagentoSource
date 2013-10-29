<?php
/*
 * code that is running on the installation of the plugin, if the module version is 1.0.8+
 */

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('commercesciences_base/config')}`;
    CREATE TABLE `{$installer->getTable('commercesciences_base/config')}` (
      `entity_id` int(1) unsigned NOT NULL default 1,
      `user_id` varchar(20) default NULL,
      `security_token` varchar(1024) default NULL,
      `tag` varchar(2048) default NULL,
      PRIMARY KEY  (`entity_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();