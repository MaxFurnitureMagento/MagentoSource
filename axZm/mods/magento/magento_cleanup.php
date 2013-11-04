<?php 

/**
* Plugin: jQuery AJAX-ZOOM, Magento JS helper file: magento_cleanup.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

// Remove this line (exit) to use it
exit;

// Magento install directory
$magePath = str_replace('/+','/',str_replace('axZm/mods/magento','',realpath('.')));

// Include Mage
require ( str_replace('//', '/', $magePath.'/app/Mage.php') );
Mage::app('default');

// Include AJAX-ZOOM helper class
require ( str_replace('//', '/', $magePath.'/axZm/axZmH.class.php') );
$axZmH = new axZmH($axZm);

// Include AJAX-ZOOM configuration file
$_GET['example'] = 'magento';
require($magePath.'/axZm/zoomConfig.inc.php');

// Connect to the database
$db = Mage::getSingleton('core/resource') -> getConnection('core_write');
$table_prefix = Mage::getConfig() -> getTablePrefix();

// Select all images in the database. If required change the query to suit your needs.
$query = $db -> query("SELECT value FROM {$table_prefix}catalog_product_entity_media_gallery GROUP BY value");

// Fetch the query results
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$arrayFiles = array();
$arrayDeleted = array();

// Generate an array only with image names without '.jpg'
foreach ($rows as $k=>$v){
	$arrayFiles[] = substr($axZmH -> getl('/',$v['value']), 0, -4);
}

// Start removing
if (!empty($arrayFiles)){

	// Open the directory with image tiles
	$handle = opendir($zoom['config']['pyrTilesDir']);
	while (false !== ($file = readdir($handle))){ 
		
		// $file is the subfolder with the name of an image without '.jpg'
		// Compare if the subfolder is in the list comming from the database
		if (!in_array($file,$arrayFiles) && $file != '.' && $file != '..'){
			
			// If not remove the tiles, initial (medium sized images) and eventually thumbnails
			// There will be nothing deleted which is created by magento, only what was created by AJAX-ZOOM!
			$axZmH -> removeAxZm($zoom, $file.'.jpg', array('In' => true, 'Th' => true, 'Ti' => true), false);
			
			// Store the filename in an array
			$arrayDeleted[] = $file;
		}
	}
	
	// Return messages
	if (!empty($arrayDeleted)){
		echo count($arrayDeleted).' images have been cleaned.';
	}else{
		echo 'There are no images to be cleaned. All '.count($arrayFiles).' images seem to be used.';
	}
}else{
	echo 'There are no images in the database.';
}

?>
