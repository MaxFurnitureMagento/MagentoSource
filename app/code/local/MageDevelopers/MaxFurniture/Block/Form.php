<?php
class MageDevelopers_MaxFurniture_Block_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id'        => 'import_form',
            'action'    => $this->getUrl('*/*/upload'),
            'method'    => 'post',
            'enctype'   => 'multipart/form-data'
        ));

        $form->addField('xls', 'file',
                array(
                    'name' => 'xls',
//                     'no_span' => true
                )
        );
        
        $form->addField('submit', 'submit',
                array(
                    'name' => 'test',
                    'value' => 'Upload File',
                    '' => true
                )
        );
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
  }
}
