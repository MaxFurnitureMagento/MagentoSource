<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Plugin_PostViews extends Fishpig_Wordpress_Helper_Plugin_Abstract
{
	/**
	 * Prefix for options field in options table
	 *
	 * @var string|null
	 */
	protected $_optionsFieldPrefix = 'views';

	/**
	 * List of search engine bots
	 *
	 * @var array
	 */
	protected $_botList = array(
		'Google Bot' => 'googlebot',
		'Google Bot' => 'google', 
		'MSN' => 'msnbot', 
		'Alex' => 'ia_archiver', 
		'Lycos' => 'lycos', 
		'Ask Jeeves' => 'jeeves', 
		'Altavista' => 'scooter', 
		'AllTheWeb' => 'fast-webcrawler', 
		'Inktomi' => 'slurp@inktomi', 
		'Turnitin.com' => 'turnitinbot', 
		'Technorati' => 'technorati',
		'Yahoo' => 'yahoo', 
		'Findexa' => 'findexa',
		'NextLinks' => 'findlinks', 
		'Gais' => 'gaisbo',
		'WiseNut' => 'zyborg', 
		'WhoisSource' => 'surveybot',
		'Bloglines' => 'bloglines', 
		'BlogSearch' => 'blogsearch', 
		'PubSub' => 'pubsub', 
		'Syndic8' => 'syndic8', 
		'RadioUserland' => 'userland', 
		'Gigabot' => 'gigabot', 
		'Become.com' => 'become.com'
	);

	/**
	 * Process a post view
	 *
	 * @param null|Varien_Event_Observer $observer = null
	 * @return bool
	 */
	public function processPostView($observer = null)
	{
		if (!$this->_getPost()) {
			return false;
		}
		
		if ($this->getCount() == '1' && $this->_isLoggedIn()) {
			return false;
		}
		
		if ($this->getCount() == '2' && !$this->_isLoggedIn()) {
			return false;
		}
		
		if ($this->getExcludeBots() == '1') {
			$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
			
			foreach($this->_bots as $botName => $botCode) {
				if (stripos($userAgent, $botCode) !== false) {
					return false;
				}
			}
		}
		
		$postViews = (int)$this->_getPost()->getCustomField('views') + 1;

		$this->_getPost()->getResource()->updateMetaValue($this->_getPost(), 'views', $postViews);

		return true;
	}
	
	/**
	 * Determine whether All In One SEO is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('WP PostViews');
	}
	
	/**
	 * Determine whether the user is logged in
	 *
	 * @return bool
	 */
	protected function _isLoggedIn()
	{
		return Mage::helper('customer')->isLoggedIn();
	}
	
	/**
	 * Retrieve the current post
	 *
	 * @return false|Fishpig_Wordpress_Model_Post
	 */
	protected function _getPost()
	{
		return Mage::registry('wordpress_post');
	}
}


