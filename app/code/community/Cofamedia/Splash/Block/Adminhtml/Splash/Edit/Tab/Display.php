<?php
class Cofamedia_Splash_Block_Adminhtml_Splash_Edit_Tab_Display extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('splash_splash');

        $fieldset = $form->addFieldset('display_fieldset', array('legend' => Mage::helper('splash')->__('Display'), 'class' => 'fieldset-wide'));

				$fieldset->addField('position', 'text', array(
            'name'      => 'position',
            'label'     =>  Mage::helper('splash')->__('Position'),
            'title'     =>  Mage::helper('splash')->__('Position'),
            'disabled'  => $isElementDisabled
        ));
        
        $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('splash')->__('Keywords'),
            'title' => Mage::helper('splash')->__('Meta Keywords'),
            'disabled'  => $isElementDisabled
        ));
				
				$dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );
        
				$fieldset->addField('date_from', 'date', array(
            'name'      => 'date_from',
            'label'     => Mage::helper('splash')->__('From Date'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso
        ));
        
				$fieldset->addField('date_to', 'date', array(
            'name'      => 'date_to',
            'label'     => Mage::helper('splash')->__('To Date'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }
        
        $fieldset->addField('active', 'select', array(
            'label'     => Mage::helper('splash')->__('Status'),
            'title'     => Mage::helper('splash')->__('Status'),
            'name'      => 'active',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ));
        if (!$model->getId()) {
            $model->setData('active', $isElementDisabled ? '0' : '1');
        }
        
				Mage::dispatchEvent('adminhtml_splash_edit_tab_display_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('splash')->__('Display');
    }

    public function getTabTitle()
    {
        return Mage::helper('splash')->__('Display');
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
        return Mage::getSingleton('admin/session')->isAllowed('splash/' . $action);
    }
}
