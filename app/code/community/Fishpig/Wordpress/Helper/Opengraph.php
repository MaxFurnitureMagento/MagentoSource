<?php

class Fishpig_Wordpress_Helper_Opengraph extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Tags to be used for Open Graph tags
	 *
	 * @var array
	 */
	protected $_tags = array();
	
	/**
	 * Determine whether OG tags are enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/opengraph/enabled');
	}

	/**
	 * Determine whether to display OG tags on blog homepage
	 *
	 * @return bool
	 */
	public function isEnabledForHomepage()
	{
		return $this->isEnabled() && Mage::getStoreConfigFlag('wordpress_blog/opengraph/display_on_homepage');
	}	
	
	/**
	 * Determine whether to display OG tags on post page
	 *
	 * @return bool
	 */
	public function isEnabledForPosts()
	{
		return $this->isEnabled() && Mage::getStoreConfigFlag('wordpress_blog/opengraph/display_on_post');
	}
	
	/**
	 * Retrieve all of the tags
	 *
	 * @return array
	 */
	public function getTags()
	{
		if ($this->isEnabled()) {
			return $this->_tags;
		}
		
		return array();
	}
	
	/**
	 * Add a tag to the array
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return Fishpig_Wordpress_Helper_Opengraph
	 */
	public function addTag($key, $value)
	{
		if (trim($value) !== '') {
			$this->_tags[$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Automatically update the title and description tags using the head block meta data
	 *
	 * @return Fishpig_Wordpress_Helper_Opengraph
	 */
	public function updateTagsFromHeadBlock()
	{
		if ($headBlock = Mage::getSingleton('core/layout')->getBlock('head')) {
			$this->addTag('title', $headBlock->getTitle());
			$this->addTag('description', $headBlock->getDescription());
		}
		
		return $this;
	}
	
	/**
	 * Adds the base tags to the internal array
	 * These values are overwritten by the controllers
	 *
	 * @return Fishpig_Wordpress_Helper_Opengraph
	 */
	public function addBaseTags()
	{
		$this->updateTagsFromHeadBlock();
		
		$this->addTag('url', $this->getUrl());
		$this->addTag('type', 'blog');
		$this->addTag('site_name', $this->getWpOption('blogname'));

		return $this;
	}
}
