<?php
/**
* Plugin: jQuery AJAX-ZOOM, axZmSpinGif.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

if (!isset($_GET['prodID']) || !isset($_GET['w']) || !isset($_GET['h']) || !isset($_GET['thumbW']) || !isset($_GET['thumbH'])){
	exit;
}

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

require('classes/GIFEncoder.class.php');

$prodID = intval($_GET['prodID']);
$w = intval($_GET['w']); 
$h = intval($_GET['h']);
$thumbW = intval($_GET['thumbW']);
$thumbH = intval($_GET['thumbH']);

if (!isset($_GET['sTurn'])){
	$_GET['sTurn'] = 2;
}else{
	$_GET['sTurn'] = floatval($_GET['sTurn']);
}

$sTurn = $_GET['sTurn'];
if ($sTurn < 2){$sTurn = 2;}

if (!$prodID || !$w || !$h || !$thumbW || !$thumbH ){
	exit(0);
}

// Path to single 360 spin images
$prod3D = 'pic/zoom3D/'.$prodID;
if (!is_dir($prod3D)){
	$prod3D = 'pic/zoom3d/'.$prodID;
}

// Output path to preview 360 spin as gif
$target3D_preview = 'pic/zoom3Dgif/'.$prodID.'/'.$w.'x'.$h;

// Output path to thumb 360 spin as gif
$target3D_thumb = 'pic/zoom3Dgif/'.$prodID.'/'.$thumbW.'x'.$thumbH;

if (!is_dir($prod3D)){
	exit;
}else{
	if (!is_dir('pic/zoom3Dgif/'.$prodID)){
		mkdir('pic/zoom3Dgif/'.$prodID);
		chmod('pic/zoom3Dgif/'.$prodID, 0777); // Change to whatever if needed
	}
	
	if (!is_dir($target3D_preview)){
		mkdir($target3D_preview);
		chmod($target3D_preview, 0777);	// Change to whatever if needed
	}
	
	if (!is_dir($target3D_thumb)){
		mkdir($target3D_thumb);
		chmod($target3D_thumb, 0777);	// Change to whatever if needed
	}
}

if ($dh = opendir($prod3D)){
	$framesArray = array();
	while (false !== ($dat = readdir($dh))){
		if ($dat != "." && $dat != ".."){
			$framesArray[] = $dat;
		}
	}
	
	closedir ($dh);


	function natIndex($array){
		$i=1; $nArray=array();
		natcasesort($array);
		foreach ($array as $k=>$v){
			$nArray[$i]=$v;
			$i++;
		}
		return $nArray;
	}

	function openGdFile($fileType, $fPath){
		if ($fileType == 'jpg' OR $fileType == 'jpeg'){
			return imagecreatefromjpeg($fPath);
		} elseif ($fileType == 'png'){
			return imagecreatefrompng($fPath); 
		} elseif ($fileType == 'gif'){
			return imagecreatefromgif($fPath);
		} elseif ($fileType == 'bmp'){
			return imagecreatefromwbmp($fPath);
		}
	}
	
	function getl($char,$str){
		$pos=strrpos($str,$char);
		$ext=substr($str,$pos+1);
		return $ext;
	}
	
	$framesArray = natIndex($framesArray);


	$framesCount = count($framesArray);
	
	if ($framesCount < 4){
		exit(0);
	}
	
	$period = round(($sTurn * 100) / $framesCount);
	
	
	foreach($framesArray as $k => $dat){

		$orgImgSize = getimagesize($prod3D."/$dat");
		$sw = $orgImgSize[0];
		$sh = $orgImgSize[1];

		$target_preview = $target3D_preview.'/'.$dat;
		$target_preview_jpg = $target3D_preview.'/axZmFirstFrame.jpg';
		
		$target_thumb = $target3D_thumb.'/'.$dat;
		$target_thumb_jpg = $target3D_thumb.'/axZmFirstFrame.jpg';
		
		$frames_preview[] = $target_preview;
		$framed_preview[] = $period;
		$frames_thumb[] = $target_thumb;
		$framed_thumb[] = $period;
		
		if (file_exists($target_preview)){
			exit(0);
		}
			
		// Open image
		$image_s = openGdFile(strtolower(getl('.', $dat)), $prod3D."/$dat");
		
		// Make a temp preview gif image
		$pw = $w; $ph = $h;
		if (($pw/$sw)>($ph/$sh)){$prc=$ph/$sh;}
		else{$prc=$pw/$sw;}
		$dst_x = round(($pw - $sw*$prc)/2);
		$dst_y = round(($ph - $sh*$prc)/2);
		//$pw=round($sw*$prc);
		//$ph=round($sh*$prc);		
		$image_p = imagecreatetruecolor($pw, $ph);
		$color = imagecolorallocate($image_p, 255, 255, 255); // white background
		imagefill($image_p, 0, 0, $color);
		imagecopyresampled($image_p, $image_s, $dst_x, $dst_y, 0, 0, round($sw*$prc), round($sh*$prc), $sw, $sh);
		imagegif($image_p, $target_preview);
		if ($k == 1){
			imagejpeg($image_p, $target_preview_jpg, 90);
		}
		
		imagedestroy($image_p);
		
		// Make a temp thumb gif image
		$pw = $thumbW; $ph = $thumbW;
		if (($pw/$sw)>($ph/$sh)){$prc=$ph/$sh;}
		else{$prc=$pw/$sw;}
		$dst_x = round(($pw - $sw*$prc)/2);
		$dst_y = round(($ph - $sh*$prc)/2);
		//$pw=round($sw*$prc);
		//$ph=round($sh*$prc);		
		$image_p = imagecreatetruecolor($pw, $ph);
		$color = imagecolorallocate($image_p, 255, 255, 255); // white background
		imagefill($image_p, 0, 0, $color);
		imagecopyresampled($image_p, $image_s, $dst_x, $dst_y, 0, 0, round($sw*$prc), round($sh*$prc), $sw, $sh);
		imagegif($image_p, $target_thumb);
		if ($k == 1){
			imagejpeg($image_p, $target_thumb_jpg, 90);
		}
		imagedestroy($image_p);	
		
		
		imagedestroy($image_s);
		
	}
}
/*
GIFEncoder constructor:
=======================

image_stream = new GIFEncoder	(
	URL or Binary data	'Sources'
	int					'Delay times'
	int					'Animation loops'
	int					'Disposal'
	int					'Transparent red, green, blue colors'
	int					'Source type'
);
*/


