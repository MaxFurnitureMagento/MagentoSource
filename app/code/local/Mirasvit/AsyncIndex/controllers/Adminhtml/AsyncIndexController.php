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


class Mirasvit_AsyncIndex_Adminhtml_AsyncIndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Process the queue
     * @return void
     */
    public function processAction()
    {
        $handler = Mage::getModel('asyncindex/handler');
        $handler->processQueue();

        $this->_redirect('*/process/list');
    }


    public function stateAction()
    {
        $html = '<div id="content">';
        
        $messages = explode("\n", Mage::helper('asyncindex')->getVariable('message'));
        $lastMessage = end($messages);

        // if (strpos($lastMessage, 'Finish') === false && strpos($lastMessage, 'Start') !== false && Mage::helper('asyncindex')->isForceAllowed()) {
        //     $html .= 'stopped';
        // }
        
        $html .= '<table>';

        foreach ($messages as $value) {
            $arr   = explode('|', $value);
            $since = '';
            if (@$arr[1] > 100) {
                $since = Mage::helper('asyncindex')->timeSince(Mage::getSingleton('core/date')->gmtTimestamp() - @$arr[1]);
            }
            $html .= '<tr><td style="width: 500px;">'.@$arr[0].'</td><td>'.$since.' ago</td></tr>';
        }

        $html .= '</table>';

        if (Mage::helper('asyncindex')->isForceAllowed()) {
            $html .= '<span class="state waiting"><span>Waiting...</span></span>';
        } else {
            $html .= '<span class="state processing"><span>Proccessing...</span></span>';
        }

        $html .= '</div>';

        echo $html;
    }



}