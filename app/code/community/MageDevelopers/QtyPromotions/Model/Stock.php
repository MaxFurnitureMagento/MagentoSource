<?php
class MageDevelopers_QtyPromotions_Model_Stock extends Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
{
  public function reindexProducts($productIds)
  {
    parent::reindexProducts($productIds);
    if (!is_array($productIds)) {
        $productIds = array($productIds);
    }
    $parentIds = $this->getRelationsByChild($productIds);
    if ($parentIds) {
        $processIds = array_merge($parentIds, $productIds);
    } else {
        $processIds = $productIds;
    }

    #WDB sync stock with custom attribute;
    $collection = Mage::getModel('cataloginventory/stock_item')->getCollection()
                  ->addProductsFilter($processIds)
                  ;
    foreach($collection as $s)
      {
        Mage::getSingleton('catalog/product_action')
              ->updateAttributes(array($s->getProductId()), array('md_qty_promotions' => $s->getQty()), Mage_Core_Model_App::ADMIN_STORE_ID);

        $p = Mage::getModel('catalog/product')->load($s->getProductId());
        if(($s->getQty() <= 0) && ($p->getMdDisableWhenOutOfStock()))
          {
            $p->setStatus(2);
            $p->save();
            continue;
          }
        
        $product_id = $s->getProductId();
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "REPLACE INTO max_reindex_product SET product_id='$product_id'";
        $write->query($query);
/*
        $productWebsiteIds = $p->getWebsiteIds();

        $rules = Mage::getModel('catalogrule/rule')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($rules as $rule) {
            if (!is_array($rule->getWebsiteIds())) {
                $ruleWebsiteIds = (array)explode(',', $rule->getWebsiteIds());
            } else {
                $ruleWebsiteIds = $rule->getWebsiteIds();
            }
            $websiteIds = array_intersect($productWebsiteIds, $ruleWebsiteIds);
            $rule->applyToProduct($p, $websiteIds);
        }
        
        
        $p->save();
*/
//         return $this;
//         Mage::getSingleton('catalog/product_action')
//             ->updateAttributes(array($s->getProductId()), array('md_qty_promotions' => (int) $s->getQty()), Mage_Core_Model_App::ADMIN_STORE_ID);
//         Mage::log($s->getProductId().' - '.$s->getQty(), null, 'stock.log');
      }

    return $this;

  }
}
