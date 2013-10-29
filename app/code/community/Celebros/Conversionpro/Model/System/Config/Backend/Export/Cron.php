<?php
/**
 * Celebros Qwiser - Magento Extension
 *
 * @category    Celebros
 * @package     Celebros_Conversionpro
 * @author		Omniscience Co. - Dan Aharon-Shalom (email: dan@omniscience.co.il)
 *
 */
class Celebros_Conversionpro_Model_System_Config_Backend_Export_Cron extends Mage_Core_Model_Config_Data
{
	const CRON_BASE_STRING 	= 'crontab/jobs/conversionpro_export';
	const CRON_STRING_PATH  = '/schedule/cron_expr';
	const CRON_MODEL_PATH   = '/run/model';
	const CRON_MODEL_VALUE 	= 'conversionpro/exporter::catalogUpdate';

	/**
     * Processing object before delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeDelete()
    {
		Mage::getModel('core/config_data')
			->getCollection()
			->addFieldToFilter('path', self::CRON_BASE_STRING . '_' . $this->getScopeId() . self::CRON_STRING_PATH)
			//->addFieldToFilter('scope_id', $this->getScopeId())
			->getFirstItem()
			->delete();
		return parent::_beforeDelete();
    }
	
	/**
	 * Cron settings after save
	 *
	 * @return Celebros_Conversionpro_Model_System_Config_Backend_Export_Cron
	 */
	protected function _afterSave()
	{
		//$enabled    = $this->getData('groups/export_settings/fields/cron_enabled/value');
		$cron_expr  = $this->getData('groups/export_settings/fields/cron_expr/value');
		try {
				$item = Mage::getModel('core/config_data')
					->getCollection()
					->addFieldToFilter('path', self::CRON_BASE_STRING . '_' . $this->getScopeId() . self::CRON_STRING_PATH)
					//->addFieldToFilter('scope_id', $this->getScopeId())
					->getFirstItem();
				
				if (!isset($item)) {
					$item = Mage::getModel('core/config_data');
				}
					
				$item->setValue($cron_expr)
					->setPath(self::CRON_BASE_STRING . '_' . $this->getScopeId() . self::CRON_STRING_PATH)
					//->setScope($this->getScope())
					//->setScopeId($this->getScopeId())
					->save();
					
				Mage::getModel('core/config_data')
					->load(self::CRON_BASE_STRING . '_' . $this->getScopeId() . self::CRON_MODEL_PATH, 'path')
					->setValue(self::CRON_MODEL_VALUE)
					->setPath(self::CRON_BASE_STRING . '_' . $this->getScopeId() . self::CRON_MODEL_PATH)
					//->setScope($this->getScope())
					//->setScopeId($this->getScopeId())
					->save();
		}
		catch (Exception $e) {
			Mage::throwException(Mage::helper('adminhtml')->__($e.'  -  Unable to save Cron expression'));
		}
	}
}
