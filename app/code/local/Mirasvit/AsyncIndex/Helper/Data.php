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


class Mirasvit_AsyncIndex_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function setVariable($key, $value)
    {
        $variable = Mage::getModel('core/variable');
        $variable = $variable->loadByCode('asyncindex_'.$key);

        $variable->setPlainValue($value)
            ->setHtmlValue(Mage::getSingleton('core/date')->gmtTimestamp())
            ->setName($key)
            ->setCode('asyncindex_'.$key)
            ->save();

        return $variable;
    }

    public function getVariable($key)
    {
        $variable = Mage::getModel('core/variable')->loadByCode('asyncindex_'.$key);

        return $variable->getPlainValue();
    }

    public function getVariableTimestamp($key)
    {
        $variable = Mage::getModel('core/variable')->loadByCode('asyncindex_'.$key);

        return $variable->getHtmlValue();
    }

    public function addMessage($message, $level = 1)
    {
        $messages = explode("\n", $this->getVariable('message'));

        $messages[] = str_repeat('&nbsp', ($level - 1) * 3).$message.'|'.Mage::getSingleton('core/date')->gmtTimestamp();

        for ($i = 0; $i < count($messages) - 100; $i++) {
            unset($messages[$i]);
        }

        $messages = implode("\n", $messages);

        $this->setVariable('message', $messages);

        return $this;
    }

    public function getEventDescription($event)
    {
        $str = '';

        $str .= 'Event "';
        $str .= 'entity: '.$event->getEntity().' \ ';
        $str .= ' entity id: '.$event->getEntityPk().'"';

        return $str;
    }

    public function timeSince($time)
    {
        $print = '';
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'min'),
            array(1 , 'sec')
        );

        for ($i = 0; $i < count($chunks); $i++) {
            $seconds = $chunks[$i][0];
            $name    = $chunks[$i][1];

            if (($count = floor($time / $seconds)) != 0) {
                $print .= $count.' ';
                $print .= $name;
                $print .= ' ';

                $time -= $count * $seconds;
            }
        }

        if ($print == '') {
            $print = '0 seconds';
        }

        return $print;
    }

    public function isForceAllowed()
    {
        $result = true;

        if (Mage::getModel('asyncindex/handler')->isLocked()) {
            $result = false;
        }


        return $result;
    }
}
