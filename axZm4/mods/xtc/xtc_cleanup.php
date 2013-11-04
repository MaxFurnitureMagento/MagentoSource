<?php 
/**
* Plugin: jQuery AJAX-ZOOM, xt:Commerce PHP helper file: xtc_cleanup.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

// Remove exit to use this file
exit;

// XTC install directory
$xtcPath = str_replace('/+','/',str_replace('axZm/mods/xtc','',realpath('.')));

$veyton = false;

// Include XTC
if (file_exists(str_replace('//', '/', $xtcPath.'/includes/configure.php'))){
	// xt: Commerce 3 ...
	include (str_replace('//', '/', $xtcPath.'/includes/configure.php'));
	include (str_replace('//', '/', $xtcPath.'/includes/database_tables.php'));
	// Connect to the database
	$sqlConnect = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	if (!$sqlConnect){ die('Could not connect: ' . mysql_error());}
	$dbSelect = mysql_select_db (DB_DATABASE, $sqlConnect);
	if (!$dbSelect) { die ('Can not use your_db_name: ' . mysql_error());}	
}else{
	// Veyton
	$veyton = true;
	define('_VALID_CALL', true);
	include (str_replace('//', '/', $xtcPath.'/conf/config.php'));
	include (str_replace('//', '/', $xtcPath.'/conf/database.php'));
	
	// Connect to the database
	$sqlConnect = mysql_connect(_SYSTEM_DATABASE_HOST, _SYSTEM_DATABASE_USER, _SYSTEM_DATABASE_PWD);
	if (!$sqlConnect){ die('Could not connect: ' . mysql_error());}
	$dbSelect = mysql_select_db (_SYSTEM_DATABASE_DATABASE, $sqlConnect);
	if (!$dbSelect) { die ('Can not use your_db_name: ' . mysql_error());}	
}


// Include AJAX-ZOOM helper class
require ( str_replace('//', '/', $xtcPath.'/axZm/axZmH.class.php') );
$axZmH = new axZmH($axZm);

// Include AJAX-ZOOM configuration file
$_GET['example'] = 'xtc';
require($xtcPath.'/axZm/zoomConfig.inc.php');

$arrayFiles = array();

if (!$veyton){
	// Select all products
	$q = mysql_query("SELECT products_image FROM ".TABLE_PRODUCTS);
	while ($r = mysql_fetch_assoc($q)){
		// Add first image to the array
		$arrayFiles[] = substr($r['products_image'], 0, -4);
	}
	
	// Select additional images
	$q = mysql_query("SELECT image_name FROM ".TABLE_PRODUCTS_IMAGES);
	while ($r = mysql_fetch_assoc($q)){
		// Add additional images to the array
		$arrayFiles[] = substr($r['image_name'], 0, -4);
	}
} else{
	// Select additional images
	$q = mysql_query("SELECT file FROM ".TABLE_MEDIA." WHERE type='images' AND class='product'");
	while ($r = mysql_fetch_assoc($q)){
		// Add additional images to the array
		$arrayFiles[] = substr($r['file'], 0, -4);
	}	
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
