<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_MiscController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Set the post password and redirect to the referring page
	 *
	 */
	public function applyPostPasswordAction()
	{
		$password = $this->getRequest()->getPost('post_password');
		
		Mage::getSingleton('wordpress/session')->setPostPassword($password);
		
		$this->_redirectReferer();
	}
	
	/**
	 * Forward requests to the WordPress installation
	 *
	 */
	public function forwardAction()
	{
		$queryString = $_SERVER['QUERY_STRING'];
		
		$forwardTo = rtrim(Mage::helper('wordpress')->getWpOption('siteurl'), '/') . '/index.php?' . $queryString;

		$this->_redirectUrl($forwardTo);
	}
	
	/**
	 * Forward requests for images
	 *
	 */
	public function forwardFileAction()
	{
	
		$url = rtrim(Mage::helper('wordpress')->getWpOption('siteurl'), '/');
		
		$forwardTo = $url . '/' . ltrim($this->getRequest()->getParam('uri'), '/');

		$this->_redirectUrl($forwardTo);
	}
}
