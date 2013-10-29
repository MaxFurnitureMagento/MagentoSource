<?php
class Cofamedia_Splash_Block_Splash extends Mage_Core_Block_Template
{
	protected $_items = array();
	
	public function __construct()
		{
			parent::__construct();
			$this->getSplashCollection();
		}
	
	public function _prepareLayout(){
		return parent::_prepareLayout();
    }
    
	public function getItems()
		{
			return $this->_items;
		}
	
	public function getAllowedCollection()
		{
			$list = Mage::getModel('splash/splash')->getCollection()
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addAttributeToFilter('active', '=', 1)
								->addDateFilter()
								;
			
			return $list;
		}
	
	private function getSplashCollection()
		{
			$list = $this->getAllowedCollection();
			
			if(Mage::getStoreConfig('splash/configuration/keywords'))
				{
					// $_SERVER['HTTP_REFERER'] = "http://www.google.com/#hl=en&biw=1440&bih=688&q=javascript+settimeout&aq=f&aqi=g10&aql=&oq=&gs_rfai=&fp=1b219014ca3fb4b2";
					// $_SERVER['HTTP_REFERER'] = "http://www.google.co.nz/url?sa=t&source=web&cd=3&sqi=2&ved=0CCkQFjAC&url=http%3A%2F%2Fwww.electrictoolbox.com%2Fusing-settimeout-javascript%2F&rct=j&q=javascript%20settimeout&ei=IijsTIzYAYLCcfeB2fYO&usg=AFQjCNFJ5Fn8pm2lVcZCt46Jn6A7v_S4TQ";
					// $_SERVER['HTTP_REFERER'] = "http://www.bing.com/search?q=javascript+date+to+timestamp&src=IE-SearchBox&FORM=IE8SRC";
					$_SERVER['HTTP_REFERER'] = "http://us.yhs.search.yahoo.com/avg/search?fr=yhs-avg-chrome&type=yahoo_avg_hs2-tb-web_chrome_us&p=concatenation+in+mysql";
					if(isset($_SERVER['HTTP_REFERER']))
						{
							$parts_url = parse_url($_SERVER['HTTP_REFERER']);
							$query = isset($parts_url['query']) ? $parts_url['query'] : (isset($parts_url['fragment']) ? $parts_url['fragment'] : '');
							if($query)
								{
									parse_str($query, $parts_query);
									if($query = isset($parts_query['q']) ? $parts_query['q'] : (isset($parts_query['p']) ? $parts_query['p'] : ''))
										{
											// $query = explode
											$tmp_list = clone $list;
											
											foreach($tmp_list as $l)
												{
													$id = $l->getId();
													$keywords = $l->getMetaKeywords();
													// qq($keywords);
												}

											// qq($query);
										}
								}
						}
					$list->addPageLimit();
				}
			
			if((Mage::getStoreConfig('splash/configuration/order') == 'random') &&
				 ($limit = Mage::getStoreConfig('splash/configuration/limit')))
				{
					$ids = array();
					$tmp_list = clone $list;
					foreach($tmp_list as $l)
						$ids[] = $l->getId();
					
					if($limit > count($ids)) $limit = count($ids);
					
					$condition = array();
					$keys = array_rand($ids, $limit);
					if(!is_array($keys)) $keys = array($keys);
					foreach($keys as $key)
						$condition[] = $ids[$key];
					
					$list->addAttributeToFilter('splash_id', 'in', "('".implode("','", $condition)."')");
				}
			else $list->addPageLimit();
			
			
			$list->setOrder("position", "DESC");
			$this->_items = $list;
		}
    
  public function getSplash(){
    	if($splash_id = $this->getRequest()->getParam('id')){
	  		if($splash_id != null && $splash_id != ''){
				$splash = Mage::getModel('splash/splash')->load($splash_id);
			} else {
				$splash = null;
			}	
			$this->setData('splash', $splash);
		}
		return $this->getData('splash');
	}
	
	public function getInterval()
		{
			$interval = Mage::getStoreConfig('splash/configuration/interval');
			if(!$interval || !is_numeric($interval)) $interval = 5;
			$interval*= 1000;
			
			return $interval;
		}
	
	public function getTrigger()
		{
			$triggers = Mage::getModel("splash/triggers")->toOptionArray();
			$trigger = Mage::getStoreConfig('splash/configuration/trigger');
			
			$ok = false;
			foreach($triggers as $data)
				if($trigger == $data['value']) $ok = true;
			
			if(!$ok) $trigger = $triggers[0]['value'];
			
			return $trigger;
		}
	
	public function getAnimations()
		{
			$animations = Mage::getStoreConfig('splash/configuration/animations');
			if(!$animations) $animations = 'fade';
			
			return $animations;
		}
	
	public function getAnimationSpeed()
		{
			$speed = (int) Mage::getStoreConfig('splash/configuration/animation_speed');
			
			return $speed;
		}
	
	public function getButtonType()
		{
			if(!$type = Mage::getStoreConfig('splash/configuration/button_type'))
				$type = 'thumbnails';
			
			return $type;
		}
	
	public function getLetter($position)
		{
			$position-= 1;
			$letters = "abcdefghijklmnopqrstuvwxyz";
			return $letters[$position];
		}
	
	public function showControls()
		{
			return Mage::getStoreConfig('splash/configuration/show_controls');
		}
	
	public function showProgress()
		{
			$v = explode(',', Mage::getStoreConfig('splash/configuration/controls'));
			return in_array('progress', $v);
		}
	
	public function showPause()
		{
			$v = explode(',', Mage::getStoreConfig('splash/configuration/controls'));
			return in_array('pause', $v);
		}
	
	public function showDescription()
		{
			return Mage::getStoreConfig('splash/configuration/button_description');
		}
}