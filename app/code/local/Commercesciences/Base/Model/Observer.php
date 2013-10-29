<?php
class Commercesciences_Base_Model_Observer
{
    private $_putcommercesciences = false;
    protected $_csHelper = null;

    /**
     * retrieve the module helper
     * @return Commercesciences_Base_Helper_Data
     */
    protected function getCsHelper(){
        if(!$this->_csHelper){
            $this->_csHelper = Mage::helper("commercesciences_base");
        }
        return $this->_csHelper;
    }

    /**
     * Tries to inject the CS tag to the layout
     *
     * (observer for the event core_block_abstract_to_html_after
     * adds the tag of commercescience to the block html)
     *
     * @param $observer
     * @return mixed
     */
    public function addCommercesciencesHtml($observer)
    {
        if($this->_putcommercesciences){
            return;
        }

        $request = Mage::app()->getRequest();
        if(($request->getRouteName() == "adminhtml" || Mage::app()->getStore()->isAdmin())){
            return;
        }



        $commercesciencesTag = Mage::helper("commercesciences_base")->getTag();
        if(!$commercesciencesTag){
            return;
        }

        $block = $observer->getBlock();
        $transport = $observer->getTransport();

        if($block->getNameInLayout() == "footer"){
            $transportHTML = $transport->getHtml();
            $transport->setHtml($transportHTML.$commercesciencesTag);
            $this->_putcommercesciences = true;
            return;
        }

        if($block->getNameInLayout() == "before_body_end"){
            $transportHTML = $transport->getHtml();
            $transport->setHtml($transportHTML.$commercesciencesTag);
            $this->_putcommercesciences = true;
            return;
        }

    }


    /**
     * Before each save of the configurations (in System->Configuration)
     * check whether some logic should be preformed. E.g: register via API
     *
     * (observer for event model_config_data_save_before)
     *
     * @param $observer
     * @return void
     */
    public function performStepLogic($observer){
        $request = Mage::app()->getRequest();
        $params = $request->getParams();
        $savedConfigSection = $params['section'];
        if($savedConfigSection != 'commercesciences'){
            return;
        }



        $step = $this->getCsHelper()->getStep();
        $csHelper = $this->getCsHelper();

        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
        if(!$csConfig || !$csConfig->getId()){
            $csConfig = Mage::getModel("commercesciences_base/config");
            $csConfig->save();
        }

        $firstStoreCode = $csHelper->getFirstStoreCode();
        $storeUrl =  Mage::app()->getStore($firstStoreCode)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        if($step == Commercesciences_Base_Helper_Data::STEP_ZERO){
            //check valid email
            if(!isset($params['groups']['required_param']['fields']['email']['value'])
                || !trim($params['groups']['required_param']['fields']['email']['value'])){
                $csHelper->handleError($csHelper->__('The email should not be empty'));
                return;
            }
            $email = trim($params['groups']['required_param']['fields']['email']['value']);
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            if(!$email){
                $csHelper->handleError($csHelper->__('The email %s is not valid', $email));
                return;
            }

            //register
            $result = $csHelper->register($email, $storeUrl);
            if($result['error']){
                $csHelper->handleError($result['error']);
                return;
            }

            if($result['message']){
                Mage::getSingleton('adminhtml/session')->addNotice($result['message']);
            }

            //customer registered - clear the cache
            Mage::app()->cleanCache();
        }

        if($step == Commercesciences_Base_Helper_Data::STEP_ONE
            || $step == Commercesciences_Base_Helper_Data::STEP_TWO){
            if(!isset($params['groups']['required_param']['fields']['is_active']['value'])){
                return;
            }
            $newIsActiveConfig = $params['groups']['required_param']['fields']['is_active']['value'];


            if($newIsActiveConfig==0){
                $csHelper->deactivate();
            }else{
                $csHelper->activate();
            }
        }
        return;
    }

    /*
     * Function to the case when the call to register took more than
     * Commercesciences_Base_Helper_Data::DEFAULT_MYSQL_TIMEOUT
     * if so, the mysql save is not performed immediately after the API call to register,
     * but instead the data saved to the session, and afterwards, on the next page load
     * this function is called, and the data is saved here.
     * This way, the "mysql server has gone away" exception is prevented
     */
    public function beforeTabLoaded($observer){
        //clean invalidated cache types
        $invalidatedCacheTypes = Mage::app()->getCacheInstance()->getInvalidatedTypes();
        foreach($invalidatedCacheTypes as $cacheType){
            Mage::app()->getCacheInstance()->cleanType($cacheType);
        }

        $controller_action = $observer->getControllerAction();
        $request = $controller_action->getRequest();
        if($controller_action->getFullActionName() == "adminhtml_system_config_edit"){
            if($request->getParam('section') == 'commercesciences'){
                $adminSession = Mage::getModel('customer/session');
                if($adminSession->getCsUserId()
                    && $adminSession->getCsSecurityToken()
                    && $adminSession->getCsTag()){
                        $csConfig = Mage::getModel("commercesciences_base/config")->load("1");
                        if(!$csConfig || !$csConfig->getId()){
                            $csConfig = Mage::getModel("commercesciences_base/config");
                        }
                        $csConfig->setUserId($adminSession->getCsUserId());
                        $csConfig->setSecurityToken($adminSession->getCsSecurityToken());
                        $csConfig->setTag($adminSession->getCsTag());
                        $csConfig->save();

                        $adminSession->unsetData("cs_user_id");
                        $adminSession->unsetData("cs_security_token");
                        $adminSession->unsetData("cs_tag");

                }
            }
        }
    }

}