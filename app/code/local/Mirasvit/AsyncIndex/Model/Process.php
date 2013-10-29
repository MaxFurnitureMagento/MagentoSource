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


class Mirasvit_AsyncIndex_Model_Process extends Mage_Index_Model_Process
{
    const STATUS_WAIT = 'wait';

    public function getStatusesOptions()
    {
        return array(
            self::STATUS_PENDING         => Mage::helper('index')->__('Ready'),
            self::STATUS_RUNNING         => Mage::helper('index')->__('Processing'),
            self::STATUS_REQUIRE_REINDEX => Mage::helper('index')->__('Reindex Required'),
            self::STATUS_WAIT            => Mage::helper('index')->__('Wait (in queue)'),
        );
    }

    /**
     * Reindex all data what this process responsible is
     *
     */
    public function reindexAll($force = false)
    {
        if (!$force && Mage::getStoreConfig('asyncindex/general/full_reindex')) {
            $this->changeStatus(Mirasvit_AsyncIndex_Model_Process::STATUS_WAIT);
        } else {
            parent::reindexAll();
        }
    }

    /**
     * Reindex all data what this process responsible is
     * Check and using depends processes
     *
     * @return Mage_Index_Model_Process
     */
    public function reindexEverything($force = false)
    {
        parent::reindexEverything();

        if ($force) {
            return $this->reindexAll(true);
        }
    }

    /**
     * Process event with assigned indexer object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Index_Model_Process
     */
    public function processEvent(Mage_Index_Model_Event $event, $force = false)
    {
        if (!$this->matchEvent($event)) {
            return $this;
        }

        if (!$force && $this->getMode() == self::MODE_MANUAL) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);
            return $this;
        }

        $ignored = Mage::getSingleton('asyncindex/config')->getIgnoredIndexes();

        $this->_getResource()->startProcess($this);
        $this->_setEventNamespace($event);
        $isError = false;
        try {
            if (!in_array($this->getId(), $ignored)) {
                $this->getIndexer()->processEvent($event);
            }
        } catch (Exception $e) {
            $isError = true;
        }
        $event->resetData();
        $this->_resetEventNamespace($event);
        $this->_getResource()->endProcess($this);
        $event->addProcessId($this->getId(), $isError ? self::EVENT_STATUS_ERROR : self::EVENT_STATUS_DONE);

        return $this;
    }

}