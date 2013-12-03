<?php

class Speroteck_TodaysDeals_Block_Sticker extends Mage_Core_Block_Template
{

    protected $_block;

    public function getStickerContent()
    {

        if ($this->_block->getId() == false) {
            return;
        }

        return Mage::getModel('cms/template_filter')->filter($this->_block->getContent());
    }

    public function isStickerEnabled()
    {
        if ($this->_block == null) {
            $this->_block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load('sticker');
        }

        return $this->_block->getIsActive();
    }
}