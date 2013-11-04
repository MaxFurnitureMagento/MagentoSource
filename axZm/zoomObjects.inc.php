<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomObjects.inc.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

if(!session_id()){session_start();}

// INTRODUCTION:
// This file is responsible for generating a PHP array with images, that have to be zoomed.
// Please find more information and examples about this file here:
// http://www.ajax-zoom.com/index.php?cid=docs


/*****************************************/
/*** STEP 1 - DEFINING THE IMAGE ARRAY ***/
/*****************************************/

///////////////////////////////////////////////////////////////////////////////////////////
// Passing PHP array over query string that have been encoded and compresed Ver. 2.1.2+ ///
///////////////////////////////////////////////////////////////////////////////////////////

// Ver. 3.2.1+ Alternatively to encoded array with filepaths and filenames you can 
// pass zoomData as string separated by | e.g. '/someFolder/someFile.jpg|/someOtherFolder/someOtherFile.jpg'

if (isset($_GET['zoomData'])){
	
	// Check validity of the passed file name
	// Patch: 2010-12-21
	if (isset($_GET['zoomFile'])){
		
		$_GET['zoomFile'] = $axZmH->uncompress($_GET['zoomFile'], true);
		
		$_GET['zoomDir'] = $axZmH->checkSlash(dirname($_GET['zoomFile']), 'add');
		$_GET['zoomFile'] = basename($_GET['zoomFile']);
		
		if (!$axZmH->isValidFilename($_GET['zoomFile'], true)){
			unset($_GET['zoomFile']);
		}
	}
	
	// Decode and uncompress data array
	$_GET['zoomData'] = $axZmH->uncompress($_GET['zoomData'], false);
	
	if (is_array($_GET['zoomData'])){
		$pic_list_array = array();
		$pic_list_data = array(); 
		$zoomTmp = array();
		
		foreach ($_GET['zoomData'] as $k=>$v){
			// Check data array
			if (!$axZmH->isValidFilename($v['f'], true) OR !$axZmH->isValidPath($v['p'])){
				unset($_GET['zoomData'][$k]);
			}else{
				// Fill $pic_list_array and $pic_list_data
				$pic_list_array[$k] = $v['f'];
				$pic_list_data[$k]['path'] = $v['p'];			
				
				if (!$zoomTmp['zoomDirFound'] AND isset($_GET['zoomDir'])){
					if (urldecode($_GET['zoomDir']) == $v['p']){
						$zoomTmp['zoomDirFound'] = true;
					}
				}
			}
		}
		
		// Unset zoomDir if not found above
		if (!$zoomTmp['zoomDirFound'] AND isset($_GET['zoomDir'])){
			unset ($_GET['zoomDir']);
		}
		
		// Choose the first folder if zoomDir ($_GET['zoomDir']) is not passed or has been unset above
		if (!isset($_GET['zoomDir']) AND is_array($_GET['zoomData'])){
			reset($_GET['zoomData']);
			$_GET['zoomDir'] = $_GET['zoomData'][key($_GET['zoomData'])]['p'];
		}	

		// Shops Hack
		if (in_array($_GET['example'], array('magento', 'oxid', 'xtc'))){
			// Remove gallery for shops
			if (count($pic_list_array) == 1){
				$zoom['config']['galleryNavi'] = false;
				$zoom['config']['useFullGallery'] = false;
				$zoom['config']['useGallery'] = false;
				$zoom['config']['useHorGallery'] = false;
			}
	
			if (count($pic_list_array) <= 3){
				$zoom['config']['galleryScrollbarWidth'] = 0; 
				$zoom['config']['galleryPlayButton'] = false; 
			} else{
				$zoom['config']['galleryScrollbarWidth'] = 10;
			}
		}

	}
	else {
		unset($_GET['zoomData']);
	}
}

