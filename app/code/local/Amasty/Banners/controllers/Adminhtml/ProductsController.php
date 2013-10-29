<?php
/**
 * @copyright   Copyright (c) 2009-2012 Amasty (http://www.amasty.com)
 */
class Amasty_Banners_Adminhtml_ProductsController extends Mage_Adminhtml_Controller_Action
{
    public function productsAction() 
	{
        $grid = $this->getLayout()->createBlock('ambanners/adminhtml_rule_edit_tab_products')
                    ->setSelectedProducts($this->getRequest()->getPost('selected_products', null));
        
        // get serializer block html if needed
        $serializerHtml = ''; 
        if ($this->isFirstTime()){
            $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
            $serializer->initSerializerBlock($grid, 'getSavedProducts', 'selected_products', 'selected_products');
            $serializerHtml = $serializer->toHtml();
        } 
                
	    $this->getResponse()->setBody(
	           $grid->toHtml() . $serializerHtml
	    ); 
	}
	
	private function isFirstTime()
	{
	    $res = true;
	    
        $params = $this->getRequest()->getParams();
        $keys   = array('sort', 'filter', 'limit', 'page');
        
        foreach($keys as $k){
            if (array_key_exists($k, $params))
                $res = false;
        }
        
        return $res;	    
	}
}