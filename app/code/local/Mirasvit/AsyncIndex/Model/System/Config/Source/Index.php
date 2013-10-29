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


class Mirasvit_AsyncIndex_Model_System_Config_Source_Index
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array(
            'value' => 0,
            'label' => '',
        );
        $collection = Mage::getModel('index/process')->getCollection();
        foreach ($collection as $index) {
            $result[] = array(
                'value' => $index->getId(),
                'label' => $index->getIndexer()->getName(),
            );
        }
        return $result;
    }
}
