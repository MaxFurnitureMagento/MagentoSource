<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */ 
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amnotfound/log')}` ADD `store_id` smallint(5) NOT NULL AFTER `log_id;
ALTER TABLE `{$this->getTable('amnotfound/log')}` ADD `client_ip` VARCHAR(255) NOT NULL;
");

$this->endSetup(); 