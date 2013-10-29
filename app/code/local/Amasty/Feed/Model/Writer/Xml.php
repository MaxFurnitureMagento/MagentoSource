<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Feed
*/  
class Amasty_Feed_Model_Writer_Xml extends Amasty_Feed_Model_Writer_Abstract
{
    public function writeRow($row, $lines2fields = array(), $fields = array())
    {
        if (is_array($row)){
        $writes = array();
        $item = $this->config['xml_item'];

        foreach($lines2fields as $lines2field){
            $write = $lines2field['tpl'];
            $skip = TRUE;
            
            if(isset($lines2field['vars'])){
                foreach($lines2field['vars'] as $varOrder => $var){
                    $link = $lines2field['links'][$varOrder];
                    $optional = isset($fields['optional'][$link]) ? ($fields['optional'][$link] == 'yes') : FALSE;
                    $value = $row[$link];
                    
                    $skip = $skip && $optional && $value == '';
                                        
                    if (!$skip)
                        $write = str_replace('{' . $var . '}', $value, $write);
                    else 
                        $write = '';
                    
        }
                
                $writes[] = $write;
            }
            
        }
        
        fwrite($this->fp, "<" . $item . ">" . implode('', $writes) . "</" . $item . ">");
        } else {
            fwrite($this->fp, $row); //header / footer
        }
//        print_r($row);
//        print_r($lines2fields);
//        exit(1);
//        $item = $this->config['xml_item'];
//        if (is_array($row)) {
//            $row = "<" . $item . ">" . implode("", $row) . "</" . $item . ">";
//        }
//        fwrite($this->fp, $row);
    }    
}