<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Writer_Csv extends Amasty_Feed_Model_Writer_Abstract
{
    public function writeRow($row, $lines2fields = array(), $fields = array())
    {
        $encl  = $this->config['csv_enclosure'];
        $delim = $this->config['csv_delimiter'];
        
        $spesialChars = array("\r", "\n", "\t", " ", "\\", $delim);
        
        if ($encl == ' '){ // bing search engine    
            for ($i=0, $n=sizeof($row); $i<$n; ++$i){
                if (str_replace($spesialChars,'', $row[$i]) != $row[$i]){
                    $row[$i] = implode('  ', explode(' ', $row[$i]));    
                }
            }
            fwrite($this->fp, implode($delim, $row) . "\n");            
        } else if ($encl == 'n'){
            
            $spesialChars = array("\r", "\n", "\t", "\\", $delim);
            
            for ($i=0, $n=sizeof($row); $i<$n; ++$i) {
                $row[$i] = str_replace($spesialChars, '', $row[$i]);
            }
            
            fwrite($this->fp, implode($delim, $row) . "\n");            
        }
        else {
            fputcsv($this->fp, $row, $delim, $encl);            
        }
    }     
}