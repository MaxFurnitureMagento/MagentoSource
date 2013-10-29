<?php
class Celebros_Conversionpro_Block_Catalog_Category_View extends Mage_Catalog_Block_Category_View
{
    /**
     * Retrieve messages block
     *
	 * We're overriding this function to add the campaign block to category view pages, in case campaigns are enabled.
	 * We'll be using the messages class we created under the same folder, to send a modified object that has the campaign
	 * html right after all the standard messages.
	 *
     * @return Mage_Core_Block_Messages
     */
    public function getMessagesBlock()
    {
        if (!Mage::helper('conversionpro')->getIsEngineAvailableForNavigation()) {
			return parent::getMessagesBlock();
		}
		
		//Fetching the product list before running any code that has to do with campaigns, so as to trigger the request
		// to the Quiser API that will give us the info we need about the dynamic properties.
		$this->getChildHtml('product_list');
		
		//Get the standard messages block, as well as the html part separately.
		$this->_messagesBlock = parent::getMessagesBlock();
		$groupedHtml = $this->_messagesBlock->getGroupedHtml();
		
		//Only add the campaign block in case campaigns are enabled in the admin.
		if (Mage::helper('conversionpro')->isCampaignsEnabled()) {
			//Add the campaigns html to the end of the list of messages.
			$groupedHtml .= $this->getLayout()->createBlock(
				'Mage_Core_Block_Template',
				'conversionpro_campaigns',
				array('template' => 'conversionpro/catalog/campaigns.phtml')
			)->toHtml();
		}
		//Create a block that has a getter & setter for groupedHtml.
		$newBlock = $this->getLayout()->createBlock(
			'Celebros_Conversionpro_Block_Catalog_Category_Messages',
			'conversionpro_messages');
		$newBlock->setGroupedHtml($groupedHtml);

        return $newBlock;
    }
}