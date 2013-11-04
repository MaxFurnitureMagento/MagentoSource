<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomLoad.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

// turn error reporting off!
error_reporting(0);

if (!headers_sent()){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header('Content-type: text/html; charset=UTF-8');
}else{
	exit;
}

ignore_user_abort(true);

// Do not inlude objects if $zoom['config']['cropNoObj'] = true;
if ( isset($_GET['zoomPath']) && isset($_GET['zoomImage']) && isset($_GET['zoomID']) && isset($_GET['str']) ){
	$noObjectsInclude = true;
}

// Include all classes
include_once ("zoomInc.inc.php");

// Test classes are running
if (!is_object($axZm)){
	$text = "The Ajax-Zoom class has not been initialized.";		
	echo "<script type=\"text/javascript\">
		try{
			jQuery.fn.axZm.zoomAlert('".$text."','Error',false);
		}catch(e){
			alert('Error: ".$text."');
		}
	</script>";
	exit;
}

elseif ( isset($_GET['setHW']) && isset($_GET['zoomID']) ){
	$setHW = true;
}

elseif ( isset($_GET['zoomLoadAjax']) ){
	echo $axZmH->drawZoomBox($zoom, $zoomTmp);
	echo $axZmH->drawZoomJsConf($zoom, $rn = false, $pack = true);
}

elseif ( isset($_GET['loadZoomAjaxSet']) ){
	echo $axZmH->drawZoomJsGallerySet($zoom, $rn = false, $pack = true);
}

elseif ( isset($_GET['zoomID']) && isset($_GET['str']) ){
	ignore_user_abort(false);
	ob_start();
	echo $axZm->zoomReturnCrop($zoom);
	ob_end_flush();
}

elseif (isset($_GET['getBackColor']) && isset($_GET['zoomID'])){
	echo $axZm->getBackColor($zoom, $_GET['zoomID']);
}

// Show an image on the fly
// $zoom['config']['allowDynamicThumbs']
elseif ($zoom['config']['allowDynamicThumbs'] && isset($_GET['previewPic']) && isset($_GET['previewDir']) && isset($_GET['qual']) && isset($_GET['width']) && isset($_GET['height'])){
	
	// Max 120px for hight / width
	if (!isset($zoom['config']['allowDynamicThumbsMaxSize'])){
		$zoom['config']['allowDynamicThumbsMaxSize'] = 120;
	}
	if ($_GET['width'] > $zoom['config']['allowDynamicThumbsMaxSize']){$_GET['width'] = $zoom['config']['allowDynamicThumbsMaxSize'];}
	if ($_GET['height'] > $zoom['config']['allowDynamicThumbsMaxSize']){$_GET['height'] = $zoom['config']['allowDynamicThumbsMaxSize'];}
	
	ob_start();
	
	// Relative paths
	$zoomTmp['fromPath'] = str_replace(array('http://', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '', isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : ''), '', $_SERVER['HTTP_REFERER']);

	// Relative paths correction
	if ($zoomTmp['fromPath'] && substr(urldecode($_GET['previewDir']),0, 3) == '../'){
		$zoomTmp['zoomDirInfo'] = pathinfo($axZmH->checkSlash(dirname(dirname($zoomTmp['fromPath'])).substr(urldecode($_GET['previewDir']),2),'add'));
		if (!is_dir($axZmH->checkSlash($zoom['config']['fpPP'].$axZmH->checkSlash(dirname(dirname($zoomTmp['fromPath'])).substr(urldecode($_GET['previewDir']),2),'add'),'add'))){
			unset($zoomTmp['zoomDirInfo']);
		}
	}
	
	if ($zoomTmp['zoomDirInfo']){
		$_GET['previewDir'] = $axZmH->checkSlash('/'.$zoomTmp['zoomDirInfo']['dirname'].'/'.$zoomTmp['zoomDirInfo']['basename'], 'add');
	}
	
	$path = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['installPath'].'/'.urldecode($_GET['previewDir']),'add');
	
	if (!is_dir($path)){
		$path = $axZmH->checkSlash($zoom['config']['fpPP'].'/'.urldecode($_GET['previewDir']),'add');
	}
	
	$enlarge = false;
	if (isset($_GET['enlarge']) && $_GET['enlarge'] != 'false' && $_GET['enlarge'] != 'no'){
		$enlarge = true;
	}
	
	if ($axZmH->isValidPath($path) && $axZmH->isValidFilename(urldecode($_GET['previewPic']), true)){
		if ( file_exists( $path . urldecode($_GET['previewPic']) )){
			$axZm->rawThumb($zoom, $path, urldecode($_GET['previewPic']), intval($_GET['width']), intval($_GET['height']), intval($_GET['qual']), true, false, $enlarge);
		}
	}else{
		echo "Something is wrong here.";
	}
	
	ob_end_flush();
}

else{
	echo "<TABLE WIDTH='100%' HEIGHT='100%'><TR><TD valign='middle' align='center'><DIV style='width: 500px; border: #000000 3px double; padding: 10px; font-size: 18px; text-align: left;'>";
	echo "ERROR<BR /><BR />";
	echo "This file is a part of a program and can not be called directly.<BR />";
	echo "For security reasons some information has been logged. <UL><LI>IP Address: <SPAN STYLE='color:red'>".$_SERVER['REMOTE_ADDR']."</SPAN></LI><LI>Date: ".date('Y-m-d')."</LI><LI>Time: ".date('H:i:s')."</LI></UL>";
	echo "</DIV></TD></TR></TABLE>";
	// You can log it to db, file or whatever...
}

?>
