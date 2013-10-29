<?php
/**
 * Mxp 
 * Menuadmin
 * 
 * @category    Mxp
 * @package     Mxp_Menuadmin
 * @copyright   Copyright (c) 2011 Mxp (http://www.magentoxp.com)
 * @author      Magentoxp (Mxp)Magentoxp Team <support@magentoxp.com>
 */

class Mxp_Menuadmin_Helper_Data extends Mage_Core_Helper_Abstract
{

	private $outtree = array();
	private $regions = array();

	public function getSelectcat(){
    	if(Mage::getStoreConfig('menuadmin/top/enabled')){
            $this->regions[] = 'top';
    	}
    	if(Mage::getStoreConfigFlag('menuadmin/left/enabled')){
            $this->regions[] = 'left';
    	}
    	if(Mage::getStoreConfigFlag('menuadmin/right/enabled')){
            $this->regions[] = 'right';
    	}
    	if(Mage::getStoreConfigFlag('menuadmin/bottom/enabled')){
            $this->regions[] = 'bottom';
    	}

    	$stores = $this->dataStores();
        if($stores->count() > 0){
        	foreach ($stores as $store){


        		if($store->store_id != 0){
        			foreach($this->regions as $region){
						$this->outtree['value'][] = $store->store_id . '-0-'.$region;
						$this->outtree['label'][] = 'Root - ' . $store->name . ' - ' . $region;
						$this->drawSelect($store->store_id, 0, $region);
        			}
        		}
        	}
        }

        if(!empty($this->outtree)){
	        foreach ($this->outtree['value'] as $k => $v){
	        	$out[] = array('value'=>$v, 'label'=>$this->outtree['label'][$k]);
	        }
			return $out;
        }
        return array();
	}

	public function drawSelect($store_id=1, $pid=0, $region, $sep=1){
		$spacer = '';
		for ($i = 0; $i <= $sep; $i++){
			$spacer.= ' - ';
		}
		$items = $this->getChildrens($store_id, $pid, $region);
		if(count($items) > 0 ){
			foreach ($items as $item){
				$this->outtree['value'][] = $store_id . '-' . $item['menuadmin_id'] . '-' . $region;
				$this->outtree['label'][] = $spacer . $item['title'];
				$child = $this->getChildrens($store_id, $item['menuadmin_id'], $region);
				if(!empty($child)){
					$this->drawSelect($store_id, $item['menuadmin_id'], $region, $sep + 1);
				}
			}
		}
		return;
	}

	public function getChildrens($store_id=1, $pid=0, $region){
		$out = array();
        $collection = Mage::getModel('menuadmin/menuadmin')->getCollection()
        	->addFieldToFilter('pid', array('in'=>$pid) )
        	->addFieldToFilter('region', array('in'=>$region) )
        	->addFieldToFilter('store_id', array('in'=>$store_id) )
			->addFieldToFilter('status', array('in'=>'1') )
			->setOrder('position', 'asc');
		foreach ($collection as $item){
			$out[] = $item->getData();
		}
		return $out;
	}

    public function getStores(){
    	$out = array();
    	$stores = $this->dataStores();
        if($stores->count() > 0){
        	$i = 0;
        	foreach ($stores as $store){
        		if($store->store_id != 0){
					$out[$i]['value'] = $store->store_id;
					$out[$i]['label'] = $store->name;
					$i++;
        		}
        	}
        }
        return $out;
    }

    public function dataStores(){
    	$stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
         return $stores;
    }
}