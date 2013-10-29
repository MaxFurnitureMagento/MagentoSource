<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
 	ALTER TABLE `{$this->getTable('ambanners/rule')}` ADD COLUMN `attributes` text DEFAULT NULL
");
$this->endSetup();
