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


require_once 'abstract.php';

class Mirasvit_Shell_Asyncindex extends Mage_Shell_Abstract
{
    /**
     * Run script
     *
     */
    public function run()
    {
        set_time_limit(36000);
        if (!$this->getArg('test')) {
            $this->_reindex();
        } else {
            $this->_test();
        }
    }

    protected function _reindex()
    {
        try {
            $handler = Mage::getModel('asyncindex/handler');
            $handler->processReindex();
            $handler->processQueue();
            $handler->validateProductIndex();
            $handler->validateCategoryIndex();
        } catch (Exception $e) {
            Mage::log($e, null, 'asyncindex.log');
        }
    }

    protected function _test()
    {
        $processes = Mage::getModel('index/process')->getCollection();
        foreach ($processes as $process) {
            if ($this->getArg('test') == 'without') {
                $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)
                    ->save();
            } else {
                $process->setMode(Mage_Index_Model_Process::MODE_MANUAL)
                    ->save();
            }
        }

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->setPageSize(100);
        $ts = microtime(true);
        foreach ($productCollection as $product) {
            $product->save();
        }
        $te = microtime(true);

        echo $this->getArg('test').' extension: '.round($te - $ts, 4)." sec \n";
    }


    protected function _validate()
    {
    }
}

$shell = new Mirasvit_Shell_Asyncindex();
$shell->run();
