<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
 	ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `show_products` tinyint DEFAULT 0
");
$this->endSetup();
