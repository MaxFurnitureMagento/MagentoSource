<?php
class Cofamedia_Splash_Model_Mysql4_Splash_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
    protected $_previewFlag;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('splash/splash');
        
        $this->_map['fields']['splash_id'] = 'main_table.splash_id';
    }
    
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('splash_id');
            if (count($items)) {
                $select = $this->getConnection()->select()
                        ->from($this->getTable('splash/splash_store'))
                        ->where($this->getTable('splash/splash_store').'.splash_id IN (?)', $items);
                if ($result = $this->getConnection()->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('splash_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('splash_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('splash_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        parent::_afterLoad();
    }
    
    
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            $this->getSelect()->join(
                array('store_table' => $this->getTable('splash/splash_store')),
                'main_table.splash_id = store_table.splash_id',
                array()
            )
            ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store))
            ->group('main_table.splash_id');

            $this->setFlag('store_filter_added', true);
        }

        return $this;
    }
    
		public function addAttributeToFilter($attribute, $condition, $value)
    {
      $this->getSelect()->where("main_table.$attribute $condition $value");

      return $this;
    }
		
		public function addDateFilter()
    {
			$date = date('Y-m-d');
      $this->getSelect()->where("main_table.date_from is null or main_table.date_from <= (?)", $date);
      $this->getSelect()->where("main_table.date_to is null or main_table.date_to >= (?)", $date);

      return $this;
    }
        
		public function addPageLimit()
    {
			$limit = (int) Mage::getStoreConfig('splash/configuration/limit');
			
			if($limit)
				$this->getSelect()->limit($limit);

      return $this;
    }
		
}