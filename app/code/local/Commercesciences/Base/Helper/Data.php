<?php
/*
 * Class for functions that are used in different parts of the module. E.g: functions for API calls
 */
class Commercesciences_Base_Helper_Data extends Mage_Core_Helper_Abstract{

    const STEP_ZERO = 0;
    const STEP_ONE = 1;
    const STEP_TWO = 2;

    const CONFIG_EMAIL = 'commercesciences/required_param/email';
    const DEFAULT_MYSQL_TIMEOUT = 10;

    protected $_step = null;
    protected $_firstStore = null;

    /**
     * handles the errors
     */
    public function handleError($error){
        // TODO Ron Gross 2/1/2013: Inline this function
        throw new Exception($error);
    }

    /**
     * return the current step of the system (can be 0,1 or 2)
     *
     * @return int
     */
    public function getStep(){
        // TODO Ron Gross 2/1/2013 - reverse this if

        if(!$this->_step){
            // Note: "1" is the ID of the singleton Model "config"
            $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
            if(!$csConfig->getUserId()){
                // No user ID --> Haven't registered yet, step 0.
                $this->_step = Commercesciences_Base_Helper_Data::STEP_ZERO;
                return $this->_step;
            }

            //we have userId, means that we are on step 1 or 2
            $aStateArr = $this->getActiveState();
            if($aStateArr['error'] != false){
                $this->handleError($aStateArr['error']);
                // TODO Ron Gross 2/1/2013 - remove this 'return' (above statement just throws an exception)
                return;
            }
            if($aStateArr['data'] == 'NotConfigured'){
                $this->_step = Commercesciences_Base_Helper_Data::STEP_ONE;
            }elseif($aStateArr['data'] == 'Hidden' || $aStateArr['data'] == 'Visible'){
                $this->_step = Commercesciences_Base_Helper_Data::STEP_TWO;
            }else{
                $error = $this->__("Error ocurred. Your updates weren't saved. Please contact ComemrceScience for support (error id: 005)");
                throw new Exception($error);
                return;
            }
        }
        return $this->_step;
    }

    /**
     * converts the first layer of stdObject to array
     * @param stdObject $stdObj
     * @return array
     */
    public function stdObject2Array($stdObj) {
        if (is_object($stdObj)){
            $stdObj = get_object_vars($stdObj);
        }
        return $stdObj;
    }

