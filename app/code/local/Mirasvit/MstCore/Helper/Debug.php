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


class Mirasvit_MstCore_Helper_Debug extends Mage_Core_Helper_Abstract
{
    protected $_filename = null;
    protected $_enabled  = null;
    protected $_level    = 0;
    protected $_id       = 0;


    public function isEnabled()
    {
        if ($this->_enabled === null) {
            if (Mage::getStoreConfig('mstcore/logger/enabled')) {
                if (Mage::getStoreConfig('mstcore/logger/developer_ip') == '*'
                    || Mage::helper('core/http')->getRemoteAddr() == Mage::getStoreConfig('mstcore/logger/developer_ip')) {
                    $this->_enabled = true;
                } elseif (Mage::helper('core/http')->getRemoteAddr() == '' && Mage::getStoreConfig('mstcore/logger/cron')) {
                    $this->_enabled = true;
                }
            }
        }

        return $this->_enabled;
    }

    public function start()
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $this->_level++;
        $this->_id++;

        $backtrace = debug_backtrace();

        $caller = array();
        
        $caller['class']           = @$backtrace[1]['class'];
        $caller['type']            = @$backtrace[1]['type'];
        $caller['function']        = $backtrace[1]['function'];

        $caller['file']            = $this->_preparePath($backtrace[1]['file']);
        $caller['line']            = $backtrace[1]['line'];

        $caller['source_file']     = $this->_preparePath($backtrace[0]['file']);
        $caller['source_line']     = $backtrace[0]['line'];

        $caller['caller_source']   = $this->_getSource($backtrace[1]['file'], $caller['line']);
        $caller['function_source'] = $this->_getSource($backtrace[0]['file'], $caller['source_line'], 10, 30);
        
        $caller['backtrace']       = $this->_backtrace();
        $caller['args']            = $this->_prepareArgs($backtrace[1]['args']);
        $caller['action']          = 'start';
        $caller['level']           = $this->_level;
        $caller['id']              = $this->_id;

        $this->_write($caller);

        return array('level' => $this->_level, 'id' => $this->_id);
    }

    public function dump($key, $data)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $backtrace = debug_backtrace();

        $caller = array();
        
        $caller['class']           = @$backtrace[1]['class'];
        $caller['type']            = @$backtrace[1]['type'];
        $caller['function']        = $backtrace[1]['function'];

        $caller['file']            = $this->_preparePath($backtrace[1]['file']);
        $caller['line']            = $backtrace[1]['line'];

        $caller['dump']            = $this->_prepareArgs(array($key => $data));
        $caller['action']          = 'dump';
        $caller['included_files']  = get_included_files();
        $caller['level']           = $this->_level;
        $caller['id']              = $this->_id;

        $this->_write($caller); 
    }

    public function end($data = array())
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $backtrace = debug_backtrace();

        $caller = array();
        
        $caller['class']           = @$backtrace[1]['class'];
        $caller['type']            = @$backtrace[1]['type'];
        $caller['function']        = $backtrace[1]['function'];

        $caller['file']            = $this->_preparePath($backtrace[1]['file']);
        $caller['line']            = $backtrace[1]['line'];

        $caller['dump']            = $this->_prepareArgs($data);
        $caller['action']          = 'end';
        $caller['included_files']  = get_included_files();
        $caller['level']           = $this->_level;
        $caller['id']              = $this->_id;
        
        $this->_level--;

        $this->_write($caller); 
    }

    protected function _backtrace()
    {
        $backtrace = debug_backtrace();

        unset($backtrace[0]);
        unset($backtrace[1]);
        
        foreach ($backtrace as $key => $trace) {
            $backtrace[$key] = array(
                'class'    => @$trace['class'],
                'function' => @$trace['function'],
                'line'     => @$trace['line'],
                'file'     => $this->_preparePath(@$trace['file']),
           );
        }
        
        return $backtrace;
    }

    protected function _getFile()
    {
        $path = Mage::getBaseDir('var').DS.'log/mst';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($this->_filename == null) {
            $this->_filename = time();

            // remove old files
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if (substr($file, 0, strlen('debug_')) == 'debug_'
                        && time() - intval(substr($file, 6, 16)) > 60 * 60) {
                        unlink($path.DS.$file);
                    }
                }
            }
        }

        return $path.DS.'debug_'.$this->_filename.'.log';
    }

    protected function _write($data)
    {
        $formatter = new Zend_Log_Formatter_Simple('%message%'.PHP_EOL);

        $writer = new Zend_Log_Writer_Stream($this->_getFile());
        $writer->setFormatter($formatter);

        $log = new Zend_Log($writer);
        $log->log(json_encode($data), 0);
    }

    protected function _prepareArgs($args)
    {
        $result = array();
        if (!is_array($args)) {
            if (is_object($args)) {
                $args = '[object] '.get_class($args);
            }

            return $args;
        }

        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $value = '[object] '.get_class($value);
            } elseif (is_array($value)) {
                $value = $this->_prepareArgs($value);
            }

            $result[$key] = $value;
        }

        return $result;
    }

    protected function _preparePath($path)
    {
        return str_replace(Mage::getBaseDir(), '<ROOT>', $path);
    }

    protected function _getSource($file, $lineNumber, $paddingTop = 10, $paddingBottom = 10)
    {
        if (!$file || !is_readable($file)) {
            return false;
        }

        $file = fopen($file, 'r');
        $line = 0;

        $range = array(
            'start' => $lineNumber - $paddingTop,
            'end'   => $lineNumber + $paddingBottom
       );

        $format = '% '.strlen($range['end']).'d';

        $source = '';
        while (($row = fgets($file)) !== false) {
            if (++$line > $range['end']) {
                break;
            }

            if ($line >= $range['start']) {
                $row = htmlspecialchars($row, ENT_NOQUOTES);

                $row = '<span>'.sprintf($format, $line).'</span> '.$row;

                if ($line === $lineNumber) {
                    $row = '<div class="highlight">'.$row.'</div>';
                } else {
                    $row = '<div>'.$row.'</div>';
                }

                $source .= $row;
            }
        }

        fclose($file);

        return $source;
    }
}