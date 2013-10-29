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

class Mxp_Menuadmin_Block_Adminhtml_Menuadmin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'menuadmin';
        $this->_controller = 'adminhtml_menuadmin';

        $this->_updateButton('save', 'label', Mage::helper('menuadmin')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('menuadmin')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

    }

    public function getHeaderText()
    {
        if( Mage::registry('menuadmin_data') && Mage::registry('menuadmin_data')->getId() ) {
            return Mage::helper('menuadmin')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('menuadmin_data')->getTitle()));
        } else {
            return Mage::helper('menuadmin')->__('Add Item');
        }
    }
}