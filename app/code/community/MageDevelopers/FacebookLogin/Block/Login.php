<?php 
class MageDevelopers_FacebookLogin_Block_Login extends Mage_Page_Block_Template_Links
{
	protected $_facebook = null;
	
	public function __construct()
		{
			parent::__construct();

			$this->_facebook = new Facebook_Facebook(array(
																										'appId'  => Mage::helper('facebooklogin')->getAppID(),
																										'secret' => Mage::helper('facebooklogin')->getAppSecret(),
																							));
		}
	
	public function getLoginUrl()
		{
			$params['redirect_uri'] = $this->getUrl('facebooklogin/ajax/login', array('_secure' => Mage::app()->getStore()->isCurrentlySecure(), 'referer' =>  Mage::helper('core')->urlEncode($this->helper('core/url')->getCurrentUrl())));
			$params['scope'] = 'email';
			return $this->_facebook->getLoginUrl($params);
		}

	public function getPosition()     
		{
			return $this->helper('facebooklogin')->getPosition();
		}
}