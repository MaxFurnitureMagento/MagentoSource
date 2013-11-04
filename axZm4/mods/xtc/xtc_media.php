<?php 
/**
* Plugin: jQuery AJAX-ZOOM, xt:Commerce PHP helper file: xtc_media.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

/***********************************************************************************************
*************************** 360 DEGREE SPINS ***************************************************
HOW TO: to add 360 degree view simply upload your high resolution images of a spin over FTP 
into '/axZm/pic/zoom3D/[product id]' e.g. '/axZm/pic/zoom3D/123'.  
AJAX-ZOOM will look into this directory and trigger everything else instantly!
 
THINGS TO TAKE ACCOUNT OF: 
1. 	Every image must have an unique filename!!! 
	You could prefix each image of a spin with the product id to ensure the uniqueness.

2. 	When you upload 16, 24, 36, 48, 72 or even more high resolution images - this takes time. 
	To avoid incomplete generation of the animated preview gif and / or image tiles 
	you can upload the spin set into some other folder and move it 
	to '/axZm/pic/zoom3D/[product id]' after all images have been successfully uploaded. 
	Alternatively place an empty file named "upload.txt" into this folder and remove it after 
	the upload is complete. 
**********************************************************************************************/


/**********************************************************************************************
*************************** TEMPLATE SETTINGS *************************************************
PLEASE READ:

Many other settings for AJAX-ZOOM player can be found in /axZm/zoomConfig.inc.php

However: some of the settings set in /axZm/zoomConfig.inc.php 
are overwritten in file /axZm/zoomConfigCustom.inc.php after 
elseif ($_GET['example'] == 'xtc'){

e.g. if you want to change the size of the player look for 
$zoom['config']['picDim'] in /axZm/zoomConfigCustom.inc.php 
after elseif ($_GET['example'] == 'xtc'){
**********************************************************************************************/


// Read some constants id present 
$axZm = $GLOBALS['info_smarty']->_tpl_vars;

/********************************************/
/**************** Settings ******************/
/********************************************/

// Transiotion speed between images
$axZm['transitionSpeed'] = 300;

// Width of preview container
$axZm['smallImageSize']['w'] = PRODUCT_IMAGE_INFO_WIDTH; // Veyton see below line 96+

// Height of preview container
$axZm['smallImageSize']['h'] = PRODUCT_IMAGE_INFO_HEIGHT; // Veyton see below line 96+

// Width of thumbnail under container
$axZm['thumbSize']['w'] = 66; 

// Height of thumbnail under container
$axZm['thumbSize']['h'] = 66;

// 'mouseover' or 'click' for thumbs. 
// if 'mouseover' click on the thumb will open AJAX-ZOOM too
$axZm['thumbSwitch'] = 'click'; // string

// Zoom icon on top of the preview image
$axZm['zoomIcon'] = true; // bool

// Return more views text above the thumbs
$axZm['moreViewsText'] = true; // bool

// Text under the image or false
$axZm['zoomText'] = ($GLOBALS['HTTP_SESSION_VARS']['language_code'] == 'de') ? ('Zum vergrößern auf das Bild klicken') : ('Click on above image to zoom'); 

// More view text
$axZm['morePicsText'] = ($GLOBALS['HTTP_SESSION_VARS']['language_code'] == 'de') ? ('WEITERE ANSICHTEN') : ('MORE VIEWS'); 

// 360 spin thumb position in the gallery along with other images - 'first' or 'last'
$axZm['360Pos'] = 'last'; // string

// 360 spin rotation time
$axZm['sTurn'] = 2.5; // float > 2

/**************** Settings End **************/

?>
<style type="text/css" media="screen"> 
.axZmThumb{
	border: 1px solid #DDDDDD; 
	margin: 0; padding: 0; 
	text-align: center; 
	vertical-align: middle;
	line-height: 0;
}
.axZmContainer{
	overflow: hidden; 
	border:1px solid #CCCCCC;
}

