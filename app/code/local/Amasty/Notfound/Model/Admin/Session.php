<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */
class Amasty_Notfound_Model_Admin_Session extends Mage_Admin_Model_Session
{
    protected function _fault()
    {
        $serverProtocol = !empty($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header("$serverProtocol 404 Not Found", true, 404);
        exit;        
    }
    
    protected function _config($key, $asNumber=false)
    {
        $key = Mage::getStoreConfig('amnotfound/attempts/' . $key);  
        if ($asNumber)
            $key = intVal($key);
            
        return $key;
    }
    
    protected function _inList($type, $ip)
    {
        $res = false;
        
        $list = $this->_config($type);
        if ($list) {
            $res = in_array($ip, explode(',', $list)); 
        }
        
        return $res;
    }    
    
    protected function _getFailedAttemptsCount($ip)
    {
        $attempts = Mage::getResourceModel('amnotfound/attempt_collection');
        
        $frame = $this->_config('limit_frame', true);
        if ($frame){
            // calculate date in PHP to avoid time differene erros
            // as we need just delta, not absolute value for the timezone
            $frame = date('Y-m-d H:i:s', time() - $frame);
            $attempts->addStartDateFilter($frame);
        }
            
        $limitIp = $this->_config('limit_ip');
        if ($limitIp) 
            $attempts->addIpFilter($ip);
            
        return $attempts->count();
    }
    
    protected function _createLoginResrtiction($ip, $username)
    {
        $error = 'Maximum for failed login attempts has been reached from the IP'
                . $ip . ', last username used: ' . $username;
                
        Mage::log($error); 
        
        if ($this->_config('add_to_blacklist')){
            $blacklist = $this->_config('black');
            if ($blacklist)
                $blacklist = trim($blacklist) . ',' . trim($ip);
            else 
                $blacklist = trim($ip);
                
            $model = Mage::getModel('core/config_data')->load('amnotfound/attempts/black', 'path');    
            $model
                ->setScope('default')
                ->setPath('amnotfound/attempts/black')
                ->setValue($blacklist)
                ->save();             
        }
        
        // possible actions for new versions - send email to the admin
        // and block login feature completely untill the link
        // from the email be visited.
        return true;
    }    
    
    public function login($username, $password, $request = null)
    {
        $ip  = Mage::app()->getRequest()->getClientIp();
        $max = $this->_config('max', true);

        //clear chache, as we store blacklist and whitelist.
        Mage::app()->cleanCache('CONFIG');
        
        if ($max && !$this->_inList('white', $ip)) {
            
            if ($this->_inList('black', $ip)){
                return $this->_fault();    
            }
            
            $attemptCnt = $this->_getFailedAttemptsCount($ip);
            if ($attemptCnt > $max){
                return $this->_fault();   
            }
                        
            if ($attemptCnt == $max){
                $this->_createLoginResrtiction($ip, $username);
                return $this->_fault();
            }
        }
        
        return parent::login($username, $password, $request);
    }

}