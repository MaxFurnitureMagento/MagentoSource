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

        $result = array("Reindex only required indexers");
        foreach ($processes as $process) {
            /* @var $process Mage_Index_Model_Process */
            try {
                $process->reindexEverything();
                $result[$process->getIndexerCode()] = 'Successfully finished';
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $result[$process->getIndexerCode()] = "ERROR: " . $e->getMessage();
            } catch (Exception $e) {
                Mage::logException($e);
                $result[$process->getIndexerCode()] = "ERROR: " . $e->getMessage();
            }
        }

        return print_r($result, 1);
    }
}