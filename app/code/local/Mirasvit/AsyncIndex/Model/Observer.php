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


class Mirasvit_AsyncIndex_Model_Observer
{
    public function process()
    {
        if (!Mage::getStoreConfig('asyncindex/general/cronjob')) {
            return $this;
        }
        set_time_limit(36000);

        try {
            $handler = Mage::getModel('asyncindex/handler');
            $handler->processReindex();
            $handler->processQueue();
            $handler->validateProductIndex();
            $handler->validateCategoryIndex();
        } catch (Exception $e) {
            Mage::helper('mstcore/logger')->logException($this, $e);
        }
    }
}