.axZmClickZoomMsg{
	text-align: right; 
	font-size: 80%; 
	position: relative; 
	top: -2px; 
}

.axZmThumbContainer{
	border-top: 1px solid #CCCCCC;
	margin: 0px 0px 10px 0px;
	padding: 0;
}

</style>
<?php

// Veyton patch
if ($GLOBALS['lic_parms']){
	$axZm['more_images'] = $this->get_template_vars('more_images');
	define (DIR_FS_DOCUMENT_ROOT, _SRV_WEBROOT);
	define (DIR_WS_CATALOG, _SRV_WEB);
	define (DIR_WS_THUMBNAIL_IMAGES, _SRV_WEB_IMAGES._DIR_THUMB); 
	define (DIR_WS_ORIGINAL_IMAGES, _SRV_WEB_IMAGES._DIR_ORG);
	define (MO_PICS, count($axZm['more_images']));
	
	$axZm['PRODUCTS_NAME'] = $this->get_template_vars('products_name');
	$axZm['PRODUCTS_ID'] = $this->get_template_vars('products_id');
 
	if ($this->get_template_vars('products_image')){
		$axZm['PRODUCTS_IMAGE'] = _SRV_WEB_IMAGES._DIR_INFO.str_replace('product:', '',  $this->get_template_vars('products_image'));
	}
	
	if (!empty($axZm['more_images'])){
		foreach ($axZm['more_images'] as $k=>$v){
			$axZm['PRODUCTS_IMAGE_'.($k+1)] = _SRV_WEB_IMAGES._DIR_INFO.$v['data']['file'];
		}
	}
	

	$r = mysql_fetch_assoc(mysql_query("SELECT * FROM ".TABLE_IMAGE_TYPE." WHERE folder='info' LIMIT 1"));
	$axZm['smallImageSize']['w'] = $r['width'];
	$axZm['smallImageSize']['h'] = $r['height'];
}


// Gambio old version patch
if (strstr($axZm['tpl_path'], 'gambio')){
	if (isset($axZm['images'][0]['IMAGE']) AND !isset($axZm['PRODUCTS_IMAGE'])){
		$axZm['PRODUCTS_IMAGE'] = $axZm['images'][0]['IMAGE'];
		for ($a = 1; $a <= 9; $a++){
			if (isset($axZm['images'][$a]['IMAGE'])){
				$axZm['PRODUCTS_IMAGE_'.$a] = $axZm['images'][$a]['IMAGE'];
			}
		}
	}
}

// Javascript string
$axZm['javascript'] = '';



// 360 Spinner urls
$axZm['absPathTo360'] = str_replace('//', '/', str_replace('\\', '/', DIR_WS_CATALOG.'/axZm/pic/zoom3D/'.$axZm['PRODUCTS_ID']));
$axZm['basePathTo360'] = str_replace('//', '/', str_replace('\\', '/', DIR_FS_DOCUMENT_ROOT.'/axZm/pic/zoom3D/'.$axZm['PRODUCTS_ID']));
$axZm['thumb360imagePath'] = '';
$axZm['preview360imagePath'] = '';
$axZm['thumb360gallThumb'] = '';


