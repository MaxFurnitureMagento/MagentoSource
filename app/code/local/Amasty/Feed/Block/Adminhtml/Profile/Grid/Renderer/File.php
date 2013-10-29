<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/ 
class Amasty_Feed_Block_Adminhtml_Profile_Grid_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
    public function render(Varien_Object $row)
    {
        if (file_exists($row->getMainPath())) {
            $downloadUrl = Mage::helper('adminhtml')->getUrl('amfeed/adminhtml_profile/download', array('file' => $row->getFilename() . $row->getFileExt()));
            return '<a href="'. $downloadUrl .'">' . $row->getFilename() . '</a>';
        } else {
            return $row->getFilename();
        }
    }
}