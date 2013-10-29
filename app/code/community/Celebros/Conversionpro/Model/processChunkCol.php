<?php

	$bExportProductLink = true;
	
	chdir (dirname(__FILE__));
	chdir ('../../../../../');
	require_once('Mage.php');
	umask(0);
	Mage::app();

	function getCalculatedPrice($product)
	{
		//$product = Mage::getModel('catalog/product')->load($product->getId());
		$price = "";
		if ($product->getData("type_id") == "giftcard")
		{
			$min_amount = PHP_INT_MAX;
			$product = Mage::getModel('catalog/product')->load($product->getId());
			if ($product->getData("open_amount_min") != null && $product->getData("allow_open_amount")) $min_amount = $product->getData("open_amount_min");
			foreach($product->getData("giftcard_amounts") as $amount)
			{
				if($min_amount > $amount["value"]) $min_amount = $amount["value"];
			}
			$price =  $min_amount;
		}
		else {
			$price = $product->getPrice();
		}
		if($price == 0){
			$priceModel  = $product->getPriceModel();

			//This fixes a bug with PHP 5.4 that causes the type instance to be simple (the default) instead of the one for bundled.
			$product->setTypeInstance(Mage::getSingleton('catalog/product_type')->factory($product, true), true);

			if($product->getData("type_id") == "bundle"){
				$isgetTotalPrices = is_callable(array($priceModel,'getTotalPrices'));
				
				if (!$isgetTotalPrices)
					list($minimalPriceTax, $maximalPriceTax) = $priceModel->getPrices($product);
				else
					list($minimalPriceTax, $maximalPriceTax) = $priceModel->getTotalPrices($product, null, null, false);
				$price = Mage::app()->getStore(1)->getConfig('conversionpro/export_settings/min_tier_price') ? $minimalPriceTax : $maximalPriceTax;
			}
			elseif($product->getData("type_id") == "grouped")
			{
				$price = $product->getMinimalPrice();
			}
		}

		return number_format($price, 2, ".", ""); 
	}
	
	function logProfiler($msg)
	{
		Mage::log(date("Y-m-d, H:i:s:: ").$msg, null, 'celebros.log',true);
	}
	
	function getProductImage($product, $type)
	{
		$bImageExists = true;
		
		try {
			//deprecated.
			//$url = $product->getMediaConfig()->getMediaUrl($product->getData($type));
			
			// Get image from cache
            if ($type == 'image') {
				$url = $product->getImageUrl();
			} else if ($type == 'thumbnail') {
				$url = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(66);
			}
			
		} catch (Exception $e) {  
			// We get here in case that there is no product image and no placeholder image is set.
			$bImageExists = false;
		}
		
		if (!$bImageExists 
			|| (stripos($url, 'no_selection') != false)
			|| (substr($url, -1) == DS)) {
			
			logProfiler('Warning: '. $type . ' Error: Product ID: '. $product->getEntityId() . ', image url: ' . $url);
			$url = '';
		}
		
		return $url;
	}
	
	/*
	Mage::log('===============');
	Mage::log('Starting export');
	Mage::log(date('Y/m/d H:i:s'));
	Mage::log('Memory usage: '.memory_get_usage(true));
	Mage::log('===============');
	$startTime = time();
	*/

	Mage::app()->setCurrentStore($argv[2]);
	$_fStore = Mage::app()->getStore();
	$_fPath = Mage::app()->getStore(0)->getConfig('conversionpro/export_settings/path').'/'.$_fStore->getWebsite()->getCode().'/'.$_fStore->getCode();

	if (!is_dir($_fPath)) $dir=@mkdir($_fPath,0777,true);
	$filePath = $_fPath . '/' . 'export_chunk_'.$argv[1] . "." . 'txt';

	$fh = fopen($filePath, 'ab');
	if (!$fh) {
		logProfiler('Cannot create file from separate process.');
		exit;
	}
	
	$item = Mage::getModel('conversionpro/cache')->getCollection()->addFieldToFilter('name', 'export_chunk_'.$argv[1])->getFirstItem();
	$rows=json_decode($item->getContent());
	$item->delete();
	$hasData = count($rows);
	
	/*
	Mage::log('# of products is:');
	Mage::log($hasData);
	*/
	
	$str='';
	$productNum=0;
	
	$ids = array();
	foreach ($rows as $row) {
		$ids[] = $row->entity_id;
	}

	//Prepare custom attributes list.
	$customAttributes = json_decode(Mage::getModel('conversionpro/cache')
										->getCollection()
										->addFieldToFilter('name', 'export_custom_fields')
										->getFirstItem()
										->getContent());
	
	$col = Mage::getSingleton('catalog/product')->getCollection()
		->addFieldToFilter('entity_id', array('in' => $ids))
		->setStoreId($_fStore->getGroupId())
		->addAttributeToSelect(array('price', 'image', 'thumbnail', 'type', 'is_salable'))
		->addAttributeToSelect($customAttributes);

	foreach ($col->load() as $product) {
		$values["id"] = $product->getEntityId();
		$values["price"] = Mage::helper('core')->currency(getCalculatedPrice($product), false, false);

		$values["image_link"] = getProductImage($product, 'image');
		$values["thumbnail"] = getProductImage($product, 'thumbnail');
		
		$values["type_id"] = $product->getTypeId();
		$values["product_sku"] = $product->getSku();
		
		$values["is_salable"] = ($product->getIsSalable() == '1') ? "1" : "0";

		$catalog_stockItem = $product->getStockItem();
		$cataloginventory_stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
		
		$values["is_in_stock"] = $cataloginventory_stockItem->getIsInStock() ? "1" : "0";
		$values["qty"] = ((int) $cataloginventory_stockItem->getQty());
		$values["min_qty"] = ((int) $cataloginventory_stockItem->getMinQty());

		if($bExportProductLink)
		{
			$values["link"] = $product->getProductUrl();
		}
		
		//Process custom attributes.
		foreach ($customAttributes as $customAttribute) {
			$values[$customAttribute] = ($product->getData($customAttribute) == "")
				? ""
				: trim($product->getResource()->getAttribute($customAttribute)->getFrontend()->getValue($product), " , ");
		}
		
				
		//Dispatching an event so that custom modules would be able to extend the functionality of the export,
		// by adding their own fields to the products export file.
		Mage::dispatchEvent('conversionpro_product_export', array(
                'values'             => &$values,
                'product'            => &$product,
            ));

		$str.= "^" . implode("^	^",$values) . "^" . "\r\n";
		
		$productNum++;
		
		$product->clearInstance();
		$product->reset();
	}
	
	fwrite($fh, $str);
	
	fclose($fh);
	
	/*
	Mage::log('===============');
	Mage::log('Finished export');
	Mage::log(date('Y/m/d H:i:s'));
	Mage::log('Memory usage: '.memory_get_usage(true));
	Mage::log('Memory peek usage: '.memory_get_peak_usage(true));
	Mage::log('Overall time: '. (time() - $startTime) );
	Mage::log('===============');
	*/
	logProfiler("Exported {$productNum} out of {$hasData} products\n");

?>