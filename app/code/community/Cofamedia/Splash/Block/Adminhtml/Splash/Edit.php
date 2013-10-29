<?php
class Cofamedia_Splash_Block_Adminhtml_Splash_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'splash_id';
        $this->_blockGroup = 'splash';
        $this->_controller = 'adminhtml_splash';

        parent::__construct();

        $url = 'splash';
        parent::addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getUrl("*/*/".$url."/") . '\')',
            'class'     => 'back',
        ), -1);

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('save', 'label', Mage::helper('splash')->__('Save'));
            $this->_addButton('saveandcontinue', array(
                'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if ($this->_isAllowedAction('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('splash')->__('Delete'));
        } else {
            $this->_removeButton('delete');
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('splash_splash')->getId()) {
            return Mage::helper('splash')->__("Edit '%s'", $this->htmlEscape(Mage::registry('splash_splash')->getData("heading")));
        }
        else {
            return Mage::helper('splash')->__('New Splash');
        }
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('splash/' . $action);
    }
}