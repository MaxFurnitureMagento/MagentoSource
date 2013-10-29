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


/**
 * Logger Helper
 *
 * @category Mirasvit
 * @package  Mirasvit_AsyncIndex
 */
class Mirasvit_AsyncIndex_Helper_Logger extends Mirasvit_MstCore_Helper_Logger
{
    private $_queue = array();

    public function beginLog($obj, $message, $info = '')
    {
        $this->_queue[$message] = array(
            'obj'     => $obj,
            'message' => $message,
            'info'    => $info,
            'time'    => microtime(true),
        );

        return $message;
    }

    public function commitLog($log)
    {
        $obj     = $this->_queue[$log]['obj'];
        $message = $this->_queue[$log]['message'];
        $info    = $this->_queue[$log]['info'];
        $time    = $this->_queue[$log]['time'];

        if (is_array($info)) {
            $info = print_r($info, true);
        }

        $info = 'Time: '.round(microtime(true) - $time, 4).$info;

        Mage::helper('mstcore/logger')->log($obj, $message, $info);
    }
}