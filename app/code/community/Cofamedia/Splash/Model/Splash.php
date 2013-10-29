<?php
class Cofamedia_Splash_Model_Splash extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('splash/splash');
    }
    
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('splash')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('splash')->__('Disabled'),
        ));

        Mage::dispatchEvent('splash_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }
    
		public function getPicture()
			{
				return $this->getData('image') ? Mage::getBaseUrl('media').$this->getData('image') : '';
			}
		
		public function getThumbnail()
			{
				if(!$thumb = $this->getData("thumbnail"))
					$thumb = 'cofa_media/splash/' . Mage::getStoreConfig('splash/configuration/default_button');
				
				return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/'.$thumb;
			}
			
    public function checkIdentifier($splash_id, $storeId)
    {
        return $this->_getResource()->checkIdentifier($splash_id, $storeId);
    }
    
    public function getContent($limit = 0)
    {
			$helper = Mage::helper('cms');
      $processor = $helper->getPageTemplateProcessor();
      $html = $processor->filter($this->getData("content"));
   		
			return $html;
    }
}