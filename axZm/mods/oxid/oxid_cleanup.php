<?php 
/**
* Plugin: jQuery AJAX-ZOOM, Oxide PHP helper file: oxid_cleanup.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

exit;

$oxidPath = str_replace('/+','/',str_replace('axZm/mods/oxid','',realpath('.')));
function getShopBasePath(){
	global $oxidPath;
    return $oxidPath.'/';
}

// custom functions file
require getShopBasePath() . 'modules/functions.php';
require_once getShopBasePath() . 'core/oxfunctions.php';
$myConfig = oxConfig::getInstance();

$dbHost = $myConfig->getConfigParam('dbHost');
$dbName = $myConfig->getConfigParam('dbName');
$dbUser = $myConfig->getConfigParam('dbUser');
$dbPwd = $myConfig->getConfigParam('dbPwd');

// Connect to the database
$sqlConnect = mysql_connect($dbHost, $dbUser, $dbPwd);
if (!$sqlConnect){ die('Could not connect: ' . mysql_error());}
$dbSelect = mysql_select_db ($dbName, $sqlConnect);
if (!$dbSelect) { die ('Can not use your_db_name: ' . mysql_error());}

// Include AJAX-ZOOM helper class
require ( str_replace('//', '/', $oxidPath.'axZm/axZmH.class.php') );
$axZmH = new axZmH($axZm);

// Include AJAX-ZOOM configuration file
$_GET['example'] = 'oxid';
require($oxidPath.'/axZm/zoomConfig.inc.php');

$arrayFiles = array();

// Select all product images
$q = mysql_query("SELECT * FROM oxarticles");
while ($r = mysql_fetch_assoc($q)){
	for ($i = 1; $i<=12; $i++){
		if ($r['OXPIC'.$i]){
			$arrayFiles[] = $axZmH->getf('.', $r['OXPIC'.$i]);
		}
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
			// There will be nothing deleted created by oxid, only what was created by AJAX-ZOOM!
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