elseif (isset($_GET['3dDir'])){
	$pic_list_array = array();
	$pic_list_data = array(); 
	$zoomTmp = array();	
	
	$_GET['3dDir'] = urldecode($_GET['3dDir']);
	if ( !$axZmH->isValidPath($_GET['3dDir']) ){
		unset ($_GET['3dDir']);
	}else{
		// $zoom['config']['pic'] from zoomConfig.inc.php as basePath
		$zoom['config']['pic'] = $axZmH->checkSlash($zoom['config']['pic'].'/'.$_GET['3dDir'],'add');
		$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['pic'], 'add');
		
		if (!is_dir($zoom['config']['picDir'])){
			$zoom['config']['pic'] = $axZmH->checkSlash('/'.$_GET['3dDir'],'add');
			$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['pic'], 'add');
		}
		
		// Ver. 3.1.0+
		if ($_GET['zoomCueFrames']){
			$zoom['config']['cueFrames'] = $axZmH -> testCSV($_GET['zoomCueFrames'], ',', 'int');
		}
		
		// Open all files
		if (is_dir($zoom['config']['picDir'])){
			$handle = opendir($zoom['config']['picDir']);
			
			// Loop through each file in $zoom['config']['picDir']
			$n=0;
			while (false !== ($file = readdir($handle))){ 
				// Filetype check
				if ( $axZmH->isValidFileType($file) ){
					// Add filename to the pic_list_array with the index $n ($n >= 1)
					$n++; $pic_list_array[$n] = $file;
				}
			}
			// Close directory
			closedir($handle);
			// Sort piclist by filename if you want, (not necessary)
			$pic_list_array = $axZmH->natIndex($pic_list_array);
		}
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
// Procedure used in most examples with the "base folder" $zoom['config']['pic'] and subfolders in it ///
/////////////////////////////////////////////////////////////////////////////////////////////////////////
else {

	// The key (zoomID) should be an integer > 0 and does not have to be 1,2,3... it could also be 1254, 5846, 352 - whatever...
	$pic_list_array = array();
	
	// $pic_list_data is a "multidimensional" array which contains more information about the source files
	$pic_list_data = array(); 
	
	// Temp array
	$zoomTmp = array();

	$zoomTmp['folderArray'] = array(); // Create empty array for folders
	
	// Ver. 3.2.1 Patch: 2011-06-14 - Hack abolut or relative paths
	if (isset($_GET['zoomDir'])){
		$_GET['zoomDir'] = urldecode(str_replace('\\','/',$_GET['zoomDir']));
		
		if (strstr($axZmH->checkSlash($_GET['zoomDir'], 'remove'), '/')){
			if (substr($_GET['zoomDir'],0, 2) == './' || substr(strtolower($_GET['zoomDir']),0, 2) == 'c:'){
				$_GET['zoomDir'] = substr($_GET['zoomDir'], 2);
			}

			$_GET['zoomDir'] = str_replace(array('http://', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '', isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : ''), '', $_GET['zoomDir']);
			
 			if (is_dir($axZmH->checkSlash($zoom['config']['fpPP'].$_GET['zoomDir'],'add'))){
				$zoomTmp['zoomDirInfo'] = pathinfo( $axZmH->checkSlash($_GET['zoomDir'], 'remove'));
				
			}else{
				// Relative paths
				if ($_SERVER['REQUEST_URI'] && substr($_GET['zoomDir'],0, 3) == '../'){
					$zoomTmp['zoomDirInfo'] = pathinfo($axZmH->checkSlash(dirname(dirname($_SERVER['REQUEST_URI'])).substr($_GET['zoomDir'],2),'add'));
					if (!is_dir($axZmH->checkSlash($zoom['config']['fpPP'].$axZmH->checkSlash(dirname(dirname($_SERVER['REQUEST_URI'])).substr($_GET['zoomDir'],2),'add'),'add'))){
						unset($zoomTmp['zoomDirInfo']);
					}
				} 
				
				elseif ($_SERVER['REQUEST_URI'] && substr($_GET['zoomDir'],0, 1) != '/'){
					$zoomTmp['zoomDirInfo'] = pathinfo($axZmH->checkSlash(dirname(dirname($_SERVER['REQUEST_URI'])).'/'.$_GET['zoomDir'],'add'));
					if (!is_dir($axZmH->checkSlash($zoom['config']['fpPP'].$axZmH->checkSlash(dirname(dirname($_SERVER['REQUEST_URI'])).'/'.$_GET['zoomDir'],'add'),'add'))){
						unset($zoomTmp['zoomDirInfo']);
					}
				}
			}
			
			if ($zoomTmp['zoomDirInfo']){
				$_GET['zoomDir'] = $zoomTmp['zoomDirInfo']['basename'];
				$zoom['config']['pic'] = $axZmH->checkSlash($zoomTmp['zoomDirInfo']['dirname'], 'add');
				$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['pic'],'add');
			}
		}
	}
	
	
	// Open the "base directory" $zoom['config']['picDir'] and choose only folders in it (GLOB_ONLYDIR)
	$n=0; // Start counter
	foreach (glob($axZmH->checkSlash($zoom['config']['picDir'],'add').'*', GLOB_ONLYDIR) as $folder){
		$n++; 
		// Fill folder array with subfolder names
		$zoomTmp['folderArray'][$n] = $axZmH->getl('/',$folder);
	}
	
	// If $_GET['zoomDir'] is not a number (the key in $zoomTmp['folderArray']),
	// try to find the real numeric key and redefine $_GET['zoomDir'] if found
	if (isset($_GET['zoomDir'])){
		if (!is_numeric($_GET['zoomDir']) AND !empty($zoomTmp['folderArray'])){
			// if urldecoded string can be found in the above defined array with folders
			if (in_array(urldecode($_GET['zoomDir']),$zoomTmp['folderArray'])){
				$flipedArray = array_flip($zoomTmp['folderArray']);
				$_GET['zoomDir'] = $flipedArray[urldecode($_GET['zoomDir'])];
			}else{
				unset ($_GET['zoomDir']);
			}
		}
	}
	
	// Choose the first folder if zoomDir ($_GET['zoomDir']) is not passed or has been unset above
	if (!isset($_GET['zoomDir']) AND !empty($zoomTmp['folderArray'])){
		reset($zoomTmp['folderArray']);
		$_GET['zoomDir'] = key($zoomTmp['folderArray']);
	}
	
	// Adjust $zoom['config']['pic'] AND $zoom['config']['picDir']
	if (isset($_GET['zoomDir'])){
		if (!empty($zoomTmp['folderArray']) AND array_key_exists($_GET['zoomDir'],$zoomTmp['folderArray'])){
			// Redefine base folder
			$zoom['config']['pic'] .= $axZmH->checkSlash($zoomTmp['folderArray'][$_GET['zoomDir']],'add');
			// Redefine server path for the pic directory
			$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['pic'],'add');
		}
	}
	
	
	// Open the above defined (in zoomConfig.php or here) directory with images
	$handle = opendir($zoom['config']['picDir']);
	
	// Loop through each file in $zoom['config']['picDir']
	$n=0;
	while (false !== ($file = readdir($handle))){ 
		// Filetype check
		if ( $axZmH->isValidFileType($file) ){
			// Add filename to the pic_list_array with the index $n ($n >= 1)
			$n++; $pic_list_array[$n] = $file;
		}
	}
	// Close directory
	closedir($handle);
	
	// Sort piclist by filename if you want, (not necessary)
	$pic_list_array = $axZmH->natIndex($pic_list_array);
}