$gif = new GIFEncoder (
	$frames_preview,
	$framed_preview,
	0,
	2,
	255, 255, 255,
	"url" // bin, url
);

fwrite(fopen($target3D_preview.'/axZmGifAnimation.gif', "wb" ), $gif->GetAnimation());


// Remove preview gif files
if ($dh = opendir($target3D_preview)){
	$framesArray = array();
	while (false !== ($dat = readdir($dh))){
		if ($dat != "." && $dat != ".." && $dat != 'axZmGifAnimation.gif' && $dat != 'axZmFirstFrame.jpg'){
			unlink($target3D_preview.'/'.$dat);
		}
	}
}

/*
// Uncomment to make gif "bounce", e.g. for 180 or 270deg images.
function reverse_array_bounce($array){
	$reversed = array_reverse($array);
	$i = sizeof($reversed);
	$newreversed = array();
	foreach ($reversed as $value){
		$newreversed[$i] = $value;
		$i++;
	}
	return $array = array_merge($array, $newreversed);
}

$frames_preview = reverse_array_bounce($frames_preview);
$framed_preview = reverse_array_bounce($framed_preview);
$frames_thumb = reverse_array_bounce($frames_thumb);
$framed_thumb = reverse_array_bounce($frames_thumb);
*/

$gif = new GIFEncoder (
	$frames_thumb,
	$framed_thumb,
	0,
	2,
	0, 0, 0,
	"url" // bin, url
);

fwrite(fopen($target3D_thumb.'/axZmGifAnimation.gif', "wb" ), $gif->GetAnimation());


// Remove preview gif files
if ($dh = opendir($target3D_thumb)){
	$framesArray = array();
	while (false !== ($dat = readdir($dh))){
		if ($dat != "." && $dat != ".." && $dat != 'axZmGifAnimation.gif' && $dat != 'axZmFirstFrame.jpg'){
			unlink($target3D_thumb.'/'.$dat);
		}
	}
}

$preview360imagePath = str_replace('//', '/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF']).'/'.$target3D_preview.'/axZmGifAnimation.gif'));
$thumb360imagePath = str_replace('//', '/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF']).'/'.$target3D_thumb.'/axZmGifAnimation.gif'));
$preview360FirstFramePath = str_replace('//', '/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF']).'/'.$target3D_preview.'/axZmFirstFrame.jpg'));

echo '
jQuery.preview360imagePath = "'.$preview360imagePath.'"; 
jQuery.preview360FirstFramePath = "'.$preview360FirstFramePath.'"; 
jQuery("#thumb360spin").attr("src", "'.$thumb360imagePath.'"); 
if (jQuery.spin360Loaded){
	jQuery("#axZm-img").attr("src", jQuery.preview360imagePath); 
}
';
?>