// Check the existence of the folder with spin images
// Do not proceed if a text file named upload.txt exists in the same folder
// This means images are uploading
if (is_dir($axZm['basePathTo360']) AND !file_exists($axZm['basePathTo360'].'/upload.txt')){ 
 	
	// Check if 360 spin gif image has been made
	if (!file_exists(str_replace('//', '/', str_replace('\\', '/', DIR_FS_DOCUMENT_ROOT.'/axZm/pic/zoom3Dgif/'.$axZm['PRODUCTS_ID'].'/'.$axZm['smallImageSize']['w'].'x'.$axZm['smallImageSize']['h'].'/axZmGifAnimation.gif')))){

		// Before the 360 spin gif image is generated show an temp png image
		if (!file_exists(str_replace('//', '/', str_replace('\\', '/', DIR_FS_DOCUMENT_ROOT.'/axZm/pic/zoom3Dgif/temp360Spin_'.$axZm['smallImageSize']['w'].'x'.$axZm['smallImageSize']['h'].'.png')))){
 
			// Small function to make temp images for 360 spin
			// The gif animations are made by ajax request after the page is loaded in /axZm/axZmSpinGif.php
			function makeTemp360SpinImage($w, $h, $x, $y, $fontSize, $base, $target, $text){
				$im = imagecreatetruecolor($w, $h);
				$white = imagecolorallocate($im, 246, 246, 246); // RGB background color (255,255,255 is white)
				$black = ImageColorAllocate ($im, 0, 0, 0); // RGB font color
				imagefill($im, 0, 0, $white);
				$pathTTF = str_replace('//', '/', str_replace('\\', '/', $base.'/axZm/fonts/COLLEGE.TTF')); // Text font
				imagettftext ($im, $fontSize, 0, $x, $y, $black, $pathTTF,  $text);
				imagepng($im, $target);
				imagedestroy($im);
			}
			makeTemp360SpinImage($axZm['smallImageSize']['w'], $axZm['smallImageSize']['h'], 10, 20, 12, DIR_FS_DOCUMENT_ROOT, str_replace('//', '/', str_replace('\\', '/', DIR_FS_DOCUMENT_ROOT.'/axZm/pic/zoom3Dgif/temp360Spin_'.$axZm['smallImageSize']['w'].'x'.$axZm['smallImageSize']['h'].'.png')), "Creating 360 spin preview\nplease wait ...");
			makeTemp360SpinImage($axZm['thumbSize']['w'], $axZm['thumbSize']['h'], 5, 15, 8, DIR_FS_DOCUMENT_ROOT, str_replace('//', '/', str_replace('\\', '/', DIR_FS_DOCUMENT_ROOT.'/axZm/pic/zoom3Dgif/temp360Spin_'.$axZm['thumbSize']['w'].'x'.$axZm['thumbSize']['h'].'.png')), "Creating\n360 spin\npreview");
		}
		
		$axZm['preview360imagePath'] = str_replace('//', '/', str_replace('\\', '/', DIR_WS_CATALOG.'/axZm/pic/zoom3Dgif/temp360Spin_'.$axZm['smallImageSize']['w'].'x'.$axZm['smallImageSize']['h'].'.png'));
		$axZm['thumb360imagePath'] = str_replace('//', '/', str_replace('\\', '/', DIR_WS_CATALOG.'/axZm/pic/zoom3Dgif/temp360Spin_'.$axZm['thumbSize']['w'].'x'.$axZm['thumbSize']['h'].'.png'));
		
		// Tigger the generation of the 360 spin as gif image (after the page is loaded)
		$axZm['javascript'] .= '
			jQuery.fn.make360gif('.$axZm['PRODUCTS_ID'].', '.$axZm['smallImageSize']['w'].', '.$axZm['smallImageSize']['h'].', '.$axZm['thumbSize']['w'].', '.$axZm['thumbSize']['h'].', '.$axZm['sTurn'].'); 
		'; 
	}else{
		// Preview and thumb gif images have been made already
		$axZm['preview360imagePath'] = str_replace('//', '/', str_replace('\\', '/', DIR_WS_CATALOG.'/axZm/pic/zoom3Dgif/'.$axZm['PRODUCTS_ID'].'/'.$axZm['smallImageSize']['w'].'x'.$axZm['smallImageSize']['h'].'/axZmGifAnimation.gif'));
		$axZm['thumb360imagePath'] = str_replace('//', '/', str_replace('\\', '/', DIR_WS_CATALOG.'/axZm/pic/zoom3Dgif/'.$axZm['PRODUCTS_ID'].'/'.$axZm['thumbSize']['w'].'x'.$axZm['thumbSize']['h'].'/axZmGifAnimation.gif'));
	}
	
	
	
	// Make the paths of 360 spin gif images abailable to javascript
	$axZm['javascript'] .= '
		jQuery.preview360imagePath = "'.$axZm['preview360imagePath'].'";
		jQuery.thumb360imagePath = "'.$axZm['thumb360imagePath'].'";
	'; 
}


