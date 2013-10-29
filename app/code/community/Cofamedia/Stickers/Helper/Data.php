<?php
class Cofamedia_Stickers_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_items = array();
	
	public function __construct()
		{
// 			parent::__construct();
			$this->getStickersCollection();
		}

	public function _prepareLayout(){
		return parent::_prepareLayout();
    }
    
	public function getStickers()
		{
			return $this->_items;
		}
	
	public function getStickersCollection()
		{
			$collection = Mage::getModel('stickers/stickers')->getCollection();
			$list = array();
			foreach($collection as $c)
				$list[$c->getIdentifier()] = $c;
			
			$this->_items = $list;
		}

	public function getThumbnail($p)
		{
			if(!$sticker = $p->getCmProductStickers()) return;
			if(!isset($this->_items[$sticker])) return;
			$sticker = $this->_items[$sticker]->getThumbnail();
			return $sticker;
		}

	public function getLabel($p)
		{
			if(!$sticker = $p->getCmProductStickers()) return;
			if(!isset($this->_items[$sticker])) return;
			$sticker = $this->_items[$sticker]->getLabel();
			return $sticker;
		}

	public function getPositionClass()
		{
			$pos = Mage::getStoreConfig('stickers/configuration/position');
				
			switch($pos)
				{
					case Cofamedia_Stickers_Model_Position::POSITION_TOP_LEFT: $class = " top left"; break;
					case Cofamedia_Stickers_Model_Position::POSITION_TOP_RIGHT: $class = " top right"; break;
					case Cofamedia_Stickers_Model_Position::POSITION_BOTTOM_RIGHT: $class = " bottom right"; break;
					case Cofamedia_Stickers_Model_Position::POSITION_BOTTOM_LEFT: $class = " bottom left"; break;
					default : $class = " top left";
				}
			
			return $class;
		}
}