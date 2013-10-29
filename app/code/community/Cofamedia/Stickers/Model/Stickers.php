<?php
class Cofamedia_Stickers_Model_Stickers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('stickers/stickers');
    }
    
    public function loadByIdentifier($identifier)
    {
        $s = $this->load($identifier, 'identifier');

        return $s;
    }    
		
		public function getSticker()
			{
				return $this->getData('image') ? Mage::getBaseUrl('media').$this->getData('image') : '';
			}
	
		public function getThumbnail()
			{
				return $this->getData('thumbnail') ? Mage::getBaseUrl('media').$this->getData('thumbnail') : '';
			}
	
    public function checkIdentifier($stickers_id)
    {
        return $this->_getResource()->checkIdentifier($sticekrs_id);
    }
    
    public function getContent($limit = 0)
    {
			$helper = Mage::helper('cms');
      $processor = $helper->getPageTemplateProcessor();
      $html = $processor->filter($this->getData("description"));
   		
			return $html;
    }
}