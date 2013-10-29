<?php
class Cofamedia_Splash_Model_Mysql4_Splash extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_store = null;
    
    public function _construct()
    {    
      $this->_init('splash/splash', 'splash_id');
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
			$format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
			
			if($date_from = $object->getData('date_from'))
				{
					$object->setData('date_from', Mage::app()->getLocale()->date($date_from, $format, null, false)
																				->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
													);
				}
			else $object->setData('date_from', null);
			
			if($date_to = $object->getData('date_to'))
				{
					$object->setData('date_to', Mage::app()->getLocale()->date($date_to, $format, null, false)
																			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
													);
				}
			else $object->setData('date_to', null);
			
			return $this;
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
			$select = $this->_getReadAdapter()->select()
										 ->from($this->getTable('splash/splash_store'))
										 ->where('splash_id = ?', $object->getId());

			if($data = $this->_getReadAdapter()->fetchAll($select))
				{
					$storesArray = array();
					foreach($data as $row)
						$storesArray[] = $row['store_id'];
						
					$object->setData('store_id', $storesArray);
				}

			return parent::_afterLoad($object);
    }    
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('splash_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('splash/splash_store'), $condition);

        foreach ((array)$object->getData('stores') as $store)
					{
            $storeArray = array();
            $storeArray['splash_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('splash/splash_store'), $storeArray);
					}

        return parent::_afterSave($object);
    }    
    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $select->join(
													array('cps' => $this->getTable('splash/splash_store')),
													$this->getMainTable().'.splash_id = `cps`.splash_id'
												 )
                    ->where('active=1 AND `cps`.store_id in (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }
    
    public function checkIdentifier($splash_id, $storeId)
    {
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'splash_id')
            ->join(
                array('cps' => $this->getTable('splash/splash_store')),
                'main_table.splash_id = `cps`.splash_id'
            )
            ->where('main_table.splash_id=?', $splash_id)
            ->where('main_table.active=1 AND `cps`.store_id in (' . Mage_Core_Model_App::ADMIN_STORE_ID . ', ?) ', $storeId)
            ->order('store_id DESC');

        return $this->_getReadAdapter()->fetchOne($select);
    }    
    
    public function lookupStoreIds($id)
    {
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
            ->from($this->getTable('splash/splash_store'), 'store_id')
            ->where("{$this->getIdFieldName()} = ?", $id)
        );
    }

    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    public function getStore()
    {
        return Mage::app()->getStore($this->_store);
    }    
}