    /**
     * register the email + domain on commercesciences
     *
     * @return array - format [error] => ''
     */
    public function register($email, $storeUrl){
        // TODO Ron Gross 2/1/2013 - refactor into a method
        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
        $RESTClient = new Zend_Rest_Client($csConfig->getCsApiUrl());

        $httpClient = $RESTClient->getHttpClient();
        $httpClient->setConfig(array(
            "timeout" => 30
        ));

        try {
            $timeBeforeApiCall = time();

            //get the extension version
            $extVersion = Mage::getResourceSingleton('core/resource')->getDbVersion('cs_setup');

            $response = $RESTClient->restPost("/magento/registerPost", array('email' => $email, 'storeURL'=>$storeUrl, 'platformVersion' => Mage::getVersion(), 'extensionVersion' => $extVersion));
            $responseJson = $response->getBody();

            $timeAfterApiCall = time();

            $parsedResponseArr = $this->stdObject2Array(json_decode($responseJson));
            if(!isset($parsedResponseArr['good'])){
                //timeout occured
                return array('error' => $this->__("The CommerceSciences server is currently busy, your updates weren't saved. Please try again later. (error id: 003)"));
            }
            if($parsedResponseArr['good'] == false){
                if(isset($parsedResponseArr['fieldErrors']) && $parsedResponseArr['fieldErrors']){
                    $fieldErrorsArr = $this->stdObject2Array($parsedResponseArr['fieldErrors']);
                    $errorMsg = '';
                    foreach($fieldErrorsArr as $field => $fError){
                        $errorMsg .= "<br />";
                        $errorMsg .= $this->__($field).": ".$this->__($fError);
                    }
                    return array('error' => $errorMsg);
                }elseif(isset($parsedResponseArr['globalError']) && $parsedResponseArr['globalError']){
                    return array('error' => $this->__($parsedResponseArr['globalError']));
                }
            }
            $parsedResponse = $this->stdObject2Array($parsedResponseArr['data']);

            if(!isset($parsedResponse['securityToken']) || !$parsedResponse['securityToken']
                || !isset($parsedResponse['userID']) || !$parsedResponse['userID']
                || !isset($parsedResponse['tag']) || !$parsedResponse['tag']){
                return $this->__("Error ocurred. Your updates weren't saved. Please contact ComemrceScience for support (error id: 001)");
            }

            if($timeAfterApiCall-$timeBeforeApiCall > self::DEFAULT_MYSQL_TIMEOUT){
                //pobably mysql serer has gone away, save data in the session

                $adminSession = Mage::getModel('customer/session');
                $adminSession->setCsUserId($parsedResponse['userID']);
                $adminSession->setCsSecurityToken($parsedResponse['securityToken']);
                $adminSession->setCsTag($parsedResponse['tag']);
            }else{
                $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
                if(!$csConfig){
                    $csConfig = Mage::getModel("commercesciences_base/config");
                }
                $csConfig->setSecurityToken($parsedResponse['securityToken'])
                    ->setUserId($parsedResponse['userID'])
                    ->setTag($parsedResponse['tag'])
                    ->save();
            }


            $message = '';
            if(isset($parsedResponse['message']) && $parsedResponse['message']){
                $message = $parsedResponse['message'];
            }

            return array('error' => false, 'message' => $message);

        }catch(Exception $e){
            //timeout or other unhandled exception was thrown
            return array('error' => $this->__($e->getMessage()));
        }
    }

    /**
     * Change the show/hide state of the bar
     *
     * @param int $changeTo
     * @return array
     */
    protected function changeActiveState($changeTo=1){

        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
        if(!$csConfig || !$csConfig->getUserId() || !$csConfig->getSecurityToken()){
            //we must be already at least on step 1, so the DB record has to exist
            return array('error' => $this->__("Error ocurred. Your updates weren't saved. Please contact ComemrceScience for support (error id: 002)"));
        }

        // TODO Ron Gross 2/1/2013 - refactor into a method
        $RESTClient = new Zend_Rest_Client($csConfig->getCsApiUrl());

        $httpClient = $RESTClient->getHttpClient();
        $httpClient->setConfig(array(
            "timeout" => 30
        ));

        try{
            if($changeTo){
                $response = $RESTClient->restPost("/magento/showBarPost", array('userID' => $csConfig->getUserId(), 'securityToken'=>$csConfig->getSecurityToken()));
            }else{
                $response = $RESTClient->restPost("/magento/hideBarPost", array('userID' => $csConfig->getUserId(), 'securityToken'=>$csConfig->getSecurityToken()));
            }

            $responseJson = $response->getBody();

            $parsedResponseArr = $this->stdObject2Array(json_decode($responseJson));
            if(!isset($parsedResponseArr['good'])){
                return $this->__("The CommerceSciences server is currently busy, your updates weren't saved. Please try again later.  (error id: 004)");
            }
            if($parsedResponseArr['good'] == false){
                if(isset($parsedResponseArr['fieldErrors']) && $parsedResponseArr['fieldErrors']){
                    $fieldErrorsArr = $this->stdObject2Array($parsedResponseArr['fieldErrors']);
                    $errorMsg = '';
                    foreach($fieldErrorsArr as $field => $fError){
                        $errorMsg .= "<br />";
                        $errorMsg .= $this->__($field).": ".$this->__($fError);
                    }
                    //remove the last comma
                    $errorMsg = substr($errorMsg, 0, strlen($errorMsg)-1);
                    return array('error' => $errorMsg);
                }elseif(isset($parsedResponseArr['globalError']) && $parsedResponseArr['globalError']){
                    return array('error' => $this->__($parsedResponseArr['globalError']));
                }
            }

            return array('error'=> false, 'data' => ($parsedResponseArr['data']==true ? 1 : 0));


        }catch(Exception $e){
            //timeout or other unhandled exception was thrown
            return array('error' => $this->__($e->getMessage()));
        }
    }

