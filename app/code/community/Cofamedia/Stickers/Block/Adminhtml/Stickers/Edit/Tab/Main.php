<?php
class Cofamedia_Stickers_Block_Adminhtml_Stickers_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
	
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model = Mage::registry('stickers_stickers');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('stickers_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('stickers')->__('Content')));
        
        if ($model->getStickersId()) {
            $fieldset->addField('stickers_id', 'hidden', array(
                'name' => 'stickers_id',
            ));
        }
        
				$fieldset->addField('label', 'text', array(
            'name'      => 'label',
            'label'     =>  Mage::helper('stickers')->__('Label'),
            'title'     =>  Mage::helper('stickers')->__('Label'),
            'disabled'  => $isElementDisabled
        ));
        
				$fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     =>  Mage::helper('stickers')->__('Identifier'),
            'title'     =>  Mage::helper('stickers')->__('Identifier'),
            'disabled'  => $isElementDisabled
        ));
        
	      $fieldset->addField('thumbnail', 'image', array(
	          'label'     => Mage::helper('stickers')->__('Thumbnail'),
	          'required'  => false,
	          'name'      => 'thumbnail',
	          'path'      => 'cofa_media/stickers/thumbnail/',
				));
		  
	      $fieldset->addField('image', 'image', array(
	          'label'     => Mage::helper('stickers')->__('Image'),
	          'required'  => false,
	          'name'      => 'image',
				));
		  

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );
        //make Wysiwyg Editor integrate in the form
        
				$wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
        $wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
        $wysiwygConfig["widget_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index');
        $plugins = $wysiwygConfig->getData("plugins");
        $plugins[0]["options"]["url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin');
        $plugins[0]["options"]["onclick"]["subject"] = "MagentovariablePlugin.loadChooser('".Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_variable/wysiwygPlugin')."', '{{html_id}}');";
        $plugins = $wysiwygConfig->setData("plugins",$plugins);
        // Setting custom renderer for content field to remove label column
        // $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                    // ->setTemplate('cms/page/edit/form/renderer/content.phtml');
        // $contentField->setRenderer($renderer);

        $descriptionField = $fieldset->addField('description', 'editor', array(
            'label'      => 'Description',
            'name'      => 'description',
            'style'     => 'height:20em; width:50em;',
            'required'  => false,
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig
        ));

        Mage::dispatchEvent('adminhtml_stickers_edit_tab_main_prepare_form', array('form' => $form));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('stickers')->__('Content');
    }

    public function getTabTitle()
    {
        return Mage::helper('stickers')->__('Content');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('stickers/' . $action);
    }
}