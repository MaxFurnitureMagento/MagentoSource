<?php
/*
 * The whole purpose of this class is to emulate the messages block that we need to pass over to the catalog view template.
 * This way, we don't need to override any template files, and can still controll the contents of the messages area.
 * We'll be using this to add campaigns right below the messages on category pages, when nav2search is enabled.
 */
class Celebros_Conversionpro_Block_Catalog_Category_Messages extends Mage_Core_Block_Messages
{
    protected $_groupedHtml = '';
	
    public function getGroupedHtml()
    {
        return $this->_groupedHtml;
    }
	
	public function setGroupedHtml($html)
	{
		$this->_groupedHtml = $html;
	}
}