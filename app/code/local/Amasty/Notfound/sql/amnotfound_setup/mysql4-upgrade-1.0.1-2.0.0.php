<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */ 
$this->startSetup();

$this->run("
CREATE TABLE `{$this->getTable('amnotfound/error')}` (
  `error_id` int(10) unsigned NOT NULL auto_increment,
  `date`     datetime  NOT NULL,
  `type`     tinyint(1) NOT NULL,
  `error`    text NOT NULL,
  PRIMARY KEY  (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('amnotfound/attempt')}` (
  `attempt_id` int(10) unsigned NOT NULL auto_increment,
  `date`       datetime  NOT NULL,
  `user`       varchar(255) NOT NULL,
  `client_ip`  varchar(255) NOT NULL,
  PRIMARY KEY  (`attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

"); 
// do not store even invalid password

$this->endSetup(); 