// Generate array with all images
$zoomData = array();
$n=1; // start counter

$zoomData[$n]['f'] = basename($axZm['PRODUCTS_IMAGE']);
$zoomData[$n]['p'] = DIR_WS_ORIGINAL_IMAGES;
$axZm['imgSize'][0] = getimagesize(DIR_FS_DOCUMENT_ROOT.$axZm['PRODUCTS_IMAGE']);


echo '<div class="axZmContainer" style="width: '.$axZm['smallImageSize']['w'].'px; height: '.$axZm['smallImageSize']['h'].'px;">';
	echo '<div id="axZm-product-image" style="position: absolute; width: '.$axZm['smallImageSize']['w'].'px; height: '.$axZm['smallImageSize']['h'].'px;">';
	echo '<a href="javascript: void(0)" id="axZm-product-link" style="margin: 0px; padding: 0px; position: absolute; z-index: 1; display: block; width: '.$axZm['smallImageSize']['w'].'px; height: '.$axZm['smallImageSize']['h'].'px;">';

	// 360 spin image if it has been selected to be shown first
	if ($axZm['360Pos'] == 'first' && $axZm['preview360imagePath']){
		echo '<img id="axZm-img" src="'.$axZm['preview360imagePath'].'" alt="'.$axZm['PRODUCTS_NAME'].'" title="" style="position: absolute; margin: 0; padding: 0; z-index: 1; left: 0px; top: 0px" />';
	} 
	// First image
	elseif($axZm['PRODUCTS_IMAGE']){
		echo '<img id="axZm-img" src="'.DIR_WS_CATALOG.$axZm['PRODUCTS_IMAGE'].'" alt="'.$axZm['PRODUCTS_NAME'].'" title="" style="position: absolute;  margin: 0; padding: 0; z-index: 1; left: '.(round(($axZm['smallImageSize']['w']-$axZm['imgSize'][0][0])/2)).'px; top: '.(round(($axZm['smallImageSize']['h']-$axZm['imgSize'][0][1])/2)).'px;" />';
	} 
	echo '</a>';
	echo '</div>';

echo '</div>';

// Zoom Text
echo '<div class="axZmClickZoomMsg" style="width: '.($axZm['smallImageSize']['w']+2).'px;">';
echo $axZm['zoomText'];
echo '</div>';

