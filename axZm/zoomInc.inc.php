<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomInc.inc.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


if(!session_id()){session_start();}

// Fixes for some servers
function axZmFixes(){
	$docRootSave = '';
	if (isset($_SERVER['DOCUMENT_ROOT'])){
		$docRootSave = $_SERVER['DOCUMENT_ROOT'];
	}
	
	unset($_SERVER['DOCUMENT_ROOT']);
	
	if(isset($_SERVER['SCRIPT_FILENAME'])){
		$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'])));
	} 
	
	if(!isset($_SERVER['DOCUMENT_ROOT'])){ 
		if(isset($_SERVER['PATH_TRANSLATED'])){
			$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF'])));
		} 
	}
	
	if(!isset($_SERVER['DOCUMENT_ROOT']) && $docRootSave){
		$_SERVER['DOCUMENT_ROOT'] = $docRootSave; 
	}
	
	if (isset($_SERVER['DOCUMENT_ROOT'])){
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']);
		$_SERVER['DOCUMENT_ROOT'] = str_replace('//','/',$_SERVER['DOCUMENT_ROOT']);
		if (substr($_SERVER['DOCUMENT_ROOT'],-1) == "/"){
			$_SERVER['DOCUMENT_ROOT'] = substr($_SERVER['DOCUMENT_ROOT'],0,-1);
		}
	}
	
	$php_version = phpversion();
	if (intval($php_version) < 5){
		echo "You need PHP 5 to run AJAX-ZOOM. Currently you are running PHP version: ".$php_version;
		exit;
	}
}

axZmFixes();

// Javascript Packer Class
// With this class it is possible to pack and obfuscate output javascripts
require ('axZm.packer.class.php');

/*
	Major class axZm (Ajax Zoom) PHP 5 only
	This class is encrypted
	Do not edit it, it will not work then
*/


// uncomment one of the versions, either ioncube or sourceguardian
require ('axZm.class.ioncube.php'); // ioncube version
//require ('axZm.class.ixed.php'); // sourceguardian version


$axZm = new axZm();


/* Helper class axZmH
This class is opensource
You can edit it if you are knowing, what you are doing
Watch inside for methods and comments
*/
require ('axZmH.class.php');
$axZmH = new axZmH($axZm);


// Include configuration file	
require_once ('zoomConfig.inc.php');

// Include the file wich defines pictures
if (!isset($noObjectsInclude)){
	require_once ('zoomObjects.inc.php');
}
?>
