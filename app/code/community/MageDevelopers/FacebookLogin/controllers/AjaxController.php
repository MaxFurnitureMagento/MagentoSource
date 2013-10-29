<?php
class MageDevelopers_FacebookLogin_AjaxController extends Mage_Core_Controller_Front_Action
{
public function loginAction()
	{
		$error = false;
		$facebook = new Facebook_Facebook(array(
																						'appId'  => Mage::helper('facebooklogin')->getAppID(),
																						'secret' => Mage::helper('facebooklogin')->getAppSecret(),
																			));
		$fb_user = $facebook->getUser();
		if($fb_user)
			{
				try
					{
						$user_profile = $facebook->api('/me');
					}
				catch (FacebookApiException $e)
					{
						$error = $this->__('Could not load user Facebook profile data.');
						$fb_user = null;
					}
			}
		if($fb_user)
			{
				$error = false;
				$session = Mage::getSingleton('customer/session');

				$uid = $user_profile['id'];
				$email = $user_profile['email'];
				$first_name = $user_profile['first_name'];
				$last_name = $user_profile['last_name'];

				$customer = Mage::getModel('customer/customer');
				$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
				
				if($customer->getId() !== NULL)
					{
						try
							{
								$session->loginById($customer->getId());
							}
						catch(Exception $e)
							{
								$error = $this->__($e->getMessage());
							}
					}
				else
					{
						$customer = Mage::getModel('customer/customer')->setId(null);

						$customer->setData('facebook_uid', $uid);
						$customer->setData('firstname', $first_name);
						$customer->setData('lastname', $last_name);
						$customer->setData('email', $email);
						$customer->getGroupId();
						$customer->save();
						
						if($customer->isConfirmationRequired())
							{
								$customer->sendNewAccountEmail('confirmation', $this->_getSession()->getBeforeAuthUrl());
							}
						else
							{
								$session->setCustomerAsLoggedIn($customer);
							}
					}
			}
// 		die($this->getAfterLoginUrl());
		Mage::app()->getFrontController()->getResponse()->setRedirect($this->getAfterLoginUrl());
// 		$this->_redirect($this->getAfterLoginUrl());
	}

	public function getAfterLoginUrl()
		{
			if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard'))
				{
					$referer = Mage::helper('core')->urlDecode($this->getRequest()->getParam('referer'));
				}
			else $referer = Mage::getUrl('customer/account');

			return $referer;
		}
}