// Gallery
if ($axZm['PRODUCTS_IMAGE_1'] || $axZm['thumb360imagePath']){
	
	// More views text
	if ($axZm['moreViewsText']){
		echo '<div style="width: '.$axZm['smallImageSize']['w'].'px; text-align: left">';
		echo $axZm['morePicsText'];
		echo '</div>';
	}

	// 360 spin thumb
	if ($axZm['thumb360imagePath']){
		$axZm['thumb360gallThumb'] = '<li style="float: left; margin: 0px 0px 8px 10px;">
		<table cellpadding="0" cellspacing="0" width="'.($axZm['thumbSize']['w']+2).'" height="'.($axZm['thumbSize']['h']+2).'"><tr><td class="axZmThumb">
		<a href="javascript: void(0)" on'.strtolower($axZm['thumbSwitch']).'="jQuery.fn.rollImage(jQuery.preview360imagePath, \''.$axZm['absPathTo360'].'\', '.$axZm['transitionSpeed'].', '.(strtolower($axZm['thumbSwitch']) == 'mouseover' ? 'this' : 'null').', true); return false;" title=""><img id="thumb360spin" src="'.$axZm['thumb360imagePath'].'" width="'.$axZm['thumbSize']['w'].'" height="'.$axZm['thumbSize']['h'].'" alt="" />
		</a></td></tr></table></li>';
	}
	
	// First image thumb
	$axZm['firstImageThumb'] = '
		<li style="float: left; margin: 0px 0px 8px 10px;"><table cellpadding="0" cellspacing="0" width="'.($axZm['thumbSize']['w']+2).'" height="'.($axZm['thumbSize']['h']+2).'"><tr><td class="axZmThumb">
		<a style="display: block; width: '.$axZm['thumbSize']['w'].'px; height: '.$axZm['thumbSize']['h'].'px;" href="javascript: void(0)" on'.strtolower($axZm['thumbSwitch']).'="jQuery.fn.rollImage(\''.DIR_WS_CATALOG.$axZm['PRODUCTS_IMAGE'].'\', \''.strtr(base64_encode(addslashes(gzcompress(serialize(DIR_WS_CATALOG.DIR_WS_ORIGINAL_IMAGES.basename($axZm['PRODUCTS_IMAGE'])),9))), '+/=', '-_,').'\', '.$axZm['transitionSpeed'].', '.(strtolower($axZm['thumbSwitch']) == 'mouseover' ? 'this' : 'null').', false); return false;" title="">
		<img src="'.DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.basename($axZm['PRODUCTS_IMAGE']).'" alt="'.$axZm['PRODUCTS_NAME'].'" title="" style="'.(($axZm['imgSize'][0][1] >= $axZm['imgSize'][0][0]) ? ('height: '.$axZm['thumbSize']['h'].'px; width: auto;') : ('width: '.$axZm['thumbSize']['w'].'px; height: auto;') ).'; margin-top: '.floor(($axZm['imgSize'][0][1] < $axZm['imgSize'][0][0]) ? (($axZm['thumbSize']['h']-($axZm['imgSize'][0][1]/$axZm['imgSize'][0][0]*$axZm['thumbSize']['h']))/2) : 0).'px;" />
		</a></td></tr></table></li>
	';	

	// Line
	echo '<div class="axZmThumbContainer" style="width: '.($axZm['smallImageSize']['w']+2).'px;"></div>';
	
	echo '<ul style="margin: 0px 0px 0px -10px; padding: 0; width: '.($axZm['smallImageSize']['w']+12).'px; position: relative; list-style: none outside none; text-align: left; display: block;">';
	
	// Return 360 spin thumb if on first place (before the images)
	if ($axZm['360Pos'] == 'first' && $axZm['thumb360imagePath']){
		echo $axZm['thumb360gallThumb'];
	}
	
	if ($axZm['PRODUCTS_IMAGE_1']){
		for ($n=2; $n <= (MO_PICS+1); $n++){
			if ($axZm['PRODUCTS_IMAGE_'.($n-1)]){
				$zoomData[$n]['f'] = basename($axZm['PRODUCTS_IMAGE_'.($n-1)]);
				$zoomData[$n]['p'] = DIR_WS_ORIGINAL_IMAGES;		
				$axZm['imgSize'][$n-1] = getimagesize(DIR_FS_DOCUMENT_ROOT.$axZm['PRODUCTS_IMAGE_'.($n-1)]);
				
				// First Image Thumb
				if ($n==2){
					echo $axZm['firstImageThumb'];				
				}
				
				// Gallery Images
				echo '<li style="float: left; margin: 0px 0px 8px 10px;"><table cellpadding="0" cellspacing="0" width="'.($axZm['thumbSize']['w']+2).'" height="'.($axZm['thumbSize']['h']+2).'"><tr><td class="axZmThumb">';
				echo '<a style="display: block; width: '.$axZm['thumbSize']['w'].'px; height: '.$axZm['thumbSize']['h'].'px;" href="javascript: void(0)" on'.strtolower($axZm['thumbSwitch']).'="jQuery.fn.rollImage(\''.DIR_WS_CATALOG.$axZm['PRODUCTS_IMAGE_'.($n-1)].'\', \''.strtr(base64_encode(addslashes(gzcompress(serialize(DIR_WS_CATALOG.DIR_WS_ORIGINAL_IMAGES.basename($axZm['PRODUCTS_IMAGE_'.($n-1)])),9))), '+/=', '-_,').'\', '.$axZm['transitionSpeed'].', '.(strtolower($axZm['thumbSwitch']) == 'mouseover' ? 'this' : 'null').', false); return false;" title="">';
				echo '<img src="'.DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.basename($axZm['PRODUCTS_IMAGE_'.($n-1)]).'" alt="'.$axZm['PRODUCTS_NAME'].'" title="" style="'.(($axZm['imgSize'][$n-1][1] >= $axZm['imgSize'][$n-1][0]) ? ('height: '.$axZm['thumbSize']['h'].'px; width: auto;') : ('width: '.$axZm['thumbSize']['w'].'px; height: auto;') ).'; margin-top: '.floor(($axZm['imgSize'][$n-1][1] < $axZm['imgSize'][$n-1][0]) ? (($axZm['thumbSize']['h']-($axZm['imgSize'][$n-1][1]/$axZm['imgSize'][$n-1][0]*$axZm['thumbSize']['h']))/2) : 0).'px;" />';
				echo '</a>';
				echo '</td></tr></table></li>';
			}
		}
	}else{
		echo $axZm['firstImageThumb'];
	}
	
	// Return 360 spin thumb if on last place (after the images)
	if ($axZm['360Pos'] == 'last' && $axZm['thumb360imagePath']){
		echo $axZm['thumb360gallThumb'];
	}
		
	echo '</ul>';
}	

