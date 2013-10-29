<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Notfound_Model_Observer
{
    public function processLogin($observer)
    {
        $username = $observer->getEvent()->getUserName();
        $ip  = Mage::app()->getRequest()->getClientIp();
        
        $attempt = Mage::getModel('amnotfound/attempt');
        $attempt
            ->setDate(date('Y-m-d H:i:s'))
    	    ->setUser($username)
    		->setClientIp($ip)
    	    ->save();
        
        return $this;      
    }    
    
    public function process404($observer)
    {
        $id = $observer->getEvent()->getPage()->getIdentifier(); 
        
        if ($id == Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE)){
            $req = $observer->getEvent()->getControllerAction()->getRequest();
            
            if ($this->inList('ip', $req->getClientIp()))
                return $this;
                
            if ($this->inList('referrer', $req->getServer('HTTP_REFERER')))
                return $this; 
                             
            if ($this->inList('pattern', $req->getRequestUri()))
                return $this;    
            
            $log = Mage::getModel('amnotfound/log');
    		$log->setDate(date('Y-m-d H:i:s'))
    			->setStoreId(Mage::app()->getStore()->getId())
    			->setUrl($req->getHttpHost() . $req->getRequestUri())
    			->setClientIp($req->getClientIp())
    			->setReferer($req->getServer('HTTP_REFERER'))
    	        ->save();	
        }
        
        return $this;
    }
    
    protected function inList($key, $str)
    {
        $pattrns = Mage::getStoreConfig('amnotfound/log/skip_' . $key);
        if ($pattrns){
            $pattrns = explode(',', $pattrns);
            foreach ($pattrns as $value){
                if (false !== strpos($str, trim($value)))
                    return true;
            }
        }         
        return false;
    }
    
    public function checkForErrors()
    {
        $freq  = $this->_config('freq', true);
        if (!$freq)
            return $this;
            
        $email = $this->_config('email');
        if (!$email)
            return $this;

        $hasInterests = false; 
          
        foreach ($this->_getTypes() as $type){
            if ($this->_config('about_' . $type))
                $hasInterests = true;  
        }        
        if (!$hasInterests)
            return $this;        
        
        
        $lastRun = $this->_config('last_run', true);
        if (time() - $lastRun < $freq * 3600)
            return $this;
        
        $txt = '';
        foreach ($this->_getTypes(true) as $type=>$descr){
            if (!$this->_config('about_' . $type))
                continue; 
            $numErrors = Mage::getModel('amnotfound/' . $type)->getCountFrom($lastRun); 
            if ($numErrors){
                $txt .= Mage::helper('amnotfound')->__($descr) . ':' . $numErrors . ";\r\n";      
            }               
        } 
        
        Mage::getModel('core/config_data')
            ->load('amnotfound/notification/last_run', 'path')    
            ->setScope('default')
            ->setPath('amnotfound/notification/last_run')
            ->setValue(time())
            ->save();
        return $this->_sendEmail($txt);        
    }
    
    protected function _sendEmail($txt)
    {
        if (!$txt)
            return $this; 
             
        $hlp = Mage::helper('amnotfound');
        $txt = $hlp->__('Hello,') . "\r\n"
             . $hlp->__('The following errors have been detected at the site:') . "\r\n"
             . $txt . "\r\n"
             . $hlp->__('Please open Admin > Reports > Detect Errors and Missing Pages for more details.') 
             . "\r\n" 
        ;
        $subj = $hlp->__('Magento Errors Summary on ' . date('Y-m-d H:i:s'));
        $subj = '=?utf-8?B?' . base64_encode($subj) . '?=';
        
        $from = $this->_config('from');
        $headers = 'From: ' . $from . "\r\n" 
                 . 'Reply-To: ' . $from . "\r\n" 
                 . 'X-Mailer: PHP/' . phpversion()
        ;   
             
        $email = $this->_config('email');
        mail($email, $subj, $txt, $headers);
        
        return $this; 
        
// todo - change with Zend_Mail?
//        $mail = new Zend_Mail('utf-8');
//            ->addTo($email)
//            ->setSubject($subj);
//            ->setBodyText($txt);
//            ->setFrom('noreply@example.com')
//        $mail->send();           
        
    }    
    
    protected function _getTypes($withLabels=false)
    {   
        $types = array(
            'attempt' => 'Failed Login Attempts', 
            'error'   => 'System Errors', 
            'log'     => 'Not Found Pages', 
        ); 
        if (!$withLabels)
            $types  = array_keys($types);
            
        return $types;
    } 
    
    protected function _config($key, $asNumber=false)
    {
        $key = Mage::getStoreConfig('amnotfound/notification/' . $key);  
        if ($asNumber)
            $key = intVal($key);
            
        return $key;
    }       
    
       
}