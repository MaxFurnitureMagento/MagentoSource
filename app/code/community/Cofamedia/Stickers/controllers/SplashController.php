<?php
class Cofamedia_Stickers_StickersController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
			$this->loadLayout();     
			$this->renderLayout();
    }
}