/*****************************************************/
/*** STEP 2 - COLLECT INFORMATION ***/
/*****************************************************/

// Loop through the $pic_list_array
// $k is the "zoomID" and $v is the source filename
foreach ($pic_list_array as $k=>$v){
	
	// Store filename under the key 'fileName'
	$pic_list_data[$k]['fileName'] = $v; 

	// We need this information only once at loading process, 
	// not for zooming into an image. AJAX-ZOOM passes the 
	// additional parameter 'str' which means,  
	// that this is a zoom request.
	if (!isset($_GET['str'])){

		// Heuristic approach Ver. 2.1.2+
		if (isset($pic_list_data[$k]['path'])){
			$picPath = $axZmH->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$k]['path'],'add');
			$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$picPath,'add');
			
			// Ver. 3.0.2 Patch: 2010-12-17
			if (!is_dir($zoom['config']['picDir'])){
				$picPath = $axZmH->checkSlash('/'.$pic_list_data[$k]['path'],'add');
				$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$picPath,'add');
				
				// Ver. 3.0.2 Patch: 2010-01-03
				if (!is_dir($zoom['config']['picDir'])){
					$picPath = $axZmH->checkSlash($zoom['config']['installPath'].'/'.$pic_list_data[$k]['path'],'add');
					$zoom['config']['picDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$picPath,'add');
				}
			}
		}

		// Store imagesize under the key 'imgSize' (necessary!!!)
		$pic_list_data[$k]['imgSize'] = $axZm->imageSize($zoom['config']['picDir'].$pic_list_array[$k], $zoom['config']['im']);

		// Store filesize under the key 'fileSize' (not necessary, just for example)
		$pic_list_data[$k]['fileSize'] = filesize($zoom['config']['picDir'].$pic_list_array[$k]);
		
		/* Under the key 'thumbDescr' you can store any short image information that will be shown under the thumb of image gallery
		This is just an example, if image size is important
		*/
		if (!in_array($_GET['example'], array('magento', 'oxid', 'xtc'))){
			$pic_list_data[$k]['thumbDescr'] = $pic_list_data[$k]['imgSize'][0].' x '.$pic_list_data[$k]['imgSize'][1];
			//$pic_list_data[$k]['thumbDescr'] = '';
			
			// Full description
			// $pic_list_data[$k]['fullDescr'] = "Full description [$k]: ".$v.' - '.$pic_list_data[$k]['imgSize'][0].' x '.$pic_list_data[$k]['imgSize'][1].'px, '.round((($pic_list_data[$k]['imgSize'][0]*$pic_list_data[$k]['imgSize'][1])/1000000),1).' MP';
			$pic_list_data[$k]['fullDescr'] = '';
		}
	}
}


// Store information in $zoom['config']
$zoom['config']['pic_list_array'] = $pic_list_array;
$zoom['config']['pic_list_data'] = $pic_list_data;

// Check the existance of the files and generate everything needed on the fly
$proceed = $axZmH->proceedList($zoom, $zoomTmp);
$zoom = $proceed[0]; $zoomTmp = $proceed[1];
$pic_list_array = $zoom['config']['pic_list_array'];
$pic_list_data = $zoom['config']['pic_list_data'];

?>