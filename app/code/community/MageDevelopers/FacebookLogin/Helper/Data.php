<?php
class MageDevelopers_FacebookLogin_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isEnabled()
		{
			if(!$this->getAppID() || !$this->getAppSecret()) return false;
			return Mage::getStoreConfig('md_facebook/login/enabled');
		}

	public function getAppID()
		{
			return Mage::getStoreConfig('md_facebook/configuration/appid');
		}

	public function getAppSecret()
		{
			return Mage::getStoreConfig('md_facebook/configuration/app_secret');
		}

	public function getPosition()
		{
			return Mage::getStoreConfig('md_facebook/login/login_top_position');
		}
}