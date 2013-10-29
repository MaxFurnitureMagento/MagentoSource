<?php
/*
 * code that is running on the installation of the plugin, if the module version is 1.0.9+
 */

Mage::getConfig()->init();

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('commercesciences_base/config')}`
      ADD COLUMN `cs_url` VARCHAR(200) DEFAULT 'http://commercesciences.com' NULL AFTER `tag`,
      ADD COLUMN `cs_api_url` VARCHAR(200) DEFAULT 'http://api.commercesciences.com' NULL AFTER `cs_url`;
");

//if there is no row in the db, create it
$csConfig = Mage::getModel("commercesciences_base/config")->load("1");
if(!$csConfig || !$csConfig->getId()){
    $csConfig = Mage::getModel("commercesciences_base/config")->setUserId(null);
    $csConfig->save();
}
$installer->endSetup();