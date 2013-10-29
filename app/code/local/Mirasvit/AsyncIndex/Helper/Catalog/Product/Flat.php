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


class Mirasvit_AsyncIndex_Helper_Catalog_Product_Flat extends Mage_Catalog_Helper_Product_Flat
{
    public function isEnabled($store = null)
    {
        $store = Mage::app()->getStore($store);
        if ($store->isAdmin()) {
            return false;
        }
        
        if (!isset($this->_isEnabled[$store->getId()])) {
            if (Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_FLAT, $store)) {
                $this->_isEnabled[$store->getId()] = true;
            } else {
                $this->_isEnabled[$store->getId()] = false;
            }
        }
        
        return $this->_isEnabled[$store->getId()];        
    }
}