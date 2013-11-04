<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomDownload.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

if(!session_id()){session_start();}
error_reporting(0);
if (headers_sent()){exit;}
include_once ("zoomInc.inc.php");

if (isset($_GET['zoomID']) && $zoom['config']['allowDownload']){
	$axZmH->downloadImage($zoom, $_GET['zoomID']);
}elseif (!$zoom['config']['allowDownload']){
	echo 'Download is not allowed.';
	exit;
}

?>
