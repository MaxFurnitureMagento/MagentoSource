<?php
class Cofamedia_Stickers_Block_Product extends Mage_Catalog_Block_Product
{
	protected $_product = false;
	protected $_sticker = false;
	
	public function __construct()
		{
			parent::__construct();
			if (!Mage::registry('product') && $this->getProductId()) {
					$product = Mage::getModel('catalog/product')->load($this->getProductId());
					Mage::register('product', $product);
			}
			$this->_product = $product = Mage::registry('product');
			if($sticker = $product->getCmProductStickers())
				$this->_sticker = Mage::getModel('stickers/stickers')->loadByIdentifier($sticker);
    }
	
	public function getSticker()
		{
			return $this->_sticker ? $this->_sticker->getSticker() : false;;
		}

	public function getLabel()
		{
			return $this->_sticker ? $this->_sticker->getLabel() : '';
		}
}