<?php
class ClaraStream_Bundleapi_Model_ObjectModel_Api extends Mage_Api_Model_Resource_Abstract {

    public function createLink($pitems, $pselectionRawData, $pproductSku, $storeid) {

        /* reindex data
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection();
    	$processes->walk('setMode', array(Mage_Index_Model_Process::MODE_MANUAL));
        $processes->walk('save');
        */
        
        $selections = array();
        $items      = array();
        $selections = $this->_reassembleArray($pselectionRawData);
        $items      = $this->_reassembleArray($pitems);
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        
        // clear any previous model calls
        Mage::getModel('catalog/product')->unsetData();
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $pproductSku);
        
        // special product updates for bundled products
        $product->setPriceType(0);
        
        // prep for bundle options insert
        Mage::register('product', $product);
        Mage::register('current_product', $product);
        
        $product->setCanSaveConfigurableAttributes(false);
        $product->setCanSaveCustomOptions(true);
        
        $product->setBundleOptionsData($items);
        $product->setBundleSelectionsData($selections);
        $product->setCanSaveCustomOptions(true);
        $product->setCanSaveBundleSelections(true);
        $product->setAffectBundleProductSelections(true);
        
        $product->save();

        /*
        $processes->walk('setMode', array(Mage_Index_Model_Process::MODE_REAL_TIME));
    	$processes->walk('save');
    	$processes->walk('reindexEverything');
        */
        
        $result['product_name'] = $product->getName();
        return $result;
    }

    public function deleteBundleOptions($sku) {
        
        $bundle_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        
        if (!$bundle_product) {
            return false;
        }
        
        // get all the options of your bundle product assumed as $bundle_product
        $optionCollection = $bundle_product->getTypeInstance()->getOptionsCollection($bundle_product);
        
        if (!$optionCollection) {
            return false;
        }
        
        $selectionIds = $optionIds = array();
        
        // and their Id into an array
        foreach ($optionCollection as $opt) {
            $optionIds[] = $opt->getOptionId();
            $opt->delete();
        }
        
        // fetch all the selections from all the previous Ids
        $selectionCollection = $bundle_product->getTypeInstance()->getSelectionsCollection($optionIds);
        // the selections we want to keep
        /*foreach($selectionCollection as $sc) {
            $selectionIds[] = $sc->getSelectionId();
        }*/
        
        try {
            // remove the Selection/Bundle association from database, we need to pass all the others except the one we need to drop
            Mage::getModel('bundle/resource_bundle')->dropAllUnneededSelections($bundle_product->getId(), $selectionIds);
        }
        catch(Exception$e) {
            // nothing
        }
        
        return true;
    }
    

    // pass in an array of objects
    protected function _reassembleArray($array, $return = null) {
        $i = 0;
        $newArray = array();
        foreach ($array as $obj) {
            // recurse through for if we hit an array
            if (is_array($obj)) {
                Mage::log("i: " . $i . "\n");
                $return[$i] = $this->_reassembleArray($obj, $return);
                $i++;
            }
            else {
                // convert skus to product ids
                if ($obj->key == 'product_id') {
                    $obj->value = Mage::getModel("catalog/product")->getIdBySku($obj->value);
                }
                $newArray[$obj->key] = $obj->value;
                $return = $newArray;
            }
        }
        return $return;
    }
}
?>