    /**
     * activate the CS bar
     *
     * @return array
     */
    public function activate(){
        $this->changeActiveState(1);
    }

    /**
     * deactivate the CS bar
     *
     * @return array
     */
    public function deactivate(){
        $this->changeActiveState(0);
    }

    /**
     * get commercesciences tag
     *
     * @return string
     */
    public function getTag(){
        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
        if(!$csConfig){
            return '';
        }
        return ($csConfig->getTag() ? $csConfig->getTag() : '');
    }

    /**
     * check whether the bar is active
     *
     * @return array
     */
    public function getActiveState(){

        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");

        if(!$csConfig){
            //we must be already at least on step 1, so the DB record has to exist
            Mage::log("Error - no csConfig");
            return array('error' => $this->__("Error ocurred. Your updates weren't saved. Please contact ComemrceScience for support (error id: 005)"));
        }

        Mage::log("csConfig=" . print_r($csConfig, true), true);

        // TODO Ron Gross 2/1/2013 - refactor into a method
        $RESTClient = new Zend_Rest_Client($csConfig->getCsApiUrl());

        $httpClient = $RESTClient->getHttpClient();
        $httpClient->setConfig(array(
            "timeout" => 30
        ));

        try{
            $response = $RESTClient->restGet("/magento/getBarStatus", array('userID' => $csConfig->getUserId(), 'securityToken'=>$csConfig->getSecurityToken()));

            $responseJson = $response->getBody();

            $parsedResponseArr = $this->stdObject2Array(json_decode($responseJson));


            if(!isset($parsedResponseArr['good'])){
                Mage::log("Server busy");
                return array('error' => $this->__("The CommerceSciences server is currently busy, your updates weren't saved. Please try again later.  (error id: 006)"));
            }
            if($parsedResponseArr['good'] == false){
                if(isset($parsedResponseArr['fieldErrors']) && $parsedResponseArr['fieldErrors']){
                    $fieldErrorsArr = $this->stdObject2Array($parsedResponseArr['fieldErrors']);
                    $errorMsg = '';
                    foreach($fieldErrorsArr as $field => $fError){
                        $errorMsg .= "<br />";
                        $errorMsg .= $this->__($field).": ".$this->__($fError);
                    }
                    $errorMsg = substr($errorMsg, 0, strlen($errorMsg)-1);
                    Mage::log("Error (fieldErrors) - " . $errorMsg);
                    return array('error' => $errorMsg);
                }elseif(isset($parsedResponseArr['globalError']) && $parsedResponseArr['globalError']){
                    Mage::log("Error (globalError) - " . $parsedResponseArr['globalError']);
                    return array('error' => $parsedResponseArr['globalError']);
                }
            }
            Mage::log("Returning data: " . $parsedResponseArr['data']);
            return array('error'=> false, 'data' => $parsedResponseArr['data']);
        }catch(Exception $e){
            Mage::log("Got error: " . print_r($e, true));
            return array('error' => $this->__($e->getMessage()));
        }
    }

    /*
     * get a code of the first store in the magento installation
     */
    public function getFirstStoreCode(){
        if(!$this->_firstStore){
            foreach (Mage::app()->getWebsites() as $website) {
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    foreach ($stores as $store) {
                        $this->_firstStore =  $store->getCode();
                        break;
                    }
                }
            }
        }
        return $this->_firstStore;
    }
}