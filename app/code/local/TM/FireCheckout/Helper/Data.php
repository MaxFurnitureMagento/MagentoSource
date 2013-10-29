<?php

class TM_FireCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_agreements = null;

    /**
     * Get fire checkout availability
     *
     * @return bool
     */
    public function canFireCheckout()
    {
        return (bool)Mage::getStoreConfig('firecheckout/general/enabled');
    }

    public function isAllowedGuestCheckout()
    {
        return 'optional' == Mage::getStoreConfig('firecheckout/general/registration_mode')
            || 'optional-checked' == Mage::getStoreConfig('firecheckout/general/registration_mode');
    }

    public function getIsSubscribed()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            return false;
        }
        $ids = Mage::getResourceModel('newsletter/subscriber_collection')
            ->useOnlySubscribed()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addFieldToFilter('subscriber_email', $customerSession->getCustomer()->getEmail())
            ->getAllIds();

        return count($ids) > 0;
    }

    public function canShowNewsletter()
    {
        if (!Mage::getStoreConfig('firecheckout/general/newsletter_checkbox')) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/Mage_Newsletter/active');
        if (!Mage::getConfig()->getModuleConfig('Mage_Newsletter')
            || !$isActive
            || !in_array((string)$isActive, array('true', '1'))) {

            return false;
        }

        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()
            && !Mage::getStoreConfig('newsletter/subscription/allow_guest_subscribe')) {

            return false;
        }

        return !Mage::helper('firecheckout')->getIsSubscribed();
    }

    public function canUseCaptchaModule()
    {
        $isExists = (bool)Mage::getConfig()->getModuleConfig('Mage_Captcha');
        if (!$isExists) {
            return false;
        }
        $isActive = Mage::getConfig()->getNode('modules/Mage_Captcha/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve the magento version converted to community edition
     */
    public function getMagentoVersion()
    {
        $version = Mage::getVersion();
        if (!Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')) {
            return $version;
        }

        // $mapping = array(
            // '1.13.0.0' => '1.8.0.0',
            // '1.12.0.2' => '1.7.0.2',
            // '1.12.0.0' => '1.7.0.0',
            // '1.11.2.0' => '1.6.2.0',
            // '1.11.1.0' => '1.6.1.0',
            // '1.11.0.0' => '1.6.0.0',
            // '1.10.0.0' => '1.5.0.0'
        // );
        $info = explode('.', $version);
        $info[1] -= 5;
        $version = implode('.', $info);

        return $version;
    }

    public function canUseMageWorxMultifees()
    {
        $isExists = (bool)Mage::getConfig()->getModuleConfig('MageWorx_MultiFees');
        if (!$isExists) {
            return false;
        }
        $isActive = Mage::getConfig()->getNode('modules/MageWorx_MultiFees/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }
}