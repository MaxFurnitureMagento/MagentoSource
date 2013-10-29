<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Writer_Abstract 
{
    protected $fp;
    protected $config;
    
    public function init($name, $config=array())
    {
        $flags = $config['create'] ? 'wb' : 'ab';
        
        $this->fp     = fopen($name, $flags);
        if (!$this->fp){
            throw new Exception('Can not create file ' . $name);
        }
        $this->config = $config;
    }
    
    public function writeRow($row, $lines2fields = array(), $fields = array())
    {
        fwrite($this->fp, $row);
    }
    
    public function close()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }
}