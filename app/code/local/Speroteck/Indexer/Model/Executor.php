<?php

class Speroteck_Indexer_Model_Executor
{

    /**
     * Reindex everything by cron
     *
     * @return array
     */
    public static function reindexEverything()
    {
        $processes = array();
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer = Mage::getSingleton('index/indexer');

        $collection = $indexer->getProcessesCollection();
        foreach ($collection as $process) {
            /** @var Mage_Index_Model_Process $process */
            if ($process->getStatus() != Mage_Index_Model_Process::STATUS_PENDING) {
                $processes[] = $process;
            }
        }

        foreach ($processes as $process) {
            /* @var $process Mage_Index_Model_Process */
            try {
                $process->reindexEverything();
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $processes;
    }
}