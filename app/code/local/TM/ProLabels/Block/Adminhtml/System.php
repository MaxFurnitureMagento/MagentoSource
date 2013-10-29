<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * EasyBanner module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_ProLabels_Block_Adminhtml_System extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_system';
        $this->_blockGroup = 'prolabels';
        $this->_headerText = Mage::helper('prolabels')->__('Manage System Labels');
        // $this->_addButtonLabel = Mage::helper('prolabels')->__('Add On Sale Label');
        $onSaleUrl = Mage::helper("adminhtml")->getUrl("*/*/new",array('rulesid'=>'1'));
        $inStockUrl = Mage::helper("adminhtml")->getUrl("*/*/new",array('rulesid'=>'2'));
        $isNewUrl = Mage::helper("adminhtml")->getUrl("*/*/new",array('rulesid'=>'3'));
        
        $this->_addButton('onsale', array(
            'label'   => Mage::helper('prolabels')->__('Add On Sale Label'),
            'onclick' => 'setLocation(\'' . $onSaleUrl . '\')',
            'class'   => 'add'
        ));
        
        $this->_addButton('instock', array(
            'label'   => Mage::helper('prolabels')->__('Add In Stock Label'),
            'onclick' => 'setLocation(\'' . $inStockUrl . '\')',
            'class'   => 'add'
        ));
        
        $this->_addButton('isnew', array(
            'label'   => Mage::helper('prolabels')->__('Add Is New Label'),
            'onclick' => 'setLocation(\'' . $isNewUrl . '\')',
            'class'   => 'add'
        ));
        
        parent::__construct();
        $this->_removeButton('add');
        $url = Mage::helper("adminhtml")->getUrl("*/*/applyRules");
        $urlSuccess = Mage::helper("adminhtml")->getUrl("*/*/applyUserRules");
        $this->_addButton('reindex', array(
            'label'     => Mage::helper('prolabels')->__('Reindex Labels'),
            'onclick'   => "
            function sendRequest(clearSession) {
                new Ajax.Request('".$url."', {
                    method: 'post',
                    parameters: {
                        clear_session: clearSession
                    },
                    onSuccess: showResponse
                    });
                }

            function showResponse(response) {
                var response = response.responseText.evalJSON();
                if (!response.completed) {
                    sendRequest(0);
                    var imageSrc = $('loading_mask_loader').select('img')[0].src;
                    $('loading_mask_loader').innerHTML = '<img src=\'' + imageSrc + '\'/><br/>' + response.message;
                } else {
                    window.location = '" . $urlSuccess . "'
                }
            }
            sendRequest(1);
                            ",
//            'onclick'   => 'setLocation(\'' . $this->getUrl('soldtogether/adminhtml_customer/reindex') . '\')',
        ));
    }
}