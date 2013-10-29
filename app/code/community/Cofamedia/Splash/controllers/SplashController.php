<?php
class Cofamedia_Splash_SplashController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
			$this->loadLayout();     
			$this->renderLayout();
    }
}