// Encode data
$zoomData = strtr(base64_encode(addslashes(gzcompress(serialize($zoomData),9))), '+/=', '-_,');

if (!empty($zoomData)){
	$axZm['javascript'] .= '
		jQuery.zoomData = \''.$zoomData.'\';
	';
	$axZm['javascript'] .= '
		jQuery.fn.zoomImage(\''.strtr(base64_encode(addslashes(gzcompress(serialize(DIR_WS_CATALOG.DIR_WS_ORIGINAL_IMAGES.basename($axZm['PRODUCTS_IMAGE'])),9))), '+/=', '-_,').'\');	
	';
}

// Init lightbox click event on preview image (360 spin or image zoom)
if ($axZm['360Pos'] == 'first' && $axZm['thumb360imagePath']){
	$axZm['javascript'] .= '
		jQuery.fn.zoomImage(\''.$axZm['absPathTo360'].'\', false, true);
	';		
}elseif(!empty($zoomData)){
	$axZm['javascript'] .= '
	jQuery.fn.zoomImage(\''.strtr(base64_encode(addslashes(gzcompress(serialize(DIR_WS_CATALOG.DIR_WS_ORIGINAL_IMAGES.basename($axZm['PRODUCTS_IMAGE'])),9))), '+/=', '-_,').'\');	
	';
}

// Add enlarge icon over the small preview image with javascript
if ($axZm['zoomIcon']){
	$axZm['javascript'] .= '
		var iconLink = axZm_BaseUrl+\'/axZm/mods/xtc/zoom_icon.png\';
		var iconImage = new Image();
		jQuery(iconImage).load(function(){
			jQuery(this).attr(\'id\',\'zoom_to_click_icon\')
			.css({
				position: \'absolute\', 
				zIndex: 10, 
				width: iconImage.width, 
				height: iconImage.height, 
				left: ('.($axZm['smallImageSize']['w']-5).'-iconImage.width), 
				top: ('.($axZm['smallImageSize']['h']-5).'-iconImage.height), 
				cursor: \'pointer\'
			})
			.click(function(){
				jQuery(\'#axZm-product-link\').click();
			}).prependTo(\'#axZm-product-image\');
		}).attr(\'src\', iconLink);
	';
}

echo '<script type="text/javascript">';
echo '
	jQuery(window).load( function() {
';
echo $axZm['javascript'];
echo '		
	});
';
echo '</script>'

?>

