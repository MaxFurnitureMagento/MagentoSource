<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Fast Asynchronous Re-indexing
 * @version   1.1.4
 * @revision  143
 * @copyright Copyright (C) 2013 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_AsyncIndex_Model_Handler
{
    protected $_timer     = 0;
    protected $_lockFile  = null;
    protected $_isLocked  = false;
    protected $_cleanTags = array();

    public function processQueue()
    {
        if (!Mage::helper('mstcore/code')->getStatus()) {
            return $this;
        }

        if (!Mage::getStoreConfig('asyncindex/general/change_reindex') || $this->isLocked()) {
            return $this;
        }

        $this->lock();
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();

        if (Mage::getStoreConfig('asyncindex/general/reindex_mode') == 'index') {
            $notLast = 0;
            foreach ($processes as $process) {
                $inLast = in_array($process->getId(), Mage::getSingleton('asyncindex/config')->getLastIndexes());

                if (!$inLast && $process->getUnprocessedEventsCollection()->count() > 0) {
                    $notLast++;
                }
            }

            foreach ($processes as $process) {
                $inLast = in_array($process->getId(), Mage::getSingleton('asyncindex/config')->getLastIndexes());

                if ((!$inLast || $notLast == 0) && $process->getUnprocessedEventsCollection()->count() > 0) {
                    $log = $this->_getLogger()->beginLog($this, 'Process queue for '.$process->getIndexer()->getName());

                    $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME);
                    $process->setStatus('pending');
                    $process->reindexAll(true);

                    $this->_getLogger()->commitLog($log);
                }
            }
        } else {
            $eventsCollection = Mage::getResourceModel('index/event_collection');
            $eventsCollection->addProcessFilter($processes->getAllIds(), Mage_Index_Model_Process::EVENT_STATUS_NEW);
            $eventsCollection->getSelect()
                ->group('entity')
                ->group('entity_pk');

            foreach ($processes as $process) {
                $process->setStatus('pending')->save();
            }

            $indexer = Mage::getSingleton('index/indexer');

            foreach ($eventsCollection as $event) {
                $log = $this->_getLogger()->beginLog($this, 'Process event '.$event->getId());
                
                Mage::helper('asyncindex')->addMessage('Start processing '.Mage::helper('asyncindex')->getEventDescription($event).'...');
                
                foreach ($processes as $process) {
                    Mage::helper('asyncindex')->addMessage('Start processing index "'.$process->getIndexer()->getName().'"...', 3);
                    
                    $process->processEvent($event, true);
                    
                    Mage::helper('asyncindex')->addMessage('Finish processing index "'.$process->getIndexer()->getName().'"...', 4);
                }
                $event->save();

                $this->_applyPriceRule($event);
                
                Mage::helper('asyncindex')->addMessage('Finish processing '.Mage::helper('asyncindex')->getEventDescription($event), 2);

                $this->_getLogger()->commitLog($log);
            }
        }

        $this->_clearCache(null, true);

        $this->unlock();

        return $this;
    }

    protected function _applyPriceRule($event)
    {
        if ($event->getEntity() == 'catalog_product'
            && $event->getType() == 'save'
            && $event->getEntityPk()) {
            $productId = $event->getEntityPk();
            Mage::getSingleton('catalogrule/rule')->applyAllRulesToProduct($productId, true);
        } 
    }

    public function processReindex()
    {
        if (!Mage::helper('mstcore/code')->getStatus()) {
            return $this;
        }

        if (!Mage::getStoreConfig('asyncindex/general/full_reindex') || $this->isLocked()) {
            return $this;
        }

        $this->lock();

        $collection = Mage::getModel('index/process')->getCollection()
            ->addFieldToFilter('status', array('', Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT));

        foreach ($collection as $process) {
            $process = $process->load($process->getId());
            if (($process->getStatus() == '' || $process->getStatus() == Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT)
                && !$process->isLocked()) {

                Mage::helper('asyncindex')->addMessage('Start processing full reindex for index "'.$process->getIndexer()->getName().'"...');

                $process->reindexEverything(true);

                Mage::helper('asyncindex')->addMessage('Finish processing full reindex for index "'.$process->getIndexer()->getName(), 2);

                $this->_getLogger()->log($this, __FUNCTION__, $process->getIndexer()->getName().' index was rebuilt.');
            }
        }

        $this->unlock();
    }

    public function validateProductIndex()
    {
        if (!Mage::helper('mstcore/code')->getStatus()) {
            return $this;
        }
        
        if (!Mage::getStoreConfig('asyncindex/general/validate_product_index') || $this->isLocked()) {
            return $this;
        }

        $this->lock();

        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');
        $status   = $this->_getAttribute('status');
        $stores   = Mage::app()->getStores();

        foreach ($stores as $store) {
            $storeId   = $store->getId();
            $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
            $flatTable = sprintf('%s_%s', $resource->getTableName('catalog/product_flat'), $storeId);
            $bind      = array(
                'website_id'     => $websiteId,
                'store_id'       => $storeId,
                'entity_type_id' => (int)$status->getEntityTypeId(),
                'attribute_id'   => (int)$status->getId()
            );

            $fieldExpr = $this->_getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select = $adapter->select()
                ->from(array('e' => $resource->getTableName('catalog/product')), array('entity_id', 'updated_at'))
                ->join(
                    array('wp' => $resource->getTableName('catalog/product_website')),
                    'e.entity_id = wp.product_id AND wp.website_id = :website_id',
                    array())
                ->joinLeft(
                    array('t1' => $status->getBackend()->getTable()),
                    'e.entity_id = t1.entity_id',
                    array())
                ->joinLeft(
                    array('t2' => $status->getBackend()->getTable()),
                    't2.entity_id = t1.entity_id'
                        .' AND t1.entity_type_id = t2.entity_type_id'
                        .' AND t1.attribute_id = t2.attribute_id'
                        .' AND t2.store_id = :store_id',
                    array())
                ->joinLeft(
                    array('flat' => $flatTable),
                    'e.entity_id = flat.entity_id',
                    array('updated_at'))
                ->where('flat.updated_at <> e.updated_at OR flat.updated_at IS NULL')
                ->where('t1.entity_type_id = :entity_type_id')
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
                ->where("{$fieldExpr} = ?", Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->limit(intval(Mage::getStoreConfig('asyncindex/general/queue_batch_size')));

            $result = $adapter->fetchAll($select, $bind);

            foreach ($result as $row) {
                $entityId = $row['entity_id'];


                $product = Mage::getModel('catalog/product');
                $product->setForceReindexRequired(1)
                        ->setIsChangedCategories(1)
                        ->setId($entityId);

                $result = Mage::getSingleton('index/indexer')->logEvent(
                    $product, Mage_Catalog_Model_Product::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
                );

                $this->_getLogger()->log($this, __FUNCTION__, Mage_Catalog_Model_Product::ENTITY.':'.Mage_Index_Model_Event::TYPE_SAVE.':'.$product->getId());

                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                Mage::getSingleton('index/indexer')->logEvent(
                    $stockItem, Mage_CatalogInventory_Model_Stock_Item::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
                );
                $this->_getLogger()->log($this, __FUNCTION__, Mage_CatalogInventory_Model_Stock_Item::ENTITY.':'.Mage_Index_Model_Event::TYPE_SAVE.':'.$stockItem->getId());
            }
        }

        $this->unlock();
    }

    public function validateCategoryIndex()
    {
        if (!Mage::getStoreConfig('asyncindex/general/validate_category_index')) {
            return $this;
        }

        $resource = Mage::getSingleton('core/resource');
        $adapter  = $resource->getConnection('core_write');
        $rootId   = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $stores   = Mage::app()->getStores();

        foreach ($stores as $store) {
            $storeId   = $store->getId();
            $suffix    = sprintf('store_%d', $storeId);
            $flatTable = sprintf('%s_%s', $resource->getTableName('catalog/category_flat'), $suffix);

            $select = $adapter->select()
                ->from(array('e' => $resource->getTableName('catalog/category')), array('entity_id'))
                ->joinLeft(
                    array('flat' => $flatTable),
                    'e.entity_id = flat.entity_id',
                    array())
                ->where('flat.updated_at <> e.updated_at OR flat.updated_at IS NULL')
                ->where('e.path = "'.(string)$rootId.'" OR e.path = "'."{$rootId}/{$store->getRootCategoryId()}".'" OR e.path LIKE "'."{$rootId}/{$store->getRootCategoryId()}/%".'"');

            $result = $adapter->fetchAll($select, array());

            foreach ($result as $row) {
                $entityId = $row['entity_id'];

                if ($entityId == 1) {
                    continue;
                }

                $category = Mage::getModel('catalog/category')->load($entityId);

                Mage::getSingleton('index/indexer')->logEvent(
                    $category, Mage_Catalog_Model_Category::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
                );

                $this->_getLogger()->log($this, __FUNCTION__, Mage_Catalog_Model_Category::ENTITY.':'.Mage_Index_Model_Event::TYPE_SAVE.':'.$category->getId());
            }

            break;
        }
    }

    protected function _clearCache($event, $clean = false)
    {
        if ($event != null) {
            $cacheTag = $event->getData('entity').'_'.$event->getData('entity_pk');
            $this->_cleanTags[] = $cacheTag;
        }

        if ($clean && count($this->_cleanTags)) {
            Mage::app()->getCache()->clean('matchingAnyTag', $this->_cleanTags);
        }

        Mage::app()->getCacheInstance()->cleanType('block_html');
    }

    /**
     * Return eav attribute by code
     * @param  string $attributeCode attribute code
     *
     * @return Mage_Model_Resource_Eav_Attribute attribute model
     */
    protected function _getAttribute($attributeCode)
    {
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage::getResourceModel('catalog/config')->getEntityTypeId(), $attributeCode);
        if (!$attribute->getId()) {
            Mage::throwException(Mage::helper('catalog')->__('Invalid attribute %s', $attributeCode));
        }
        $entity = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getEntity();
        $attribute->setEntity($entity);

        return $attribute;
    }


    /**
     * Get lock file resource
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $file = $varDir . DS . 'asyncreindex.lock';

            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }
        return $this->_lockFile;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function lock()
    {
        $this->_startTime();
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function lockAndBlock()
    {
        flock($this->_getLockFile(), LOCK_EX);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Mirasvit_AsyncIndex_Model_Handler
     */
    public function unlock()
    {
        $this->_stopTimer();
        flock($this->_getLockFile(), LOCK_UN);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        $fp = $this->_getLockFile();
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);
            return false;
        }
        return true;
    }


    protected function _getCheckSql($expression, $true, $false)
    {
        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IF((%s), %s, %s)", $expression, $true, $false);
        } else {
            $expression = sprintf("IF(%s, %s, %s)", $expression, $true, $false);
        }

        return new Zend_Db_Expr($expression);
    }

    /**
     * Close file resource if it was opened
     */
    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }

    protected function _getLogger()
    {
        return Mage::helper('asyncindex/logger');
    }

    protected function _startTime()
    {
        $this->_timer = microtime(true);
    }

    protected function _stopTimer()
    {
        $time  = microtime(true) - $this->_timer;
        $time += Mage::helper('asyncindex')->getVariable('time');

        Mage::helper('asyncindex')->setVariable('time', floatval($time));

        return $this;
    }
}