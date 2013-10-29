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


class Mirasvit_AsyncIndex_Block_Adminhtml_AsyncControl extends Mage_Adminhtml_Block_Template
{
    public function getAsyncCollection()
    {
        $this->getSavedTime();
        $result = array();

        $processes        = Mage::getSingleton('index/indexer')->getProcessesCollection();
        $eventsCollection = Mage::getResourceModel('index/event_collection');
        $eventsCollection->addProcessFilter($processes->getAllIds(), Mage_Index_Model_Process::EVENT_STATUS_NEW);

        $eventsCollection->getSelect()
            ->group('entity')
            ->group('entity_pk');

        $this->setQueueSize($eventsCollection->count());

        foreach ($eventsCollection as $event) {
            $item = new Varien_Object();
            $item->setType($event->getType());
            $item->setEntity($event->getEntity());
            $item->setEntityPk($event->getEntityPk());

            $result[] = $item;

            if (count($result) > 10) {
                break;
            }
        }


        return $result;
    }

    public function getSavedTime()
    {
        $seconds = intval(Mage::helper('asyncindex')->getVariable('time'));

        $time = new Zend_Date();
        $time->setTime('00:00:00');
        $time->addSecond($seconds);

        return $time->toString('HH').' hr '.$time->toString('mm').' min '.$time->toString('ss').' sec';
    }

    public function ucString($string)
    {
        $string = uc_words($string);
        $string = str_replace('_', ' ', $string);

        return $string;
    }

    public function getLogDisplay()
    {
        $display = 'none';

        if (isset($_COOKIE['async_detailed_log'])) {
            $display = $_COOKIE['async_detailed_log'];
        }
        
        return $display;
    }
}