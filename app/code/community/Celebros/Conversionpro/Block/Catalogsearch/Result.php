<?php
class Celebros_Conversionpro_Block_CatalogSearch_Result extends Mage_CatalogSearch_Block_Result
{
    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
		//We're triggering the call to the Quiser API now, so that we'll have the info we need about campaigns at this stage.
		$this->getChildHtml('search_result_list');
		
		//Fetching the standard messages html.
		$messages = Mage::helper('catalogsearch')->getNoteMessages();
		//Only add the campaign block's html if it's enabled in the configuration menu.
		if (Mage::helper('conversionpro')->getIsEngineAvailable() 
			&& Mage::helper('conversionpro')->isCampaignsEnabled()) {
			$messages[] = $this->getLayout()->createBlock(
				'Mage_Core_Block_Template',
				'conversionpro_campaigns',
				array('template' => 'conversionpro/catalog/campaigns.phtml')
			)->toHtml();
		}
		return $messages;
    }
}
