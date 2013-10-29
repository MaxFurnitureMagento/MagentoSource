<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

class Mxp_Menuadmin_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
    }
}