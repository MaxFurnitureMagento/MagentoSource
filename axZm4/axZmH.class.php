<?php
/**
* Plugin: jQuery AJAX-ZOOM, axZmH.class.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

class axZmH {

	public $axZm;
	
	private $returnMakeFirstImage;
	private $returnMakeZoomTiles;
	private $returnMakeAllThumbs;
	private $fileErrorDialog;
	
	function __construct($axZm){
		$this->axZm = $axZm;
	}

	private $doctype = array(
		1 => array ('XHTML 1.0 Transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'), 
		array ('XHTML 1.0 Strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'),
		array ('XHTML Basic 1.0' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd"><html>'),
		array ('XHTML 1.1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"><html>'),
		array ('XHTML Basic 1.1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd"><html>'),
		array ('HTML 4.01 Transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>'),
		array ('HTML 4.01 Strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html>'),
		array ('HTML 3.2' => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2 Final//EN"><html>'),
		array ('HTML 2.0' => '<!DOCTYPE html PUBLIC "-//IETF//DTD HTML//EN"><html>'),
		array ('None' => '<html>')
	);
	
	/**
	  * Check filename validity
	  * @access public
	  * @param string $filename
	  *	@param bool $ext
	  * @return bool
	  **/	
	public function isValidFilename($filename, $ext){
		if ($ext){
			$pattern = "/^[a-zA-Z\_0-9ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç]+[a-zA-Z\_0-9\-\.\,\(\)\[\]\%ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç\s+]+\.+[a-zA-Z]{3,4}$/";
		}else{
			$pattern = "/^[a-zA-Z\_0-9ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç]+[a-zA-Z\_0-9\-\.\,\(\)\[\]\%ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç\s+]$/";
		}
		if (preg_match ($pattern, $filename)){
			return true;
		}else{
			return false;
		}
	}

	/**
	  * Check filetype validity
	  * @access public
	  * @param string $filename
	  * @return bool
	  **/	

	 public function isValidFileType($filename){
	 	// tif and psd are only supported by imagemagick 
		$fileTypes = array('jpg', 'jpeg', 'tif', 'tiff', 'gif', 'png', 'bmp', 'psd');
		$ext = $this->getl('.',$filename);
		if (in_array(strtolower($ext), $fileTypes)){
			return true;
		}else{
			return false;
		}
	}

	/**
	  * Check pathname validity
	  * @access public
	  * @param string $path
	  * @return bool
	  **/	
	public function isValidPath($path){
		if (preg_match ("/^[a-zA-Z\_0-9\:\/ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç]+([a-zA-Z\_0-9\:\.\,\(\)\[\]\ÄÖÜßäöüßÁÀÉÈÍÌÓÒÚÙÃÂÊÎÕÔÛÇáàéèíìóòúùãâêîõôûç\-\/\s+]*)$/", $path)){
			return true;
		}else{
			return false;
		}
	}

	public function pngMod($zoom){
		if (isset($zoom['config']) && $zoom['config']['pngMode'] === true){
			return 'png';
		}else{
			return 'jpg';
		}
	}

	/**
	  * Deep extend arrays in jQuery like way, Ver. 4.0+
	  * @access public
	  * @param string $arrDefault
	  * @param int $arrExtend
	  * @return array
	  **/	

	public function deepExtend($arrDefault, $arrExtend){
		$returnArray = array();
		
		foreach ($arrDefault as $k=>$v){
			if (isset($arrExtend[$k]) && is_array($arrExtend[$k]) && is_array($v)){
				$returnArray[$k] = $this->deepExtend($v, $arrExtend[$k]);
			} elseif (isset($arrExtend[$k])){
				$returnArray[$k] = $arrExtend[$k];
			} else {
				$returnArray[$k] = $v;
			} 
		}
		
		return $returnArray;
	}

	/**
	  * Download original image or image in certain resolution
	  * @access public
	  * @param string $zoom
	  * @param int $zoomID
	  * @return bool
	  **/		
	public function downloadImage($zoom, $zoomID){
		if (!$zoom['config']['allowDownload']){
			echo 'Download is not allowed.';
			exit;
		}
		$fileName = '';
		
		if ($zoom['config']['pic_list_array'][$zoomID]){
			$fileName = $zoom['config']['pic_list_array'][$zoomID];
		}else{
			$flipedArray = array_flip($zoom['config']['pic_list_array']);
			if ($flipedArray[$zoomID]){
				$fileName = $zoomID;
			}
		}
		
		if (!$fileName){
			echo 'File not found.';
			exit;		
		}
		
		
		$filePath = $this->checkSlash($zoom['config']['picDir'].'/'.$fileName,'remove');

		$ext = strtolower($this->getl('.', $filePath));
		
		$extAllow = array('jpg', 'jpeg', 'jpe', 'tif', 'tiff', 'bmp', 'gif', 'png', 'psd'); 

		if (file_exists($filePath) && in_array($ext, $extAllow)){
			ob_end_flush();
			
			if (ini_get('zlib.output_compression')){
				ini_set('zlib.output_compression', 'Off');
			}
			
			if (function_exists('header_remove')){header_remove();}
			if ($zoom['config']['downloadRes']){
				// Image in certain resolution, output as jpg, forced download (8th argument $this->axZm->rawThumb)
				if (isset($_GET['downloadRes']) && is_array($zoom['config']['downloadRes'])){
					if (in_array($_GET['downloadRes'], $zoom['config']['downloadRes'])){
						$zoom['config']['downloadRes'] = $_GET['downloadRes'];
					}
				}
				
				if (!isset($_GET['downloadRes']) && is_array($zoom['config']['downloadRes'])){
					$zoom['config']['downloadRes'] = $zoom['config']['downloadRes'][0];
				}
				
				$dim = explode('x', $zoom['config']['downloadRes']);
				
				ob_start();
				$this->axZm->rawThumb($zoom, dirname($filePath), basename($filePath), $dim[0], $dim[1], ($zoom['config']['pngMode'] ? $zoom['config']['downloadQualPng'] : $zoom['config']['downloadQual']), $zoom['config']['downloadCache'], true);
				ob_end_flush();
				exit;
			}else{
				if ($zoom['config']['memory_limit']){ini_set('memory_limit', $zoom['config']['memory_limit']);}
				// Original image, output original image, forced download
				$len = filesize($filePath);
				$outname = $this->getl('/', $filePath);
				header('Content-Description: File Transfer');
				header("Content-type: image/".$ext);
				header("Content-Disposition: attachment; filename=\"$outname\"");
				header("Content-Transfer-Encoding: binary");
				header("Cache-Control: must-revalidate");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header('Pragma: public');
				header("Content-Length: $len");
				ob_clean();
				flush();
				readfile($filePath);
				exit;
			}
		}
		exit;
	}

	/**
	  * Check validity of CSV
	  * @access public
	  * @param string $string
	  * @param string $sep
	  * @param string $type
	  * @return mixed
	  **/	
	public function testCSV($string, $sep, $type){
		$array = explode($sep, $string);
		$output = array();
		if ($type == 'int'){
			foreach ($array as $k=>$v){
				$output[$k] = intval($v);
			}
		}
		if (!empty($output)){
			return implode($sep, $output);
		}else{
			return false;
		}
	}

	/**
	  * Remove all tags except supported
	  * @access public
	  * @param string $string
	  * @return string
	  **/	
	public function removeScriptTags($string){
		return strip_tags($string, '<ul><ol><li><br><div><span><table><tr><td><h1><h2><h3><h4><img>');
	}

	/**
	  * Try to figure out the path of axZm direcotry
	  * @access public
	  * @return string
	  **/	
	public function installPath(){
		//$path = str_replace(array('//','axZm'),array('/',''),str_replace(str_replace('\\','/',realpath($_SERVER['DOCUMENT_ROOT'])),'/',str_replace('\\','/',dirname(realpath(__FILE__)))));		
		$path = dirname(str_replace('//', '/', str_replace(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/', str_replace('\\','/',dirname(realpath(__FILE__))))));
		//$path = preg_replace('/.*?\bpublic_html\b/s', '', $path);
		$path = $this->checkSlash($path, 'remove');
		return $path;
	}
	

	/**
	  * Uncompress data string
	  * @access public
	  * @param string $data
	  * @return mixed
	  **/	
	public function uncompress($data, $noArray){
		 $r = unserialize(gzuncompress(stripslashes(base64_decode(strtr($data, '-_,', '+/=')))));
		 if ($data && !$noArray && (!is_array($r) || empty($r))){
		 	$r = array();
			
			$arr = explode('|', $data);
			foreach($arr as $k=>$v){
				if ($v){
					$i = pathinfo($v);
					$r[$k+1]['p'] = $this->checkSlash($i['dirname'], 'add');
					$r[$k+1]['f'] = $i['basename'];
				}
			}
		 }
		 return $r;
	}

	/**
	  * Compress php data to pass over query string
	  * @access public
	  * @param mixed $data
	  * @return string
	  **/	
	public function compress($data){
		return strtr(base64_encode(addslashes(gzcompress(serialize($data),9))), '+/=', '-_,');
	}	
	
	/**
	  * Returns the doctype as string for html, used in examples
	  * @access public
	  * @param int $key The numeric key of $doctype
	  * @return HTML-Output
	  **/
	public function setDoctype($key = false){
		if (array_key_exists($key, $this->doctype)){
			$doc = array_values($this->doctype[$key]);
		}else{
			$doc = array_values($this->doctype[7]);
		}
		$docc = explode('<html',$doc[0]);
		$doc[0] = $docc[0]."\r\n".'<html'.$docc[1];
		return $doc[0];		
	}

	/**
	  * Compare original image time with images created by AJAX-ZOOM
	  * @access public
	  * @param array $zoom
	  * @return nothing
	  **/
	public function cTimeCompare($zoom){
		foreach ($zoom['config']['pic_list_data'] as $num => $v){
			$smallImg = $zoom['config']['thumbDir'].$this->composeFileName($zoom['config']['pic_list_array'][$num], $zoom['config']['picDim'], '_', $this->pngMod($zoom));
			$tileImg = $zoom['config']['pyrTilesDir'].$this->getf('.',$zoom['config']['pic_list_array'][$num]).'/0-0-0.'.$this->pngMod($zoom);
			if (isset($v['path'])){
				$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['pic'].'/'.$v['path'],'add'),'add');
			}
			
			if (file_exists($smallImg) && file_exists($tileImg) && file_exists($zoom['config']['picDir'].$zoom['config']['pic_list_array'][$num])){
				$smallImgTime = filectime($smallImg);
				$tileImgTime = filectime($tileImg);
				$orgImageTime = filectime ($zoom['config']['picDir'].$zoom['config']['pic_list_array'][$num]);
				
				if ($orgImageTime > $smallImgTime || $orgImageTime > $tileImgTime){					
					$this->removeAxZm($zoom, $zoom['config']['pic_list_array'][$num], array('In' => true, 'Th' => true, 'Ti' => true, 'gP' => true), false);
				}
			}
		}
	}

	/**
	  * Rotate images
	  * @access public
	  * @param array $filename
	  * @param array $angle
	  * @return image
	  **/

	public function rotateImage($filename, $angle) {
		$fType = strtolower($this->getl('.', $filename));
		if (!($fType == 'jpg' || $fType == 'jpeg')){
			return readfile($filename);
		}
		$src_img = imagecreatefromjpeg($filename);
		
		if (function_exists('imagerotate')) {
			return imagerotate($src_img, $angle, 0);
		} else { 
			$src_x = imagesx($src_img);
			$src_y = imagesy($src_img);
			if ($angle == 180) {
				$dest_x = $src_x;
				$dest_y = $src_y; 
			}
			elseif ($src_x <= $src_y) {
				$dest_x = $src_y;
				$dest_y = $src_x; 
			}
			elseif ($src_x >= $src_y) {
				$dest_x = $src_y;
				$dest_y = $src_x; 
			}
		   
			$rotate = imagecreatetruecolor($dest_x, $dest_y);
			imagealphablending($rotate, false);

			switch ($angle) {
				case 270:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($src_img, $x, $y);
							imagesetpixel($rotate, $dest_x - $y - 1, $x, $color); 
						}
					}
					break;
				case 90:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($src_img, $x, $y);
							imagesetpixel($rotate, $y, $dest_y - $x - 1, $color); 
						}
					}
					break;
				case 180:
					for ($y = 0; $y < ($src_y); $y++) {
						for ($x = 0; $x < ($src_x); $x++) {
							$color = imagecolorat($src_img, $x, $y);
							imagesetpixel($rotate, $dest_x - $x - 1, $dest_y - $y - 1, $color); 
						}
					}
					break;
				default: $rotate = $src_img; 
			}
			
			return $rotate;
		} 
	}

	public function exifOrientation($input_file, $output_file) {
		$data = new PelDataWindow(file_get_contents($input_file));
	
		if (PelJpeg::isValid($data)) {
			$jpeg = new PelJpeg();
			$jpeg->load($data);
			if ($jpeg != null) {
				$exif = $jpeg->getExif();
				if ($exif != null) {
					$tiff = $exif->getTiff();
					if ($tiff != null) {
						$ifd0 = $tiff->getIfd();
						if ($ifd0 != null) {
	
							// change tag "orientation"
							$orientation = $ifd0->getEntry(PelTag::ORIENTATION);
							$orientation->setValue(0);
							//$orientation->getValue();
							
							
							// Change tag "image_description"
							$sEXIF_description = "Picture rotated automatically.";
							$description = $ifd0->getEntry(PelTag::IMAGE_DESCRIPTION);
							
							// We need to check if the image already had a description stored. 
							if ($description == null) {
								// Create a new PelEntryAscii object to hold the description.  
								$description = new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $sEXIF_description);
								// This will insert the newly created entry with the description into the IFD. 
								$ifd0->addEntry($description);
							} else {
								// Save old description found in the image
								$sEXIF_description_old = $description->getValue();
								// Update description  
								$description->setValue($sEXIF_description);
							}
							
							// write file
							file_put_contents($output_file, $jpeg->getBytes());
						}
					}
				}
			}
		}
	}

	/**
	  * Check the existance of the files and generate everything needed on the fly
	  * @access public
	  * @param array $zoom
	  * @param array $zoomTmp
	  * @return array $zoom, $zoomTmp
	  **/	
	public function proceedList($zoom, $zoomTmp){

		if ($zoom['config']['cTimeCompare']){
			$this->cTimeCompare($zoom);
		}
	
		$pic_list_array = $zoom['config']['pic_list_array'];
		$pic_list_data = $zoom['config']['pic_list_data'];
		
		if (!empty($pic_list_array) AND !empty($pic_list_data))
		{
		
			//////////////////////////////////////////////////////////////////////////////////////////////////////
			//// Select the first picture if no zoomID ///////////////////////////////////////////////////////////
			//// passed over query string ////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////////////////////////////////
			
			// zoomID must be a number (integer) > 0 !!!
			settype ($_GET['zoomID'],'int');
			
			// Set the internal pointer of an array to its first element
			reset($pic_list_array);
			
			// You can also pass the filename, which has to exist in the $pic_list_array
			if (isset($_GET['zoomFile'])){
				if (in_array($_GET['zoomFile'], $pic_list_array)){
					$flipedArray = array_flip($pic_list_array);
					$_GET['zoomID'] = $flipedArray[$_GET['zoomFile']];
				}
			}
			
			// If no zoomID passed or zoomID is not a key in picture array
			if (!$_GET['zoomID'] OR !array_key_exists($_GET['zoomID'],$pic_list_array)){
				// then select the first picture in defined array
				$_GET['zoomID'] = key ($pic_list_array);
			}
			
			// Redefine the root path of the image
			if (isset($pic_list_data[$_GET['zoomID']]['path'])){
				$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$_GET['zoomID']]['path'],'add'),'add');
				
				// Heuristic approach
				if (!is_dir($zoom['config']['picDir'])){
					$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash('/'.$pic_list_data[$_GET['zoomID']]['path'],'add'),'add');
					if (is_dir($zoom['config']['picDir'])){
						$zoom['config']['pic'] = '';
					} else {
						$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['installPath'].'/'.$pic_list_data[$_GET['zoomID']]['path'],'add'),'add');
						if (is_dir($zoom['config']['picDir'])){
							$zoom['config']['pic'] = $zoom['config']['installPath'];
						}
					}
				}
			}

			////////////////////////////////////////////////////////////////////////////////////////////////////////
			//// Check if source file exists ///////////////////////////////////////////////////////////////////////
			////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			// If for some reason there the original image does not exist, there is a problem, which could be solved
			// This may happen if you get images from the database entry, 
			// but they do not exist in the filesystem because you may intentionally or unintentionally renamed them or whatever...
			// So we need to check the whole array if such an error occures
			
			if (!file_exists($zoom['config']['picDir'].$pic_list_array[$_GET['zoomID']]))
			{
				
				// The passed zoomID is problematic! Unset it.
				unset ($_GET['zoomID']);
				
				$pic_list_temp_array = $pic_list_array;
				
				$zoomTmp['errorImages'] = array();
				
				// Loop through the $pic_list_array to find a picture that exists
				foreach ($pic_list_array as $k=>$v){
					if (isset($pic_list_data[$k]['path'])){
						$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$k]['path'],'add'),'add');
					}
					
					// If we have found an image, that exists :-)
					if (file_exists($zoom['config']['picDir'].$pic_list_array[$v])){
						// If we have not found an existing picture already
						if (!$zoomTmp['picFound']){
							// Define $_GET['zoomID'] with the image key from $pic_list_array
							$_GET['zoomID'] = $k;
							// Define a var that tells the loop about a successful finding
							$zoomTmp['picFound'] = $k;
							
						}
					}
					// There is not such a file in filesystem :-(
					else{
						$zoomTmp['errorImages'][$k] = $zoom['config']['picDir'].$pic_list_array[$k]; //Patch: 20101-01-01 ($pic_list_array[$k] not $pic_list_array[$v])
						// Remove this picture from the arrays
						unset($pic_list_temp_array[$k]);
						unset($pic_list_data[$k]);
					}
				}
				
				// If you have defined in zoomConfig.inc.php to show errors
				if ($zoom['config']['errors'] && !empty($zoomTmp['errorImages'])){

					// Trigger message that lists missing images (with method drawZoomBox of axZmH.class.php)
					$zoomTmp['fileErrorTitle']="Error images missing";

					// Display also the paths of images that can not be found
					foreach ($zoomTmp['errorImages'] as $k=>$v){$zoomTmp['fileErrorText'].="<li>$v</li> ";}
					// Compute what has been removed (not needed)
					/*
					$zoomTmp['fileError'] = array_diff($pic_list_array, $pic_list_temp_array);
					foreach ($zoomTmp['fileError'] as $k=>$v){$zoomTmp['fileErrorText'].="<li>$v</li> ";}
					*/
					
					$zoomTmp['fileErrorText']="<ul>".$zoomTmp['fileErrorText']."</ul>";
					$this->fileErrorDialog="<script type=\"text/javascript\">jQuery.fn.axZm.zoomAlert('".$zoomTmp['fileErrorText']."','".$zoomTmp['fileErrorTitle']."',false);</script>";
				}
				
				// Redefine the basic $pic_list_array with removed items
				$pic_list_array = $pic_list_temp_array;
			}	
			
			// Store information in $zoom['config']
			$zoom['config']['pic_list_array'] = $pic_list_array;
			$zoom['config']['pic_list_data'] = $pic_list_data;
			
			
			// Exif orientation
			if ($zoom['config']['exifAutoRotation'] && !isset($_GET['str']) && !isset($_GET['setHW']) && !defined('PHALANGER')){
				
				$pelLib = false;
				// http://lsolesen.github.com/pel/
				if (file_exists(dirname(__FILE__).'/classes/pel/PelJpeg.php')){
					$pelLib = true;
					require_once(dirname(__FILE__).'/classes/pel/PelDataWindow.php');
					require_once(dirname(__FILE__).'/classes/pel/PelJpeg.php');
					require_once(dirname(__FILE__).'/classes/pel/PelTiff.php');
				}
				
				foreach ($pic_list_array as $k=>$v){
					if (strtolower($this->getl('.', $v)) == 'jpg' || strtolower($this->getl('.', $v)) == 'jpeg'){
						if (isset($pic_list_data[$k]['path'])){
							$tempPicDir = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$k]['path'],'add'),'add');
						}else{
							$tempPicDir = $zoom['config']['picDir'];
						}
						
						$aryEXIF = array();
						$aryEXIF = exif_read_data($tempPicDir.$v);						
						
						if (isset($aryEXIF["Orientation"]) && ($aryEXIF["Orientation"] == 6 || $aryEXIF["Orientation"] == 8)) {
							$angle = 0;
							if ($aryEXIF["Orientation"] == 6){
								$angle = 270;
							} elseif ($aryEXIF["Orientation"] == 8){
								$angle = 90;
							}


							if ($angle != 0){
								if (is_writable($tempPicDir.$v)){
								
									// Delete all already made tiles, thumbs etc.
									$this->removeAxZm($zoom, $v, array('In' => true, 'Th' => true, 'Ti' => true, 'gP' => true), false);
									
									// Save exif info
									if ($pelLib){
										$sourceExifFile  = new PelJpeg($tempPicDir.$v);
										$sourceExifInfo = $sourceExifFile->getExif();
									 }
									
									// Rotate
									$rotatedImage = $this->rotateImage($tempPicDir.$v, $angle);
									imagejpeg($rotatedImage, $tempPicDir.$v, 100);
									
									// Transfere EXIF information
									if ($pelLib){
										$outputExifFile = new PelJpeg($tempPicDir.$v);
										if ($sourceExifInfo != null) {
											$outputExifFile->setExif($sourceExifInfo);
											file_put_contents($tempPicDir.$v, $outputExifFile->getBytes());
										}

										// Reset the EXIF-tag "orientation"
										$this->exifOrientation($tempPicDir.$v, $tempPicDir.$v);
									}
									
									$pic_list_data[$k]['imgSize'] = $this->axZm->imageSize($tempPicDir.$v, $zoom['config']['im'], false);
								}else{
									if ($zoom['config']['errors']){
										echo 'alert("'.$tempPicDir.$v.' is not writable by PHP.");';
									}
								}
							}
						}
					}
					
					$zoom['config']['pic_list_data'] = $pic_list_data;
					
				}
			}
			
			
			
			// By now we have generated and checked $pic_list_array and $pic_list_data
			// $_GET['zoomID'] is also checked and should exist
			if (isset($_GET['zoomID']))
			{
				// Redefine the root path of the image
				if (isset($pic_list_data[$_GET['zoomID']]['path'])){
					$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['fpPP'].$this->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$_GET['zoomID']]['path'],'add'),'add');
				}
				
				////////////////////////////////////////////////////
				//// Important for next code and zoomLoad.php !!!///
				////////////////////////////////////////////////////
				
				// 1. $zoom['config']['orgImgName'] // filename of the source image
				// 2. $zoom['config']['orgImgSize'] // imagesize of the source image
				// 3. $zoom['config']['smallImgName'] // filename of the initial image
				// 4. $zoom['config']['smallImgSize'] // imagesize of the initial image
				// 5. $zoom['config']['smallFileSize'] // filesize of thr initial image		
				
				// 1. Filename of the source image
				$zoom['config']['orgImgName'] = $pic_list_array[$_GET['zoomID']];
				
				// 2. Imagesize of the source image [array(0=>width, 1=>height)]
				// Do not replace it with $pic_list_data[$_GET['zoomID']]['imgSize']
				// since this information will only be generated on load and we also need it for zooming (zoomLoad.php)
				$zoom['config']['orgImgSize'] = $this->axZm->imageSize($zoom['config']['picDir'].$zoom['config']['orgImgName'], $zoom['config']['im'], false);
				
				// 3. Filename of the initial image
				// This image will be loaded first (without zoom)
				// Before it has to be resized with $zoom['config']['picDim'] and saved in $zoom['config']['thumbDir']
				// e.g. imagename is abcdefgh.jpg, $zoom['config']['picDim'] = '600x400';
				// It will be then saved as abcdefgh_600x400.jpg in $zoom['config']['thumbDir']
				$zoom['config']['smallImgName'] = $this->composeFileName($pic_list_array[$_GET['zoomID']], $zoom['config']['picDim'], '_', $this->pngMod($zoom));
				
				// 4. Make initial image on the fly and save it once
				// Only current displayed image will be made on the fly
				// Check if it exists first
				if (!file_exists($zoom['config']['thumbDir'].$zoom['config']['smallImgName'])){
					// Make first image
					$this->returnMakeFirstImage = $this->axZm->makeFirstImage($zoom, false);
				} else if ($zoom['config']['mapOwnImage']){
					if (!isset($zoom['config']['mapDir'])){
						$zoom['config']['mapDir'] = $axZmH->checkSlash($zoom['config']['fpPP'].$zoom['config']['mapPath'],'add');
					}
					if (!file_exists($zoom['config']['mapDir'].$this->composeFileName($zoom['config']['orgImgName'], $zoom['config']['mapOwnImage'], '_', $this->pngMod($zoom)))){
						$this->returnMakeFirstImage = $this->axZm->makeFirstImage($zoom, true);
					}
				}
		
				// Imagesize of the initial image
				// We also need to know how big this initial image is, but only after it has been possibly made on the fly
				$zoom['config']['smallImgSize'] = $this->axZm->imageSize($zoom['config']['thumbDir'].$zoom['config']['smallImgName'], $zoom['config']['im'], false);
				
				// 5. Filesize of the first image for internet connection speed test
				$zoom['config']['smallFileSize'] = filesize($zoom['config']['thumbDir'].$zoom['config']['smallImgName']);
		
				// This will tell the javascript the dimensions of the initial picture if it is not already done and loaded via ajax
				// This happens if you load a new picture via javascript wich has not been "prepared" already.
				// In this case it will be prepared on the fly after calling it. The procedure can take a couple seconds.
				if (isset($_GET['setHW'])){
					echo "<script type=\"text/javascript\">";
					echo "
					jQuery.axZm['iw']=".$this->ptj($zoom['config']['smallImgSize'][0]).";
					jQuery.axZm['ih']=".$this->ptj($zoom['config']['smallImgSize'][1]).";
					";

					echo "</script>";
					if (!is_bool($this->returnMakeFirstImage)){echo $this->returnMakeFirstImage;}
					
					// Do not exit here in order to make pyramid images or tiles on the fly too!
					// If $zoom['config']['pyrDialog'] OR $zoom['config']['gPyramidDialog'] is set to true 
					// and tiles or image pyramid have to be generated, a "please wait" diolog will appear during this operation. 
					// You can switch off this dialog by setting $zoom['config']['pyrDialog'] = false ....
				}

				// The following code will be executed only onece on window load...
				if (!isset($_GET['str']))
				{
		
					if (!isset($_GET['setHW'])) //  && ($zoom['config']['useGallery'] OR $zoom['config']['useFullGallery'] OR $zoom['config']['useHorGallery'] OR $zoom['config']['galleryNavi'])
					{
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						//// Get all initial image sizes for Image Gallery, but only on page load ////////////////////////////////////////////
						//// We will need to pass this parameters to and then from the gallery, when the user clicks on an different thumb ///
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						foreach ($pic_list_array as $k=>$v){
							$zoomTmp['smallImgNameTemp'] = $this->composeFileName($v, $zoom['config']['picDim'], '_', $this->pngMod($zoom));
							
							// Since not all initial pictures could have been generated, check their existence first
							// Generation of all initial pictures on the fly during first page load could take to long. 
							if (file_exists($zoom['config']['thumbDir'].$zoomTmp['smallImgNameTemp'])){
								$pic_list_data[$k]['thumbSize'] = $this->axZm->imageSize($zoom['config']['thumbDir'].$zoomTmp['smallImgNameTemp'], $zoom['config']['im'], false);
							}else{
								$pic_list_data[$k]['thumbSize'] = false;
							}
						}
						
						// Store information
						$zoom['config']['pic_list_data'] = $pic_list_data;
						
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						//// Generate all thumbs for Image Gallery on the fly, ///////////////////////////////////////////////////////////////
						//// Depending on the number of pictures and it's sizes this may take a while... /////////////////////////////////////
						//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
						
						// Added the possibility to not generate thumbs
						if (!isset($zoom['config']['galleryNoThumbs']) OR $zoom['config']['galleryNoThumbs'] === false){
							$this->returnMakeAllThumbs = $this->axZm->makeAllThumbs($zoom);
						}else{
							$this->returnMakeAllThumbs = false;
						}
					}
					
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					//// Image pyramid generation for selected zoomID, not all gallery list //////////////////////////////////////////////////
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					
					if ($zoom['config']['gPyramid'] AND $zoom['config']['gPyramidDir']){
						// The subfolder name is the same as image name
						$zoomTmp['gPyramidPicDir'] = $zoom['config']['gPyramidDir'].$this->getf('.',$zoom['config']['orgImgName']);
						
						// Check if pyramid images have been made already
						$zoomTmp['gPyramidPicDirExists'] = is_dir($zoomTmp['gPyramidPicDir']);
						
						// Make subfolder and generate the pyramid images, if they are not already made
						if (!$zoomTmp['gPyramidPicDirExists'] AND !file_exists($zoom['config']['picDir'].$this->getf('.', $zoom['config']['orgImgName']).'.txt')){
							$this->returnMakeZoomTiles = $this->axZm->gPyramid($zoom);
							if (isset($_GET['setHW']) AND !is_bool($this->returnMakeZoomTiles)){echo $this->returnMakeZoomTiles;}
						}		
						
						// Overwrite the files, if a $zoom['config']['gPyramidOverwrite'] is set to true 
						elseif ($zoomTmp['gPyramidPicDirExists'] AND $zoom['config']['gPyramidOverwrite'] AND is_int($zoom['config']['gPyramidTime']) AND strlen($zoom['config']['gPyramidTimeDim']) AND !file_exists($zoom['config']['picDir'].$this->getf('.', $zoom['config']['orgImgName']).'.txt')){
							$this->returnMakeZoomTiles = $this->axZm->gPyramid($zoom);
							if (isset($_GET['setHW']) AND !is_bool($this->returnMakeZoomTiles)){echo $this->returnMakeZoomTiles;}
						}	
							
						// Changes chmod of all pyramid directories
						if ($zoom['config']['gPyramidChmodAll']){
							$this->chmodAllDir($zoom['config']['gPyramidDir'],$zoom['config']['gPyramidChmod']);
						}
					}
					
					////////////////////////////////////////////////////////////////////////
					/// Image tiles generation for selected zoomID, not all gallery list ///
					////////////////////////////////////////////////////////////////////////
					if ($zoom['config']['pyrTiles'] AND $zoom['config']['pyrTilesDir']){
						$zoomTmp['thisTilesPicDir'] = $zoom['config']['pyrTilesDir'].$this->getf('.',$zoom['config']['orgImgName']);
						
						if ($zoom['config']['orgImgSize'][0] >= $zoom['config']['tileSize'] OR $zoom['config']['orgImgSize'][1] >= $zoom['config']['tileSize']){
							$this->returnMakeZoomTiles = $this->axZm->zC($zoom, false);
							
							if (!file_exists($zoomTmp['thisTilesPicDir'].'/0-0-0.'.$this->pngMod($zoom)) AND !file_exists($zoom['config']['picDir'].$this->getf('.', $zoom['config']['orgImgName']).'.txt') ){
								$this->returnMakeZoomTiles = $this->axZm->makeZoomTiles($zoom);
							}
							
							if (isset($_GET['setHW']) AND !is_bool($this->returnMakeZoomTiles) AND $this->returnMakeZoomTiles){echo $this->returnMakeZoomTiles;}
						}
						
						// Changes chmod matched with the config
						if ($zoom['config']['pyrChmodAll']){
							$this->chmodAllDir($zoom['config']['pyrTilesDir'],$zoom['config']['pyrChmod']);
						}
					}
				
					//////////////////////////////////////////////////////////////////////////////////
					// Generate code (an array) for the gallery, which will be passed to javascript //
					//////////////////////////////////////////////////////////////////////////////////
					if (!isset($_GET['setHW'])){ //  && ($zoom['config']['useGallery'] OR $zoom['config']['useFullGallery'] OR $zoom['config']['useHorGallery'] OR $zoom['config']['galleryNavi'])
						foreach ($pic_list_data as $k=>$v){
							$zoom['config']['galArray'][$k]['img']	= $v['fileName']; // Filename of the image
							$zoom['config']['galArray'][$k]['ow']	= $v['imgSize'][0]; // Width of original pic
							$zoom['config']['galArray'][$k]['oh']	= $v['imgSize'][1]; // Height of original pic
							$zoom['config']['galArray'][$k]['iw']	= $v['thumbSize'][0]; // Width of initial pic
							$zoom['config']['galArray'][$k]['ih']	= $v['thumbSize'][1]; //  Height of initial pic
							$zoom['config']['galArray'][$k]['tD']	= $v['thumbDescr']; // Description under the thumb in the gallery
							$zoom['config']['galArray'][$k]['fD']	= $v['fullDescr']; // Full Description

							//$zoom['config']['galArray'][$k]['fT'] 	= $this->getl('.', $pic_list_array[$k]);
							//$zoom['config']['galArray'][$k]['fB'] 	= $this->getf('.', $pic_list_array[$k]); // Ver. 4.0
							
							$zoom['config']['galArray'][$k]['mf'] = false;
							if ($zoom['config']['useMap'] && $zoom['config']['mapOwnImage'] && $zoom['config']['mapDir']){
								
								$ownImageSize = explode('x', $zoom['config']['mapOwnImage']);
								$ownImageName = $this->composeFileName($v['fileName'], $ownImageSize[0].'x'.$ownImageSize[1], '_', $this->pngMod($zoom));
								 
								if (!file_exists($zoom['config']['mapDir'].$ownImageName)){
									$zoom['config']['galArray'][$k]['mf'] = true;
								}
							}
							
							if (isset($v['path'])){ 
								// Note: this is absolute path to the image now
								$zoom['config']['galArray'][$k]['ph'] = $this->checkSlash($zoom['config']['pic'].'/'.$v['path'], 'add'); // Image Path
							}
				
							// Check, whether pyramid or tiles have to be generated
							if ($zoom['config']['gPyramid']){
								if ( is_dir($zoom['config']['gPyramidDir'].$this->getf('.',$v['fileName'])) ){
									//$zoom['config']['galArray'][$k][9]=false;
									$zoom['config']['galArray'][$k]['mk'] = false;
								}else{
									$zoom['config']['galArray'][$k]['mk']='gP';
								}
							}
							elseif ($zoom['config']['pyrTiles']){
								if ( (($v['imgSize'][0] < $zoom['config']['tileSize'] && $v['imgSize'][1] < $zoom['config']['tileSize']) OR file_exists($zoom['config']['pyrTilesDir'].$this->getf('.',$v['fileName']).'/0-0-0.'.$this->pngMod($zoom))) ){
									$zoom['config']['galArray'][$k]['mk'] = false;
									if ($zoom['config']['pyrAutoDetect']){
										$thisTileSize = $this->axZm->imageSize($zoom['config']['pyrTilesDir'].$this->getf('.',$v['fileName']).'/0-0-0.'.$this->pngMod($zoom), $zoom['config']['im'], false);
										$zoom['config']['galArray'][$k]['ts'] = intval(max($thisTileSize[0], $thisTileSize[1]));
									}
								}else{
									$zoom['config']['galArray'][$k]['mk'] = 'tL';
								}
							}else{
								$zoom['config']['galArray'][$k]['mk'] = false;
							}

						} // END: foreach ($pic_list_data as $k=>$v){
						
					} // END: if (!isset($_GET['setHW'])){
		
				} // END: if (!isset($_GET['str']))
				
				if (isset($pic_list_data[$_GET['zoomID']]['path'])){
					$zoom['config']['pic'] = $this->checkSlash($zoom['config']['pic'].'/'.$pic_list_data[$_GET['zoomID']]['path'], 'add');
				}
			
			} // END: if ($_GET['zoomID'])
		
		} // END: if (!empty($pic_list_array) AND !empty($pic_list_data))
		else{
			unset ($_GET['zoomID']);
		}
		
		return array($zoom, $zoomTmp);


	}


	/**
	  * Returns formated array or string from numeric seconds amount
	  * @access public
	  * @param int $time Number of seconds
	  * @param string $ret Return type - 'string' or 'array'
	  * @return mixed
	  **/
	public function seconds2time($time, $ret = 'string'){
		if(is_numeric($time)){
			$value = array("years" => 0, "days" => 0, "hours" => 0, "minutes" => 0, "seconds" => 0);
			if ($time >= 31556926){
				$value['years'] = floor($time/31556926);
				$time = ($time%31556926);
			}
			if ($time >= 86400){
				$value['days'] = floor($time/86400);
				$time = ($time%86400);
			}
			if ($time >= 3600){
				$value['hours'] = floor($time/3600);
				$time = ($time%3600);
			}
			if($time >= 60){
				$value['minutes'] = floor($time/60);
				$time = ($time%60);
			}
			$value['seconds'] = floor($time);
			if ($ret == 'string'){
				$string = '';
				foreach ($value as $k=>$v){
					if ($v > 0){
						$string .= $v.' '.ucfirst($k).', ';
					}
				}
				$string = substr($string,0, -2);
				return $string;
			}else{
				return $value;
			}
		}else{
			return false;
		}
	}
	
	/**
	  * Generates a html table for files in specified folder. Used in zoomBatch.php
	  * @access public
	  * @param array $zoom
	  * @param array $pic_list_array
	  * @param array $pic_list_data
	  * @return HTML-Output
	  **/
	public function batchList($zoom, $pic_list_array, $pic_list_data){
		$batchListColor='#FFFFFF';
		if (!empty($pic_list_array)){
								
			$return = "<DIV style=\"position: fixed\"><table class=\"leftFrameTable\" cellspacing=\"0\" cellpadding=\"1\">";
			$return .= "<thead><tr>";
				$return .= "<th style=\"width: 22px;\">&nbsp;</th>";
				$return .= "<th>Filename</th>";
				
				// Initial image
				if ($zoom['batch']['arrayMake']['In']){
					$return .= "<th style=\"width: 16px;\">In</th>";
				}
				
				// Thumbs
				if ($zoom['batch']['arrayMake']['Th']){
					$return .= "<th style=\"width: 16px;\">Th</th>";
				}
				
				//gPyramid
				if ($zoom['batch']['arrayMake']['gP']){
					$return .= "<th style=\"width: 16px;\">gP</th>";
				}
				
				// Pyramid
				if ($zoom['batch']['arrayMake']['Ti']){
					$return .= "<th style=\"width: 16px;\">Ti</th>";
				}
				
				if ($zoom['batch']['allowDelete']){
					$return .= "<th style=\"width: 16px;\">&nbsp;</th>";
				}

				$return .= "<th style=\"width: 70px;\">Imgsize</th>";
				$return .= "<th style=\"width: 45px;\">Filesize</th>";
				// Pic preview
				$return .= "<th style=\"width: 18px;\">&nbsp;</th>";
			$return .= "</tr></thead></table>";
			$return .= "</DIV><DIV style=\"margin-top: 18px;\">";
			$return .= "<table class=\"leftFrameTable\" id=\"leftFrameTable\" cellspacing=\"0\" cellpadding=\"1\">";
			foreach ($pic_list_array as $k=>$v){
				if ($batchListColor=='#FFFFFF'){$batchListColor='#EEEEEE';}
				else {$batchListColor='#FFFFFF';}
				$return .= "<tbody><tr id=\"d$k\" style=\"background-color: $batchListColor\">";
					$return .= "<td style=\"width: 22px;\"><input type=\"checkBox\" name=\"f$k\" id=\"f$k\" value=\"1\"></td>";
					$return .= "<td id=\"fname$k\">".wordwrap($v,30,'<br>',true)."</td>";
					
					// Initial image
					if ($zoom['batch']['arrayMake']['In']){
						$return .= "<td style=\"width: 16px;\">".(file_exists($zoom['config']['thumbDir'].$this->composeFileName($v, $zoom['config']['picDim'], '_', $this->pngMod($zoom))) ? str_replace('<img','<img id="In'.$k.'"', $zoom['batch']['iconOk']) : str_replace('<img','<img id="In'.$k.'"', $zoom['batch']['iconError']))."</td>";
					}
					
					// Thumbs
					if ($zoom['batch']['arrayMake']['Th']){
						$errThumb = $thumbExists = $thumbFullExists = false;

						if ($zoom['config']['useGallery'] || $zoom['config']['fullScreenVertGallery']){
							$thumbExists = file_exists($zoom['config']['galleryDir'].$this->composeFileName($v, $zoom['config']['galleryPicDim'], '_', $this->pngMod($zoom))) ? true : false;
						}
						if ($zoom['config']['useFullGallery']){
							$thumbFullExists = file_exists($zoom['config']['galleryDir'].$this->composeFileName($v, $zoom['config']['galleryFullPicDim'], '_', $this->pngMod($zoom))) ? true : false;
						}
						
						if ($zoom['config']['useGallery'] AND !$thumbExists){$errThumb = true;}
						if ($zoom['config']['useFullGallery'] AND !$thumbFullExists){$errThumb = true;}
						
						if (!$zoom['config']['useGallery'] AND !$zoom['config']['useFullGallery']){
							$thumbExists = file_exists($zoom['config']['galleryDir'].$this->composeFileName($v, $zoom['config']['galleryPicDim'], '_', $this->pngMod($zoom))) ? true : false;
							$thumbFullExists = file_exists($zoom['config']['galleryDir'].$this->composeFileName($v, $zoom['config']['galleryFullPicDim'], '_', $this->pngMod($zoom))) ? true : false;
							if (!$thumbExists AND !$thumbFullExists){


								$errThumb = true;
							}
						}
						
						$iconThumb = $errThumb ? $zoom['batch']['iconError'] : $zoom['batch']['iconOk'];
						$iconThumb = str_replace('<img','<img id="Th'.$k.'"', $iconThumb);
						$return .= "<td style=\"width: 16px;\">$iconThumb</td>";
					}
					
					// gPyramid
					if ($zoom['batch']['arrayMake']['gP']){
						$return .= "<td style=\"width: 16px;\">".(is_dir($zoom['config']['gPyramidDir'].$this->getf('.',$v)) ? str_replace('<img','<img id="gP'.$k.'"', $zoom['batch']['iconOk']) : str_replace('<img','<img id="gP'.$k.'"', $zoom['batch']['iconError']))."</td>";
					}
					
					// Pyramid
					if ($zoom['batch']['arrayMake']['Ti']){
						$return .= "<td style=\"width: 16px;\">".(file_exists($zoom['config']['pyrTilesDir'].$this->getf('.',$v).'/0-0-0.'.$this->pngMod($zoom)) ? str_replace('<img','<img id="Ti'.$k.'"', $zoom['batch']['iconOk']) : str_replace('<img','<img id="Ti'.$k.'"', $zoom['batch']['iconError']))."</td>";
					}
					
					// Delete
					if ($zoom['batch']['allowDelete']){
						$return .= "<td style=\"width: 16px;\">".str_replace('<img',"<img onclick=\"jQuery.zoomBatch.deleteZoom($k)\"", $zoom['batch']['iconTrash'])."</td>";
					}
					
					$return .= "<td style=\"width: 70px;\">".$pic_list_data[$k]['imgSize'][0]." x ".$pic_list_data[$k]['imgSize'][1]."</td>";
					$return .= "<td style=\"width: 45px;\">".$this->zoomFileSmartSize($pic_list_data[$k]['fileSize'],1)."</td>";
					$return .= "<td style=\"width: 18px;\"><img src=\"".$zoom['config']['icon']."batch_thumb.png\" id=\"prev$k\" width=\"16\" height=\"16\" border=\"0\" style=\"cursor: pointer\" onclick=\"jQuery.zoomBatch.previewPic($k,\'$v\',".$pic_list_data[$k]['imgSize'][0].",".$pic_list_data[$k]['imgSize'][1].")\" title=\"Preview\"></td>";
				$return .= "</tr>";
			}
			$return .= "</tbody></table></DIV>";
		}else{
			$return .= "<div class=\"processMsg\">".$zoom['batch']['iconWarning']." No files in \$pic_list_array</div>";
		}
		
		return $return;
	}

	/**
	  * Generates a html table for files, that have been batch processed. Used in zoomBatch.php
	  * @access public
	  * @param array $zoom
	  * @return HTML-Output
	  **/
	
	public function batchProcess($zoom){
		$return = '';
		$return .= "<table id=\"processTable\" class=\"batchProcessTable\" cellspacing=\"0\" cellpadding=\"1\">";
			$return .= "<thead><tr>";
				$return .= "<th>Filename</th>";
				$return .= "<th width=\"20px\">In</th>";
				$return .= "<th width=\"20px\">Th</th>";
				$return .= "<th width=\"20px\">gP</th>";
				$return .= "<th width=\"20px\">Ti</th>";
				$return .= "<th width=\"45px\">Time</th>";
				if (isset($zoom['batch']['batchThumbs'])){
					$return .= "<th style=\"width: ".($zoom['config'][$zoom['batch']['batchThumbs'].'X']+4)."px;\">&nbsp;</th>";
				}
			$return .= "</tr></thead>";
			$return .= "<tbody></tbody>";
		$return .= "</table>";
		
		return $return;
	}

	/**
	  * This method is used in zoomBatch.php to get the directory tree. Used in zoomBatch.php
	  * @access public
	  * @param string $path defines the start (home) directory where images are located (for dropdown option list)
	  * @param string $baseDir should be $zoom['config']['fpPP'] as in zoomConfig.inc.php
	  * @param array $exclude an array of folders that should be excluded from the returned array
	  * @param int $levelString
	  * @param int $level
	  * @return array
	  **/
	  
	public function getDirTree($path = '', $baseDir = './', $exclude = array(), $levelString = 1, $level = 1){
		$return = $arr = $arrTemp = array();
		$excludeDefault = array('.', '..', 'cgi-bin');
		$excludeFinal = array_merge($excludeDefault, $exclude);
		
		$baseDir = $this->checkSlash($baseDir, 'remove');
		$openDir = $this->checkSlash($baseDir.$path, 'remove');
		
		if (!is_dir($openDir)) {return false;}
		
		$n=0;

		if ($level == 1){
			$return['HOME']['DIR_NAME'] = $this->getl('/', $this->checkSlash($path,'remove'));
			$return['HOME']['DIR_PATH'] = $path;
			//$return['HOME']['ROOT_PATH'] = $this->checkSlash($openDir,'add');
			$return['HOME']['DIR_LEVEL'] = 0;
		}

		foreach (glob($openDir.'/*', GLOB_ONLYDIR) as $file){
			$file = $this->getl('/', $this->checkSlash($file,'remove'));
			if( !in_array($file, $excludeFinal) ){ 
				$n++;
				$key = $levelString.'_'.$n;
				$return[$key]['DIR_NAME'] = $file;
				$return[$key]['DIR_PATH'] =  $this->checkSlash($path.'/'.$file,'remove');
				//$return[$key]['ROOT_PATH'] = $this->checkSlash("$openDir/$file",'add');
				$return[$key]['DIR_LEVEL'] = $level;
	
				$arr = $this->getDirTree($this->checkSlash($path.'/'.$file,'remove'), $baseDir, $exclude, $key, $level+1);
				if (!empty($arr)){
					$return=array_merge($return, $arr);
				}	
			}
		}

		return $return;
	} 

	/**
	  * Drow html options for select formfield out of an array. Used in zoomBatch.php
	  * @access public
	  * @param array $arr
	  * @param int|bool $sel The key of selected option, accepts false
	  * @return HTML-Output
	  **/
	  
	public function directoryOptions($arr=array(), $sel=false){
		$return = '';
		if (is_array($arr) AND !empty($arr)){
			foreach ($arr as $k=>$v){
				
				if ($v['DIR_LEVEL'] != 0){
					$pref = str_repeat ('&nbsp;',($v['DIR_LEVEL']-1)*4).'|&#151;';
				}else{
					$pref = '';
				}
				
				$return .= "<option class='opt_".$v['DIR_LEVEL']."' value='$k'";
				if ($sel==$k){$return .= " selected";}
				$return .= ">$pref ".$v['DIR_NAME']."</option>";
			}
		}
		return $return;
	}

	/**
	  * Drow html options for select formfield out of an array. Used in demo.
	  * @access public


	  * @param array $arr
	  * @param int|bool $sel The key of selected option, accepts false
	  * @param string|bool $opr A callback function on array value
	  * @param string|bool $add String to add after the value, eg. 'ms'
	  * @return HTML-Output
	  **/
	  
	public function sOptions($arr=array(), $sel=false, $opr=false, $add=false){
		$return=array();
		$oneD = false; $n = 0;
		foreach ($arr as $k=>$v){
			if ($n == 0){$oneD = ($k === 0) ? true : false;}
			$n++;
			if ($oneD === true){$k = $v;}
			$return .= "<option value=\"".$k."\"";
			if ($k == $sel OR $v == $sel){$return .= " selected";}
			$return .= ">";
			if (function_exists($opr)){$return .= $opr($v);}
			else {$return .= $v;}
			if ($add){$return .= ' '.$add;}
			$return .= "</option>";
		}
		return $return;
	}

	/**
	  * Removes initial image, gallery thumbs, gPyramid and tiles image files. Used in zoomBatch.php
	  * @access public
	  * @param array $zoom
	  * @param string $pic Orinal image filename
	  * @param array $arrDel Array that defines which images have to be deleted
	  * @param bool $self Defines if original image have to be deleted, default false
	  * @return nothing
	  **/
	
	// remove made initial image, gallery thumbs, gPyramid and tiles files
	// $zoom is the config var, $pic is the name of the original file
	public function removeAxZm($zoom, $pic, $arrDel = array(), $self = false){
	
		$picName = $this->getf('.',$pic);
		
		// Remove initial image(s)
		if ($arrDel['In'] === true){
			if (is_dir($zoom['config']['thumbDir'])){			
				foreach (glob($this->checkSlash($zoom['config']['thumbDir'],'add').$picName.'*.'.$this->pngMod($zoom)) as $file){
					// get the pure filename without jpg and without size (like _150x100)
					$fileName = $this->getf('_',$this->getf('.',$this->getl('/',$file)));
					if ($fileName == $picName){
						unlink($file);
					}
				}
			}
		}
		
		// Remove gallery thumbs
		if ($arrDel['Th'] === true){
			$zoom['config']['galleryDir'] = $this->checkSlash($zoom['config']['galleryDir'],'add');
			if (is_dir($zoom['config']['galleryDir'])){
				foreach (glob($this->checkSlash($zoom['config']['galleryDir'],'add').$picName.'*.'.$this->pngMod($zoom)) as $file){
					$fileName = $this->getf('_',$this->getf('.',$this->getl('/',$file)));
					if ($fileName == $picName){
						unlink($file);
					}			
				}
			}
		}
		
		// Remove gPyramid
		if ($arrDel['gP'] === true){
			$zoom['config']['gPyramidDir'] = $this->checkSlash($zoom['config']['gPyramidDir'],'add');
			if (is_dir($zoom['config']['gPyramidDir'])){
				if (is_dir($zoom['config']['gPyramidDir'].$picName)){
					$handle = opendir($zoom['config']['gPyramidDir'].$picName);
					if (is_resource($handle)){
						while (false !== ($file = readdir($handle))){ 
							if (is_file($zoom['config']['gPyramidDir'].$picName.'/'.$file)){
								unlink($zoom['config']['gPyramidDir'].$picName.'/'.$file);
							}
						}
					}
					closedir($handle);
					rmdir($zoom['config']['gPyramidDir'].$picName);
				}
			}
		}
		
		// Remove tiles
		if ($arrDel['Ti'] === true){
			$zoom['config']['pyrTilesDir'] = $this->checkSlash($zoom['config']['pyrTilesDir'],'add');
			if (is_dir($zoom['config']['pyrTilesDir'])){
				if (is_dir($zoom['config']['pyrTilesDir'].$picName)){
					$handle = opendir($zoom['config']['pyrTilesDir'].$picName);
					if (is_resource($handle)){
						while (false !== ($file = readdir($handle))){ 
							if (is_file($zoom['config']['pyrTilesDir'].$picName.'/'.$file)){
								unlink($zoom['config']['pyrTilesDir'].$picName.'/'.$file);
							}
						}
					}
					closedir($handle);
					rmdir($zoom['config']['pyrTilesDir'].$picName);
				}
			}
		}
		
		// remove original file, default false!
		if ($self === true){
			$zoom['config']['picDir'] = $this->checkSlash($zoom['config']['picDir'],'add');
			if (file_exists($zoom['config']['picDir'].$pic)){
				unlink ($zoom['config']['picDir'].$pic);
			}
		}
		
		return;
	}
	
	/**
	  * Delete zoom cache files, inited in zoomSess.php on load
	  * @access public
	  * @param string $cacheDir Cache directory, should be $zoom['config']['tempDir']
	  * @param integer $maxTime Min. difference of cache filetime from "now"
	  * @return nothing
	  **/	
	
	public function delteZoomCache($cacheDir, $maxTime){
		if ($maxTime < 300){$maxTim=300;}
		$dateNow=strtotime("now");
		foreach (glob($cacheDir."*.".$this->pngMod($zoom)) as $fname){
			if (strpos($fname, 'zoom_') == true){
				if (($dateNow-filemtime($fname)) > $maxTime){ // Seconds 3600=1h
					unlink($fname);
				}
			}
		}
	}

	/**
	  * Checks if slashes in paths are set correctly.
	  * @access public
	  * @param string $input Input path as string
	  * @param bool $mode case 'remove' removes last slash, case 'add' adds slash to the end
	  * @return string
	  **/

	public function checkSlash($input, $mode = false){ // updated 2010-10-22
		// Replace backslashes
		$input = str_replace('\\', '/', $input);	
		
		// Remove doubleslashes in $input
		$input = preg_replace('/\/+/', '/', $input);
		
		// Remove slash at the end of $input
		if ($mode == 'remove'){
			if (substr($input,-1) == '/'){
				$input = substr($input,0,-1);
			}
		}
		
		// Add slash at the end of $input
		elseif ($mode == 'add'){
			if (substr($input,-1) != '/' AND strlen($input)>0){
				$input.='/';
			}
		}

		return $input;
	}
	
	/**
	  * Convert php values to js values.
	  * @access public
	  * @param mixed $a Input php var
	  * @return string
	  **/
	public function ptj($a){
		// boolean
		if ($a === true){return 'true';}
		elseif ($a === false){return 'false';}
		// integer, written object
		elseif (is_int($a) || is_float($a) || stristr($a,'}')){return $a;}
		// string
		elseif ($a){
			$a = str_replace("\n",'',$a);
			$a = preg_replace('/\s+/', ' ', $a);
			return "'".$a."'";
		}
		// nothing
		else {return "''";}
	}

	/**
	  * Rewrites $integer in appropriete measure MB, KB or BYTES
	  * @access public
	  * @param int $integer
	  * @param int $digits Precision
	  * @return string
	  **/
	  
	public function zoomFileSmartSize($integer, $digits){
		if (!$integer){$integer = 0;}
		if (!$digits){$dig='2';}
		settype ($integer,'int');
		settype ($digits,'int');
		if ($integer>=1048576){$integer = round(($integer/1024000),$digits) . " MB";}
		elseif ($integer>=1024){$integer = round(($integer/1024),0) . " KB";}
		elseif ($integer>=0){$integer = $integer . " BYTES";}
		else{$integer = "0 BYTES";}
		return $integer;
	}
	
	/**
	  * Returns filetype (extension)
	  * @access public
	  * @param int $char
	  * @param string $str
	  * @return string
	  **/
	  
	public function getl($char, $str){
		$pos=strrpos($str,$char);
		$ext=substr($str,$pos+1);
		return $ext;
	}
	
	/**
	  * Returns filename without extension
	  * @access public
	  * @param int $char
	  * @param string $str
	  * @return string
	  **/
	public function getf($char, $str){
		$pos=strrpos($str,$char);
		$ext=substr($str,0,$pos);
		return $ext;
	}
	
	/**
	  * Returns composed filename out of input par
	  * Example input: $file = 'image.jpg', $ext = 'test', $sep = '_'
	  * Example return: image_test.jpg 
	  * @access public
	  * @param string $file
	  * @param string $ext
	  * @param string $sep
	  * @return string
	  **/	
	  
	public function composeFileName($file, $ext, $sep, $fType = 'jpg'){
		$return = $this->getf('.',$file);
		$return .= $sep;
		$return .= $ext;
		$return .= '.';
		//$return .= $this->getl('.',$file); Ver. 2.1.5 return jpg filetype only
		$return .= $fType;
		return $return;
	}
	

	/**
	  * Sorts an array and rebuilds index keys starting from 1
	  * @access public
	  * @param array $array
	  * @param bool $reverse
	  * @return array
	  **/	  
	public function natIndex($array, $reverse){
		$i=1; $nArray=array();
		natcasesort($array);
		if ($reverse){
			$array = array_reverse($array);
		}
		foreach ($array as $k=>$v){
			$nArray[$i]=$v;
			$i++;
		}
		return $nArray;
	}
	
	/**
	  * Generatens a random string which has $len characters
	  * @access public
	  * @param int $len
	  * @return array
	  **/
	public function rndNum($len){
		$return = '';
		$passwordChars = '0123456789'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'abcdefghijklmnopqrstuvwxyz'; 
		for ($index = 1; $index <= $len; $index++){
			$randomNumber = rand(1,strlen($passwordChars));
			$return .=substr($passwordChars,$randomNumber-1,1);
		}
		return $return;
	}	
	
	/**
	  * Chmod all directories in $path with $mode
	  * @access public
	  * @param int $path
	  * @param int $mode
	  * @return array
	  **/
	public function chmodAllDir($path, $mode){
		$chmodArray=array(0600,0644,0755,0750,0777);
		if (in_array($mode,$chmodArray)){
			foreach (glob($path.'*', GLOB_ONLYDIR) as $dirName){
				chmod($dirName, $mode);
			}
		}
	}

	/**
	  * Converts a string to UTF-8
	  * @access public
	  * @param string $t
	  * @return string
	  **/
	public function numeric_to_utf8($t)
	{  
		if (function_exists('mb_decode_numericentity')){
			$convmap = array(0x0, 0x2FFFF, 0, 0xFFFF);
			return mb_decode_numericentity($t, $convmap, 'UTF-8');
		}else{
			return $t;
		}
	}
		
	/**
	  * Converts multidemensional php array $phpArray into javascript array
	  * @access public
	  * @param array $phpArray
	  * @param string $jsArrayName
	  * @return string
	  **/
	public function arrayToJSArray($phpArray, $jsArrayName, &$html = '') {
		$html .= $jsArrayName . "=new Array();";
		foreach ($phpArray as $key => $value) {
				$outKey = (is_int($key)) ? '[' . $key . ']' : "['" . $key . "']";
				if (is_array($value)) {
						$this->arrayToJSArray($value, $jsArrayName . $outKey, $html);
						continue;
				}
				$html .= $jsArrayName . $outKey . "=";
				
				if (is_string($value)) {
					$html .= "'" . $value . "';";
				} else if ($value === false) {
					$html .= "false;";
				} else if ($value === NULL) {
					$html .= "null;";
				} else if ($value === true) {
					$html .= "true;";
				} else {
					$html .= "'".$value . "';";
				}
		}	   
		return $html;
	}

	/**
	  * Converts multidemensional php array $phpArray into javascript object
	  * @access public
	  * @param array $array
	  * @param string $varname
	  * @param bool $sub
	  * @param bool $rn If true adds linebreak after each line
	  * @param bool $string If true any value will be regarded as string
	  * @return string
	  **/	
	private function arrayToJSObject($array, $varname, $sub = false, $rn = false, $string = false) {
		$rnStr='';
		if ($rn){$rnStr="\n";} 

		$jsarray = $sub ? $varname . "{" : $varname . " = {".$rnStr; 
		$varname = "\t$varname"; 
		reset ($array); 
	
		// Loop through each element of the array 
		while (list($key, $value) = each($array)) { 
			$jskey = "'" . $key . "' : "; 

			if (is_array($value)) { 
				// Multi Dimensional Array 
				$temp[] = $this->arrayToJSObject($value, $jskey, true, $rn, $string); 
			} else { 
				if (is_numeric($value) && !$string) { 
					$jskey .= "$value"; 
				} elseif (is_bool($value) && !$string) { 
					$jskey .= ($value ? 'true' : 'false') . ""; 
				} elseif ($value === NULL) { 
					$jskey .= "null"; 
				} else { 
					static $pattern = array("\\", "'", "\r", "\n"); 
					static $replace = array('\\', '\\\'', '\r', '\n'); 
					$jskey .= "'" . str_replace($pattern, $replace, $value) . "'"; 
				} 
				$temp[] = $jskey; 
			} 
		} 
		$jsarray .= implode(', ', $temp); 
	
		$jsarray .= "}".$rnStr; 

		return $jsarray; 
	} 


	/**
	  * Determins the size of an resized image, returns an array with dimensions
	  * @access public
	  * @param array $oSize
	  * @param array $rSize
	  * @return array
	  **/	
	public function virtualResize($oSize=array(), $rSize=array()){
		$w = $rSize[0]; 
		$h = $rSize[1];
		$sw = $oSize[0];
		$sh = $oSize[1];
		if (($w/$sw)>($h/$sh)){$prc=$h/$sh;}
		else{$prc=$w/$sw;}
		$w=round($sw*$prc);
		$h=round($sh*$prc);
		return array($w,$h);
	}
		
	/**
	  * This method will take the query string or an array and return either an array with parameters and their values ($ret = 'arr') or 
	  * it will return a new query string ($ret = 'str')
	  * $parExcl are parameters from query string which have to be excluded
	  * $parExcl can be passed as single string (like 'zoomID') or as an array e.g. $parExcl = array('zoomID', 'yourOtherPar', ...)
	  * $parExclPreg is optional array like $parExcl, but it searches for specific string in parameter and excludes it if found
	  * this function can be used for jQuery.axZm['parToPass'], 
	  * ajax will then append the string to the query in zoomLoad.php
	  * Example: query string is: prodID=895&language=EN&zoomID=3&template=xp&somthElse=abc
	  * $aaa = zoomServerPar($ret='str',array('zoomID',somthElse),false);
	  * echo $aaa; //result: prodID=895&language=EN&template=xp
	  * $bbb = zoomServerPar($ret='arr',array('zoomID','somthElse'),zoomServerPar);
	  * print_r($bbb): //result: array('prodID' => '895', 'language' => 'EN', 'template' => 'xp')
	  * $ccc = zoomServerPar($ret='str', $parExcl='zoomID', $parExclPreg=array('mthE'));
	  * echo $ccc; //result: prodID=895&language=EN&template=xp
	  * @access public
	  * @param string $ret Possible values: arr or str
	  * @param array|string $parExcl
	  * @param array $parExclPreg
	  * @param array|string $queryString
	  **/	

	public function zoomServerPar ($ret, $parExcl = false, $parExclPreg = false, $queryString = false){
		$return=array();
		if (!$parExcl AND !$parExclPreg AND is_string($queryString)){return $queryString;}
		if (!$queryString){$queryString = $_SERVER['QUERY_STRING'];} // string
		
		if ($queryString){
			if (is_array($queryString)){ // e.g. $_GET
				$parArr = $queryString;
			}else{
				$parArr = explode('&',$queryString);
			}
			
			foreach ($parArr as $key => $par){
				if (is_array($queryString)){
					$k=$key; $v=$par;
				}else{
					$kv=explode('=',$par);
					$k=$kv[0]; $v=$kv[1];
				}
				if (is_array($parExcl)){
					if (!in_array($k,$parExcl)){
						$returnArray[$k]=$v;
					}
				} elseif (is_string($parExcl)){
					if ($parExcl != $k){
						$returnArray[$k]=$v;
					}
				}
			}
			if (is_array($parExclPreg) AND !empty($returnArray)){
				$returnArrayTemp=$returnArray;
				foreach ($parExclPreg as $k){
					foreach ($returnArrayTemp as $kk=>$vv){
						if (stristr($kk,$k)){
							unset($returnArray[$kk]);
						}
					}
				}
			}
			if (!empty($returnArray)){
				if ($ret=='arr'){
					return $returnArray;
				}elseif ($ret=='str'){
					$strArray=array();
					foreach ($returnArray as $k=>$v){
						array_push($strArray,$k.'='.$v);
					}
					return implode('&',$strArray);
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}
	
	/**
	  * Returns all needed css files
	  * @access public
	  * @param array $zoom
	  * @return HTML-Output
	  **/	
	public function drawZoomStyle($zoom){
		$return = '';
		$jsPath = $zoom['config']['js'];
		$cssLink = array();
		
		// Main AjaxZoom Css
		array_push ($cssLink,'axZm.css');
		
		// Scrollpane
		if ($zoom['config']['scrollPane']){
			array_push ($cssLink,'plugins/jScrollPane/style/jquery.jscrollpane.css');
			if ($zoom['config']['scrollPaneTheme']){
				array_push ($cssLink,'plugins/jScrollPane/themes/'.$zoom['config']['scrollPaneTheme'].'/style/jquery.jscrollpane.'.$zoom['config']['scrollPaneTheme'].'.css');
			}
		}

		// UI Slider
		if ($zoom['config']['jsUiAll']){
			array_push ($cssLink,'plugins/jquery.ui/themes/'.$zoom['config']['jsUiTheme'].'/jquery-ui.css');
		} else {
			array_push ($cssLink,'plugins/jquery.ui/themes/'.$zoom['config']['jsUiTheme'].'/jquery.ui.core.css');
			array_push ($cssLink,'plugins/jquery.ui/themes/'.$zoom['config']['jsUiTheme'].'/jquery.ui.theme.css');
			array_push ($cssLink,'plugins/jquery.ui/themes/'.$zoom['config']['jsUiTheme'].'/jquery.ui.slider.css');
		}
		
		// Only for Demo
		if ($zoom['config']['visualConf']){
			array_push ($cssLink,'plugins/demo/axZm.demo.css');
			array_push ($cssLink,'plugins/demo/colorpicker/css/colorpicker.css');
		}
		
		foreach ($cssLink as $k=>$v){
			$return .= "\n<link rel=\"stylesheet\" href=\"".$jsPath.$v."\" media=\"screen\" type=\"text/css\">";
		}
		$return .= "\n";
		
		return $return;
	}

	/**
	  * Returns all needed javascript files
	  * @access public
	  * @param array $zoom
	  * @param array $exclude JS files to exclude
	  * @return HTML-Output
	  **/
	public function drawZoomJs($zoom, $exclude = array()){
		// For application
		/*
		$exclude=array(
			'jquery',
			'ui.core',
			'ui.widjet',
			'ui.mouse'
			'ui.draggable',
			'ui.slider',
			'effects.core',
			'browser',
			'mousewheel',
			'jScrollPane',
			'facebox',
			'axZm'
		);
		*/
		
		// For demo
		//$exclude=array('scrollTo','colorpicker','form','axZmDemo');		
		
		$return = '';
		$jsPath = $zoom['config']['js'];
		$min = $zoom['config']['jsMin'];
		
		$js = array();

		// Javascripts jquery core
		if (!in_array('jquery',$exclude)){
			array_push ($js,'plugins/jquery-1.7.2.js');
		}

		// Javascripts jquery ui
		if ($zoom['config']['jsUiAll']){
			array_push ($js,'plugins/jquery.ui/js/jquery-ui-'.$zoom['config']['jsUiVer'].'.custom.js');
		}else{
			array_push ($js,'plugins/jquery.ui/js/jquery-ui-'.$zoom['config']['jsUiVer'].'.axZm.js');
		}

		## Javascripts jquery plugins ##
		
		// Browser detection
		if (!in_array('browser',$exclude)){
			array_push ($js,'plugins/jquery.browser.js');
		}

		// Mousewheel is needed for scrolling
		if (!in_array('mousewheel',$exclude)){
			array_push ($js,'plugins/jquery.mousewheel.js');
		}
		
		// Crolling area for vertical gallery
		if (!in_array('jScrollPane',$exclude)){
			array_push ($js,'plugins/jScrollPane/script/jquery.jscrollpane.js');
		}
		
		// Zoom Main script
		if (!in_array('axZm',$exclude)){
			array_push ($js,'jquery.axZm.js'); 
		}

		// Only for Demo with visual configuration
		if ($zoom['config']['visualConf']){
			if (!in_array('scrollTo',$exclude)){
				array_push ($js,'plugins/jquery.scrollTo.js');
			}	
			if (!in_array('colorpicker',$exclude)){
				array_push ($js,'plugins/demo/colorpicker/js/colorpicker.js');
			}
			if (!in_array('form',$exclude)){
				array_push ($js,'plugins/demo/jquery.form.js');
			}

			if (!in_array('axZmDemo',$exclude)){
				array_push ($js,'plugins/demo/jquery.axZm.demo.js');
			}
		}

		foreach ($js as $k=>$v){
			if ($min && !stristr($v,'axZm')){
				$v = $this->getf('.',$v).'.min.js';
				//$v = str_replace('.js','.min.js',$v);
			}
			$return .= "\n<script type=\"text/javascript\" src=\"$jsPath$v\"></script>";
		}
		$return .= "\n";
		
		if ($zoom['config']['visualConf']){
			$return .= "
				<script type=\"text/javascript\">
				jQuery.optSubmit = function(){
					jQuery.ajaxSubmitCustom('demoOptions','zoomOpr','".$this->installPath()."/axZm/zoomVisualConf.inc.php');
				};
				</script>
			";
		}

		return $return;
	}

	/**
	  * Returns all needed javascript for onLoad
	  * @access public
	  * @param array $zoom
	  * @param bool $pack Use packer
	  * @param bool $windowLoad If true the method should be called in any case in the head section of html
	  * @param string $jsObject Options javascript object for jQuery.fn.axZm
	  * @return HTML-Output
	  **/
	
	public function drawZoomJsLoad($zoom, $pack = false, $windowLoad = true, $jsObject){
		$js='';
		if (!$jsObject){$jsObject = '{}';}
		if ($windowLoad){
			$js='jQuery(window).load(function(){
					jQuery.fn.axZm('.$jsObject.');
			';
		}else{
			$js='
				jQuery.fn.axZm('.$jsObject.');
			';
		}

		if ($zoom['config']['visualConf']){
			
			$js .= '
				jQuery(\'#demoOptions\').ajaxForm();
				jQuery.colPick(\'demoColorStage\',\'demoColorStage\');
				jQuery.colPick(\'demoBodyColor\',\'demoBodyColor\');
				jQuery.colPick(\'demoColorArea\',\'demoColorArea\');
				jQuery.colPick(\'demoColorOuter\',\'demoColorOuter\');
				jQuery.colPick(\'demoColorBorder\',\'demoColorBorder\');
				
				jQuery.demoAnm = true;
				jQuery(\'#zoomAjaxDemoButton\').click(function () {
				  jQuery(\'#zoomAjaxDemo\').slideToggle(300);
				});
				
				jQuery(\'#zoomAjaxDemoButton\').mouseover(function () {
					jQuery(this).css(\'color\',\'#F4E10A\');
				}).mouseout(function () {
					jQuery(this).css(\'color\',\'#FFFFFF\');
				});
			';
			
		}
		if ($windowLoad){
			$js.="
			});";
		}
		if ($pack){
			$myPacker = new JavaScriptPacker($js, 'Normal', true, false);
			$js = $myPacker->pack();
		}
		$js = "<script type=\"text/javascript\">$js</script>";
		return $js;		
	}

	/**
	  * Returns all needed configuration variables in javascript
	  * @access public
	  * @param array $zoom
	  * @param bool $rn Add line break after each line of code 
	  * @param bool $pack Use packer
	  * @return Javascript-Output
	  **/	
	public function drawZoomJsConf($zoom, $rn = false, $pack = true){
		$rnStr = '';
		if ($rn){$rnStr = "\n";}
		
		// General
		$js = 'if (jQuery.axZm){delete jQuery.axZm;} jQuery.axZm = {}; ';
		
		//$js .= 'jQuery.axZm.version = '.$this->ptj($_GET['version']).'; ';
		$js .= 'jQuery.axZm.zoomID = '.$this->ptj($_GET['zoomID']).'; ';
		$js .= 'jQuery.axZm.randNum = '.$this->ptj($this->rndNum(24)).'; ';
		
		$js .= 'jQuery.axZm.icon = '.$this->ptj($zoom['config']['icon']).'; ';
		$js .= 'jQuery.axZm.iconDir = '.$this->ptj($zoom['config']['icon']).'; '; // Alias
		
		$js .= 'jQuery.axZm.js = '.$this->ptj($zoom['config']['js']).'; ';
		$js .= 'jQuery.axZm.jsDir = '.$this->ptj($zoom['config']['js']).'; ';
		
		$js .= 'jQuery.axZm.jsDynLoad = '.$this->ptj($zoom['config']['jsDynLoad']).'; ';
		$js .= 'jQuery.axZm.jsMin = '.$this->ptj($zoom['config']['jsMin']).'; ';
		$js .= 'jQuery.axZm.jsUiAll = '.$this->ptj($zoom['config']['jsUiAll']).'; ';  
		
		$js .= 'jQuery.axZm.jsUiVer = '.$this->ptj($zoom['config']['jsUiVer']).'; ';
		
		$js .= 'jQuery.axZm.jsUiTheme = '.$this->ptj($zoom['config']['jsUiTheme']).'; ';
		
		
		$js .= 'jQuery.axZm.thumbs = '.$this->ptj($zoom['config']['thumbs']).'; ';
		$js .= 'jQuery.axZm.smallImgPath = '.$this->ptj($zoom['config']['thumbs']).'; '; // Alias
		
		// smallImageName is composed out of orgImgName + _ + picDim, e.g. test.jpg / test_800x600.jpg
		$js .= 'jQuery.axZm.smallImg = '.$this->ptj($zoom['config']['thumbs'].$zoom['config']['smallImgName']).'; ';
		
		// Ver. 2.1.5+
		//$js .= 'jQuery.axZm.fileType = '.$this->ptj($this->getl('.', $zoom['config']['orgImgName'])).'; ';
		//$js .= 'jQuery.axZm.fileBase = '.$this->ptj($this->getf('.', $zoom['config']['orgImgName'])).'; ';
		
		if ($zoom['config']['cropNoObj']){
			$js .= 'jQuery.axZm.pic = '.$this->ptj($zoom['config']['pic']).'; ';
			$js .= 'jQuery.axZm.orgPath = '.$this->ptj($zoom['config']['pic']).'; '; // Alias
		}
		
		// jQuery.axZm.iw and jQuery.axZm.ih are REAL width and hight of the preview image generated out of big image
		$js .= 'jQuery.axZm.iw = '.$this->ptj($zoom['config']['smallImgSize'][0]).'; ';
		$js .= 'jQuery.axZm.ih = '.$this->ptj($zoom['config']['smallImgSize'][1]).'; ';
		
		$js .= 'jQuery.axZm.ow = '.$this->ptj($zoom['config']['orgImgSize'][0]).'; ';
		$js .= 'jQuery.axZm.oh = '.$this->ptj($zoom['config']['orgImgSize'][1]).'; ';


		$js .= 'jQuery.axZm.smallFileSize = '.$this->ptj($zoom['config']['smallFileSize']).'; ';
		$js .= 'jQuery.axZm.parToPass = '.$this->ptj($zoom['config']['parToPass']).'; ';
		
		$js .= 'jQuery.axZm.domain = '.$this->ptj($zoom['config']['domain']).'; ';
		
		$js .= 'jQuery.axZm.visualConf = '.$this->ptj($zoom['config']['visualConf']).'; ';
		$js .= 'jQuery.axZm.errors = '.$this->ptj($zoom['config']['errors']).'; ';
		
		// Layout
		$js .= 'jQuery.axZm.keepBoxW = '.$this->ptj($zoom['config']['keepBoxW']).'; ';
		$js .= 'jQuery.axZm.keepBoxH = '.$this->ptj($zoom['config']['keepBoxH']).'; ';
		$js .= 'jQuery.axZm.boxW = '.$this->ptj($zoom['config']['picX']).'; '; // set out of $zoom['config']['picDim']
		$js .= 'jQuery.axZm.boxH = '.$this->ptj($zoom['config']['picY']).'; '; // // set out of $zoom['config']['picDim']
		$js .= 'jQuery.axZm.gravity = '.$this->ptj($zoom['config']['gravity']).'; ';
		$js .= 'jQuery.axZm.traveseGravity = '.$this->ptj($zoom['config']['traveseGravity']).'; ';
		$js .= 'jQuery.axZm.disableZoom = '.$this->ptj($zoom['config']['disableZoom']).'; ';  
		$js .= $this->arrayToJSArray($zoom['config']['disableZoomExcept'],'jQuery.axZm.disableZoomExcept').'; ';  
		
		$js .= 'jQuery.axZm.pngMode = '.$this->ptj($zoom['config']['pngMode']).'; ';
		$js .= 'jQuery.axZm.pngKeepTransp = '.$this->ptj($zoom['config']['pngKeepTransp']).'; ';
		
		$js .= 'jQuery.axZm.forceToPan = '.$this->ptj($zoom['config']['forceToPan']).'; ';
		
		if ($zoom['config']['pyrTiles']){
			if ($zoom['config']['pyrAutoDetect']){
				if (file_exists($zoom['config']['pyrTilesDir'].$this->getf('.',$zoom['config']['orgImgName']).'/0-0-0.'.$this->pngMod($zoom))){
					$thisTileSize = $this->axZm->imageSize($zoom['config']['pyrTilesDir'].$this->getf('.',$zoom['config']['orgImgName']).'/0-0-0.'.$this->pngMod($zoom), $zoom['config']['im'], false);
					$zoom['config']['tileSize'] = intval(max($thisTileSize[0], $thisTileSize[1]));
				}
			}
		}
		
		$js .= 'jQuery.axZm.tileSize = '.$this->ptj($zoom['config']['tileSize']).'; ';
		$js .= 'jQuery.axZm.pyrLoadTiles = '.$this->ptj($zoom['config']['pyrLoadTiles']).'; ';
		if ($zoom['config']['pyrLoadTiles']){
			$js .= 'jQuery.axZm.pyrTilesPath = '.$this->ptj($zoom['config']['pyrTilesPath']).'; ';
			$js .= 'jQuery.axZm.pyrTilesFadeInSpeed = '.$this->ptj($zoom['config']['pyrTilesFadeInSpeed']).'; ';
			$js .= 'jQuery.axZm.pyrTilesFadeLoad = '.$this->ptj($zoom['config']['pyrTilesFadeLoad']).'; ';
		}
		
		// Zoom Map
		$js .= 'jQuery.axZm.useMap = '.$this->ptj($zoom['config']['useMap']).'; ';
		$js .= 'jQuery.axZm.mapPath = '.$this->ptj($zoom['config']['mapPath']).'; ';  
		$js .= 'jQuery.axZm.mapOwnImage = '.$this->ptj($zoom['config']['mapOwnImage']).'; ';  
		
		if ($zoom['config']['useMap']){
			$js .= 'jQuery.axZm.mapFract = '.$this->ptj($zoom['config']['mapFract']).'; ';
			$js .= $this->arrayToJSObject($zoom['config']['mapBorder'],'jQuery.axZm.mapBorder', false, $rn, false).'; ';
			$js .= 'jQuery.axZm.zoomMapVis = '.$this->ptj($zoom['config']['zoomMapVis']).'; ';
			$js .= 'jQuery.axZm.dragMap = '.$this->ptj($zoom['config']['dragMap']).'; ';
			$js .= 'jQuery.axZm.mapHolderHeight = '.$this->ptj($zoom['config']['mapHolderHeight']).'; ';
			$js .= 'jQuery.axZm.mapHolderText = '.$this->ptj($zoom['config']['mapHolderText']).'; ';
			$js .= 'jQuery.axZm.zoomMapDragOpacity = '.$this->ptj($zoom['config']['zoomMapDragOpacity']).'; ';
			$js .= 'jQuery.axZm.zoomMapSelOpacity = '.$this->ptj($zoom['config']['zoomMapSelOpacity'] ).'; ';
			$js .= 'jQuery.axZm.zoomMapSelBorder = '.$this->ptj($zoom['config']['zoomMapSelBorder'] ).'; ';
			
			$js .= 'jQuery.axZm.zoomMapContainment = '.$this->ptj($zoom['config']['zoomMapContainment']).'; ';
			$js .= 'jQuery.axZm.mapButton = '.$this->ptj($zoom['config']['mapButton']).'; ';
			$js .= 'jQuery.axZm.mapPos = '.$this->ptj($zoom['config']['mapPos']).'; ';
			
			$js .= 'jQuery.axZm.zoomMapRest = '.$this->ptj($zoom['config']['zoomMapRest'] ).'; ';
			$js .= 'jQuery.axZm.zoomMapAnimate = '.$this->ptj($zoom['config']['zoomMapAnimate'] ).'; ';
			$js .= 'jQuery.axZm.zoomMapSwitchSpeed = '.$this->ptj($zoom['config']['zoomMapSwitchSpeed'] ).'; ';
			$js .= 'jQuery.axZm.mapSelSmoothDrag = '.$this->ptj($zoom['config']['mapSelSmoothDrag'] ).'; ';
			$js .= 'jQuery.axZm.mapSelSmoothDragSpeed = '.$this->ptj($zoom['config']['mapSelSmoothDragSpeed'] ).'; ';
			$js .= 'jQuery.axZm.mapSelSmoothDragMotion = '.$this->ptj($zoom['config']['mapSelSmoothDragMotion'] ).'; ';  
			$js .= 'jQuery.axZm.mapSelZoomSpeed = '.$this->ptj($zoom['config']['mapSelZoomSpeed'] ).'; ';		
			$js .= 'jQuery.axZm.mapParent = '.$this->ptj($zoom['config']['mapParent']).'; ';  
			$js .= 'jQuery.axZm.mapParCenter = '.$this->ptj($zoom['config']['mapParCenter']).'; ';  
			
			$js .= 'jQuery.axZm.mapWidth = '.$this->ptj($zoom['config']['mapWidth']).'; ';  
			$js .= 'jQuery.axZm.mapHeight = '.$this->ptj($zoom['config']['mapHeight']).'; ';   
			
			$js .= 'jQuery.axZm.mapMouseOver = '.$this->ptj($zoom['config']['mapMouseOver']).'; ';   
			$js .= 'jQuery.axZm.mapMouseWheel = '.$this->ptj($zoom['config']['mapMouseWheel']).'; ';  
			
			$js .= 'jQuery.axZm.mapHorzMargin = '.$this->ptj($zoom['config']['mapHorzMargin']).'; ';
			$js .= 'jQuery.axZm.mapVertMargin = '.$this->ptj($zoom['config']['mapVertMargin']).'; ';
			$js .= 'jQuery.axZm.mapOpacity = '.$this->ptj($zoom['config']['mapOpacity']).'; ';  
		}
		
		// Gallery
		$js .= 'jQuery.axZm.useGallery = '.$this->ptj($zoom['config']['useGallery']).'; ';
		$js .= 'jQuery.axZm.useFullGallery = '.$this->ptj($zoom['config']['useFullGallery']).'; ';
		$js .= 'jQuery.axZm.useHorGallery = '.$this->ptj($zoom['config']['useHorGallery']).'; ';
		$js .= 'jQuery.axZm.gallery = '.$this->ptj($zoom['config']['gallery']).'; ';
		$js .= 'jQuery.axZm.zoomGalDir = '.$this->ptj($zoom['config']['gallery']).'; '; // Alias
		
		$js .= 'jQuery.axZm.galleryLines = '.$this->ptj($zoom['config']['galleryLines']).'; ';
		$js .= 'jQuery.axZm.galleryNoThumbs = '.$this->ptj($zoom['config']['galleryNoThumbs']).'; ';
		
		
		// Gallery slideshow and navigation buttons
		$js .= 'jQuery.axZm.galleryNavi = '.$this->ptj($zoom['config']['galleryNavi']).'; ';
		$js .= 'jQuery.axZm.galleryNaviCirc = '.$this->ptj($zoom['config']['galleryNaviCirc']).'; ';
		$js .= 'jQuery.axZm.galleryPlayButton = '.$this->ptj($zoom['config']['galleryPlayButton']).'; ';
		$js .= 'jQuery.axZm.galleryButtonSpace = '.$this->ptj($zoom['config']['galleryButtonSpace']).'; ';
		$js .= 'jQuery.axZm.galleryNaviPos = '.$this->ptj($zoom['config']['galleryNaviPos']).'; ';
		$js .= 'jQuery.axZm.galleryNaviHeight = '.$this->ptj($zoom['config']['galleryNaviHeight']).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['galleryNaviMargin'],'jQuery.axZm.galleryNaviMargin', false, $rn, false).'; ';
		//$js .= $this->arrayToJSArray($zoom['config']['galleryNaviMargin'],'jQuery.axZm.galleryNaviMargin').'; ';
		
		$js .= 'jQuery.axZm.galleryPlayInterval = '.$this->ptj($zoom['config']['galleryPlayInterval']).'; ';
		$js .= 'jQuery.axZm.galleryAutoPlay = '.$this->ptj($zoom['config']['galleryAutoPlay']).'; ';		
		
		$js .= 'jQuery.axZm.gallerySlideNavi = '.$this->ptj($zoom['config']['gallerySlideNavi']).'; ';
		$js .= 'jQuery.axZm.gallerySlideNaviMouseOver = '.$this->ptj($zoom['config']['gallerySlideNaviMouseOver']).'; ';
		$js .= 'jQuery.axZm.gallerySlideNaviOnlyFullScreen = '.$this->ptj($zoom['config']['gallerySlideNaviOnlyFullScreen']).'; ';
		$js .= 'jQuery.axZm.gallerySlideNaviMargin = '.$this->ptj($zoom['config']['gallerySlideNaviMargin']).'; ';
		$js .= 'jQuery.axZm.gallerySlideNaviAnm = '.$this->ptj($zoom['config']['gallerySlideNaviAnm']).'; ';

		$js .= 'jQuery.axZm.galleryFadeOutSpeed = '.$this->ptj($zoom['config']['galleryFadeOutSpeed']).'; ';
		$js .= 'jQuery.axZm.galleryFadeInSpeed = '.$this->ptj($zoom['config']['galleryFadeInSpeed']).'; ';
		$js .= 'jQuery.axZm.galleryFadeInMotion = '.$this->ptj($zoom['config']['galleryFadeInMotion']).'; ';
		$js .= 'jQuery.axZm.galleryFadeInOpacity = '.$this->ptj($zoom['config']['galleryFadeInOpacity']).'; ';
		$js .= 'jQuery.axZm.galleryFadeInSize = '.$this->ptj($zoom['config']['galleryFadeInSize']).'; ';
		$js .= 'jQuery.axZm.galleryFadeInAnm = '.$this->ptj($zoom['config']['galleryFadeInAnm']).'; ';
		$js .= 'jQuery.axZm.gallerySwipe = '.$this->ptj($zoom['config']['gallerySwipe']).'; ';
		$js .= 'jQuery.axZm.galleryInnerFade = '.$this->ptj($zoom['config']['galleryInnerFade']).'; ';
		$js .= 'jQuery.axZm.galleryInnerFadeCut = '.$this->ptj($zoom['config']['galleryInnerFadeCut']).'; ';
		$js .= 'jQuery.axZm.galleryInnerFadeMotion = '.$this->ptj($zoom['config']['galleryInnerFadeMotion']).'; ';
		
		//if ($zoom['config']['useGallery'] || $zoom['config']['useFullGallery'] || $zoom['config']['useHorGallery']){

			$js .= 'jQuery.axZm.galleryThumbFadeOn = '.$this->ptj($zoom['config']['galleryThumbFadeOn']).'; ';
			
			$js .= 'jQuery.axZm.galleryThumbOverSpeed = '.$this->ptj($zoom['config']['galleryThumbOverSpeed']).'; ';
			$js .= 'jQuery.axZm.galleryThumbOverOpaque = '.$this->ptj($zoom['config']['galleryThumbOverOpaque']).'; ';
			
			//if ($zoom['config']['useGallery'] || $zoom['config']['useHorGallery']){
				$js .= 'jQuery.axZm.galleryThumbOutSpeed = '.$this->ptj($zoom['config']['galleryThumbOutSpeed']).'; ';
				$js .= 'jQuery.axZm.galleryThumbOutOpaque = '.$this->ptj($zoom['config']['galleryThumbOutOpaque']).'; ';
			//}
		//}
		
		// Horizontal Gallery
		//if ($zoom['config']['useHorGallery']){
			$js .= 'jQuery.axZm.galHorHeight = '.$this->ptj($zoom['config']['galHorHeight']).'; ';
			$js .= 'jQuery.axZm.galHorPosition = '.$this->ptj($zoom['config']['galHorPosition']).'; ';
			$js .= 'jQuery.axZm.galHorMarginTop = '.$this->ptj($zoom['config']['galHorMarginTop']).'; ';
			$js .= 'jQuery.axZm.galHorMarginBottom = '.$this->ptj($zoom['config']['galHorMarginBottom']).'; ';
			$js .= 'jQuery.axZm.galHorArrowWidth = '.$this->ptj($zoom['config']['galHorArrowWidth']).'; ';
			$js .= 'jQuery.axZm.galleryHorPicDim = '.$this->ptj($zoom['config']['galleryHorPicDim']).'; ';
			$js .= 'jQuery.axZm.galHorPicX = '.$this->ptj($zoom['config']['galHorPicX']).'; ';
			$js .= 'jQuery.axZm.galHorPicY = '.$this->ptj($zoom['config']['galHorPicY']).'; ';	
			$js .= 'jQuery.axZm.galHorCssPadding = '.$this->ptj($zoom['config']['galHorCssPadding']).'; ';	
			$js .= 'jQuery.axZm.galHorCssBorderWidth = '.$this->ptj($zoom['config']['galHorCssBorderWidth']).'; ';	
			$js .= 'jQuery.axZm.galHorCssDescrHeight = '.$this->ptj($zoom['config']['galHorCssDescrHeight']).'; ';	
			$js .= 'jQuery.axZm.galHorCssDescrPadding = '.$this->ptj($zoom['config']['galHorCssDescrPadding']).'; ';	
			$js .= 'jQuery.axZm.galHorCssMarginBetween = '.$this->ptj($zoom['config']['galHorCssMarginBetween']).'; ';	
			$js .= 'jQuery.axZm.galHorCssMarginTop = '.$this->ptj($zoom['config']['galHorCssMarginTop']).'; ';	
			$js .= 'jQuery.axZm.galHorScrollPos = '.$this->ptj($zoom['config']['galHorScrollPos']).'; ';	
			$js .= 'jQuery.axZm.galHorScrollToCurrent = '.$this->ptj($zoom['config']['galHorScrollToCurrent']).'; ';	
			$js .= 'jQuery.axZm.galHorScrollMotion = '.$this->ptj($zoom['config']['galHorScrollMotion']).'; ';	
			$js .= 'jQuery.axZm.galHorScrollSpeed = '.$this->ptj($zoom['config']['galHorScrollSpeed']).'; ';	
			$js .= 'jQuery.axZm.galHorScrollBy = '.$this->ptj($zoom['config']['galHorScrollBy']).'; ';	
			$js .= 'jQuery.axZm.galHorArrows = '.$this->ptj($zoom['config']['galHorArrows']).'; ';	
			$js .= 'jQuery.axZm.galHorFlow = '.$this->ptj($zoom['config']['galHorFlow']).'; ';
			$js .= 'jQuery.axZm.galHorMouse = '.$this->ptj($zoom['config']['galHorMouse']).'; ';
			$js .= 'jQuery.axZm.galHorInnerCorner = '.$this->ptj($zoom['config']['galHorInnerCorner']).'; ';
			$js .= 'jQuery.axZm.galHorInnerCornerImage = '.$this->ptj($zoom['config']['galHorInnerCornerImage']).'; ';
			$js .= 'jQuery.axZm.galHorInnerCornerWidth = '.$this->ptj($zoom['config']['galHorInnerCornerWidth']).'; ';
		//}

		// Vertical Gallery
		$js .= 'jQuery.axZm.galleryPicDim = '.$this->ptj($zoom['config']['galleryPicDim']).'; ';
		$js .= 'jQuery.axZm.galPicX = '.$this->ptj($zoom['config']['galPicX']).'; ';
		$js .= 'jQuery.axZm.galPicY = '.$this->ptj($zoom['config']['galPicY']).'; ';
		$js .= 'jQuery.axZm.galleryPos = '.$this->ptj($zoom['config']['galleryPos']).'; ';
		$js .= 'jQuery.axZm.galleryScrollbarWidth = '.$this->ptj($zoom['config']['galleryScrollbarWidth']).'; ';
		$js .= 'jQuery.axZm.galleryScrollTop = '.$this->ptj($zoom['config']['galleryScrollTop']).'; ';
		$js .= 'jQuery.axZm.galleryScrollToCurrent = '.$this->ptj($zoom['config']['galleryScrollToCurrent']).'; ';
		$js .= 'jQuery.axZm.galleryMarginLeft = '.$this->ptj($zoom['config']['galleryMarginLeft']).'; ';
		$js .= 'jQuery.axZm.galleryCssPadding = '.$this->ptj($zoom['config']['galleryCssPadding']).'; ';
		$js .= 'jQuery.axZm.galleryCssBorderWidth = '.$this->ptj($zoom['config']['galleryCssBorderWidth']).'; ';
		$js .= 'jQuery.axZm.galleryCssMargin = '.$this->ptj($zoom['config']['galleryCssMargin']).'; ';
		$js .= 'jQuery.axZm.galleryCssMargin = '.$this->ptj($zoom['config']['galleryCssMargin']).'; ';
		$js .= 'jQuery.axZm.galleryCssFirstTopMargin = '.$this->ptj($zoom['config']['galleryCssFirstTopMargin']).'; ';
		$js .= 'jQuery.axZm.galleryCssDescrPadding = '.$this->ptj($zoom['config']['galleryCssDescrPadding']).'; ';
		
		// Inline Gallery
		if ($zoom['config']['useFullGallery']){
			$js .= 'jQuery.axZm.galFullScrollCurrent = '.$this->ptj($zoom['config']['galFullScrollCurrent']).'; ';
			$js .= 'jQuery.axZm.galFullAutoStart = '.$this->ptj($zoom['config']['galFullAutoStart']).'; ';
			$js .= 'jQuery.axZm.galFullButton = '.$this->ptj($zoom['config']['galFullButton']).'; ';
			
			$js .= 'jQuery.axZm.galFullTooltip = '.$this->ptj($zoom['config']['galFullTooltip']).'; ';
			if ($zoom['config']['galFullTooltip']){
				$js .= 'jQuery.axZm.galFullTooltipOffset = '.$this->ptj($zoom['config']['galFullTooltipOffset']).'; ';
				$js .= 'jQuery.axZm.galFullTooltipSpeed = '.$this->ptj($zoom['config']['galFullTooltipSpeed']).'; ';
				$js .= 'jQuery.axZm.galFullTooltipTransp = '.$this->ptj($zoom['config']['galFullTooltipTransp']).'; ';
			}
			
			// Inline Gallery Thumbs
			$js .= 'jQuery.axZm.galleryFullPicDim = '.$this->ptj($zoom['config']['galleryFullPicDim']).'; ';
			$js .= 'jQuery.axZm.galFullPicX = '.$this->ptj($zoom['config']['galFullPicX']).'; ';
			$js .= 'jQuery.axZm.galFullPicY = '.$this->ptj($zoom['config']['galFullPicY']).'; ';
			$js .= 'jQuery.axZm.galFullCssPadding = '.$this->ptj($zoom['config']['galFullCssPadding']).'; ';
			$js .= 'jQuery.axZm.galFullCssBorderWidth = '.$this->ptj($zoom['config']['galFullCssBorderWidth']).'; ';
			$js .= 'jQuery.axZm.galFullCssMargin = '.$this->ptj($zoom['config']['galFullCssMargin']).'; ';
			$js .= 'jQuery.axZm.galFullCssDescrHeight = '.$this->ptj($zoom['config']['galFullCssDescrHeight']).'; ';
			$js .= 'jQuery.axZm.galFullCssDescrPadding = '.$this->ptj($zoom['config']['galFullCssDescrPadding']).'; ';
		}
		
		$arrayMods = array('crop', 'spin', 'pan');
		if (!in_array($zoom['config']['firstMod'], $arrayMods)){$zoom['config']['firstMod'] = 'pan';}
		if (!$zoom['config']['spinMod'] && $zoom['config']['firstMod'] == 'spin'){$zoom['config']['firstMod'] = 'pan';}
		$js .= 'jQuery.axZm.firstMod = '.$this->ptj($zoom['config']['firstMod']).'; ';		
		$js .= $this->arrayToJSObject($zoom['config']['keyPressMod'],'jQuery.axZm.keyPressMod', false, $rn, false).'; ';
		
		$js .= 'jQuery.axZm.spinMod = '.$this->ptj($zoom['config']['spinMod']).'; ';

		
		if ($zoom['config']['spinMod']){
			$js .= $this->arrayToJSObject($zoom['config']['spinPreloaderSettings'],'jQuery.axZm.spinPreloaderSettings', false, $rn, false).'; ';
			$js .= 'jQuery.axZm.spinSensitivity = '.$this->ptj($zoom['config']['spinSensitivity']).'; ';
			$js .= 'jQuery.axZm.spinReverse = '.$this->ptj($zoom['config']['spinReverse']).'; ';
			$js .= 'jQuery.axZm.spinDemo = '.$this->ptj($zoom['config']['spinDemo']).'; ';
			$js .= 'jQuery.axZm.spinDemoTime = '.$this->ptj($zoom['config']['spinDemoTime']).'; ';
			$js .= 'jQuery.axZm.spinDemoRounds = '.$this->ptj($zoom['config']['spinDemoRounds']).'; ';
			$js .= 'jQuery.axZm.spinOnSwitch = '.$this->ptj($zoom['config']['spinOnSwitch']).'; ';
			$js .= 'jQuery.axZm.spinWhilePreload = '.$this->ptj($zoom['config']['spinWhilePreload']).'; ';
			
			$js .= 'jQuery.axZm.spinMouseOverStop = '.$this->ptj($zoom['config']['spinMouseOverStop']).'; ';
			$js .= $this->arrayToJSObject($zoom['config']['spinEffect'],'jQuery.axZm.spinEffect', false, $rn, false).'; ';
			$js .= 'jQuery.axZm.spinSlider = '.$this->ptj($zoom['config']['spinSlider']).'; ';
			$js .= 'jQuery.axZm.spinSliderContainerHeight = '.$this->ptj($zoom['config']['spinSliderContainerHeight']).'; ';
			$js .= 'jQuery.axZm.spinSliderReverse = '.$this->ptj($zoom['config']['spinSliderReverse']).'; ';
			$js .= 'jQuery.axZm.spinBounce = '.$this->ptj($zoom['config']['spinBounce']).'; ';
			$js .= $this->arrayToJSObject($zoom['config']['spinKeys'],'jQuery.axZm.spinKeys', false, $rn, false).'; ';
			
			if ($zoom['config']['cueFrames']){
				$js .= 'jQuery.axZm.cueFrames = '.$this->ptj($zoom['config']['cueFrames']).'; ';
			}			
			$js .= 'jQuery.axZm.spinSliderPlayButton = '.$this->ptj($zoom['config']['spinSliderPlayButton']).'; ';
			$js .= 'jQuery.axZm.spinSliderFSoffset = '.$this->ptj($zoom['config']['spinSliderFSoffset']).'; ';
			$js .= 'jQuery.axZm.spinAreaDisable = '.$this->ptj($zoom['config']['spinAreaDisable']).'; ';
			$js .= 'jQuery.axZm.spinToMotion = '.$this->ptj($zoom['config']['spinToMotion']).'; ';
			$js .= 'jQuery.axZm.spinOnClick = '.$this->ptj($zoom['config']['spinOnClick']).'; ';  
			
			$js .= 'jQuery.axZm.spinFlip = '.$this->ptj($zoom['config']['spinFlip']).'; ';  
			
			if (isset($zoom['config']['zAxis'])){
				$js .= $this->arrayToJSObject($zoom['config']['zAxis'],'jQuery.axZm.zAxis', false, $rn, false).'; ';
				$js .= $this->arrayToJSObject($zoom['config']['zFolder'],'jQuery.axZm.zFolder', false, $rn, true).'; ';
				$js .= 'jQuery.axZm.spinReverseZ = '.$this->ptj($zoom['config']['spinReverseZ']).'; ';
				$js .= 'jQuery.axZm.spinSensitivityZ = '.$this->ptj($zoom['config']['spinSensitivityZ']).'; ';
			}
			
			if (isset($_GET['firstAxis'])){
				$js .= 'jQuery.axZm.firstAxis = '.$this->ptj($_GET['firstAxis']).'; ';  
			} elseif(isset($zoom['config']['firstAxis'])){
				$js .= 'jQuery.axZm.firstAxis = '.$this->ptj($zoom['config']['firstAxis']).'; '; 
			} else {
				$js .= 'jQuery.axZm.firstAxis = false; ';
			}
		}else{
			$js .= 'jQuery.axZm.zAxis = false; ';
			$js .= 'jQuery.axZm.zFolder = false; ';
			$js .= 'jQuery.axZm.firstAxis = false; ';
		}
		
		$js .= 'jQuery.axZm.zoomSlider = '.$this->ptj($zoom['config']['zoomSlider']).'; ';
		if ($zoom['config']['zoomSlider']){
			$js .= 'jQuery.axZm.zoomSliderHeight = '.$this->ptj($zoom['config']['zoomSliderHeight']).'; ';
			$js .= 'jQuery.axZm.zoomSliderWidth = '.$this->ptj($zoom['config']['zoomSliderWidth']).'; ';
			$js .= 'jQuery.axZm.zoomSliderHandleSize = '.$this->ptj($zoom['config']['zoomSliderHandleSize']).'; ';
			$js .= 'jQuery.axZm.zoomSliderPosition = '.$this->ptj($zoom['config']['zoomSliderPosition']).'; ';
			$js .= 'jQuery.axZm.zoomSliderMarginV = '.$this->ptj($zoom['config']['zoomSliderMarginV']).'; ';
			$js .= 'jQuery.axZm.zoomSliderMarginH = '.$this->ptj($zoom['config']['zoomSliderMarginH']).'; ';
			$js .= 'jQuery.axZm.zoomSliderOpacity = '.$this->ptj($zoom['config']['zoomSliderOpacity']).'; ';
			$js .= 'jQuery.axZm.zoomSliderHorizontal = '.$this->ptj($zoom['config']['zoomSliderHorizontal']).'; ';
			$js .= 'jQuery.axZm.zoomSliderMouseOver = '.$this->ptj($zoom['config']['zoomSliderMouseOver']).'; ';
			$js .= 'jQuery.axZm.zoomSliderContainerPadding = '.$this->ptj($zoom['config']['zoomSliderContainerPadding']).'; ';
		}
		
		// Motion
		$js .= 'jQuery.axZm.pMove= '.$this->ptj($zoom['config']['pMove']).'; ';
		$js .= 'jQuery.axZm.pZoom = '.$this->ptj($zoom['config']['pZoom']).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['autoZoom'],'jQuery.axZm.autoZoom', false, $rn, false).'; ';  
		$js .= 'jQuery.axZm.pZoomOut = '.$this->ptj($zoom['config']['pZoomOut']).'; ';
		$js .= 'jQuery.axZm.stepZoom = '.$this->ptj($zoom['config']['stepZoom']).'; ';  
		
		$js .= 'jQuery.axZm.zoomSpeedGlobal = '.$this->ptj($zoom['config']['zoomSpeedGlobal']).'; ';
		$js .= 'jQuery.axZm.moveSpeed = '.$this->ptj($zoom['config']['moveSpeed']).'; ';
		$js .= 'jQuery.axZm.zoomSpeed = '.$this->ptj($zoom['config']['zoomSpeed']).'; ';
		$js .= 'jQuery.axZm.zoomOutSpeed = '.$this->ptj($zoom['config']['zoomOutSpeed']).'; ';
		$js .= 'jQuery.axZm.cropSpeed = '.$this->ptj($zoom['config']['cropSpeed']).'; ';
		$js .= 'jQuery.axZm.restoreSpeed = '.$this->ptj($zoom['config']['restoreSpeed']).'; ';
		$js .= 'jQuery.axZm.traverseSpeed = '.$this->ptj($zoom['config']['traverseSpeed']).'; ';
		$js .= 'jQuery.axZm.zoomFade = '.$this->ptj($zoom['config']['zoomFade']).'; ';
		$js .= 'jQuery.axZm.zoomFadeIn = '.$this->ptj($zoom['config']['zoomFadeIn']).'; ';
		$js .= 'jQuery.axZm.buttonAjax = '.$this->ptj($zoom['config']['buttonAjax']).'; ';
		
		$js .= 'jQuery.axZm.zoomEaseGlobal = '.$this->ptj($zoom['config']['zoomEaseGlobal']).'; ';
		$js .= 'jQuery.axZm.zoomEaseIn = '.$this->ptj($zoom['config']['zoomEaseIn']).'; ';
		$js .= 'jQuery.axZm.zoomEaseCrop = '.$this->ptj($zoom['config']['zoomEaseCrop']).'; ';
		$js .= 'jQuery.axZm.zoomEaseOut = '.$this->ptj($zoom['config']['zoomEaseOut']).'; ';
		$js .= 'jQuery.axZm.zoomEaseMove = '.$this->ptj($zoom['config']['zoomEaseMove']).'; ';
		$js .= 'jQuery.axZm.zoomEaseRestore = '.$this->ptj($zoom['config']['zoomEaseRestore']).'; ';
		$js .= 'jQuery.axZm.zoomEaseTraverse = '.$this->ptj($zoom['config']['zoomEaseTraverse']).'; ';
		
		$js .= 'jQuery.axZm.fps1 = '.$this->ptj($zoom['config']['fps1']).'; ';  
		$js .= 'jQuery.axZm.fps2 = '.$this->ptj($zoom['config']['fps2']).'; ';  
		
		$js .= 'jQuery.axZm.zoomLoaderEnable = '.$this->ptj($zoom['config']['zoomLoaderEnable']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderClass = '.$this->ptj($zoom['config']['zoomLoaderClass']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderTransp = '.$this->ptj($zoom['config']['zoomLoaderTransp']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderFadeIn = '.$this->ptj($zoom['config']['zoomLoaderFadeIn']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderFadeOut = '.$this->ptj($zoom['config']['zoomLoaderFadeOut']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderPos = '.$this->ptj($zoom['config']['zoomLoaderPos']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderMargin = '.$this->ptj($zoom['config']['zoomLoaderMargin']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderFrames = '.$this->ptj($zoom['config']['zoomLoaderFrames']).'; ';
		$js .= 'jQuery.axZm.zoomLoaderCycle = '.$this->ptj($zoom['config']['zoomLoaderCycle']).'; ';
		
		
		// Navi
		$js .= 'jQuery.axZm.displayNavi = '.$this->ptj($zoom['config']['displayNavi']).'; ';
		$js .= 'jQuery.axZm.naviZoomBut = '.$this->ptj($zoom['config']['naviZoomBut']).'; ';
		$js .= 'jQuery.axZm.naviPanBut = '.$this->ptj($zoom['config']['naviPanBut']).'; ';
		$js .= 'jQuery.axZm.naviRestoreBut = '.$this->ptj($zoom['config']['naviRestoreBut']).'; ';
		$js .= 'jQuery.axZm.naviHotspotsBut = '.$this->ptj($zoom['config']['naviHotspotsBut']).'; ';
		$js .= 'jQuery.axZm.downloadButton = '.$this->ptj($zoom['config']['downloadButton']).'; ';
		
		$js .= 'jQuery.axZm.naviCropButSwitch = '.$this->ptj($zoom['config']['naviCropButSwitch']).'; ';
		$js .= 'jQuery.axZm.naviPanButSwitch = '.$this->ptj($zoom['config']['naviPanButSwitch']).'; ';
		$js .= 'jQuery.axZm.naviSpinButSwitch = '.$this->ptj($zoom['config']['naviSpinButSwitch']).'; ';

		$js .= 'jQuery.axZm.deaktTransp = '.$this->ptj($zoom['config']['deaktTransp']).'; ';
		$js .= 'jQuery.axZm.disabledTransp = '.$this->ptj($zoom['config']['disabledTransp']).'; ';
		$js .= 'jQuery.axZm.naviPos = '.$this->ptj($zoom['config']['naviPos']).'; ';
		$js .= 'jQuery.axZm.naviFloat = '.$this->ptj($zoom['config']['naviFloat']).'; ';
		$js .= 'jQuery.axZm.naviHeight = '.$this->ptj($zoom['config']['naviHeight']).'; ';
		$js .= 'jQuery.axZm.naviSpace = '.$this->ptj($zoom['config']['naviSpace']).'; ';
		$js .= 'jQuery.axZm.naviGroupSpace = '.$this->ptj($zoom['config']['naviGroupSpace']).'; ';
		$js .= 'jQuery.axZm.naviMinPadding = '.$this->ptj($zoom['config']['naviMinPadding']).'; ';
		$js .= 'jQuery.axZm.naviTopMargin = '.$this->ptj($zoom['config']['naviTopMargin']).'; ';
		$js .= 'jQuery.axZm.naviBigZoom = '.$this->ptj($zoom['config']['naviBigZoom']).'; ';

		$js .= 'jQuery.axZm.naviDownState = '.$this->ptj($zoom['config']['naviDownState']).'; ';
		$js .= 'jQuery.axZm.naviOverState = '.$this->ptj($zoom['config']['naviOverState']).'; ';
		
		$js .= 'jQuery.axZm.pssBar = '.$this->ptj($zoom['config']['pssBar']).'; ';
		
		if ($zoom['config']['pssBar']){
			$js .= 'jQuery.axZm.pssBarH = '.$this->ptj($zoom['config']['pssBarH']).'; ';
			$js .= 'jQuery.axZm.pssBarM = '.$this->ptj($zoom['config']['pssBarM']).'; ';
			$js .= 'jQuery.axZm.pssBarMS = '.$this->ptj($zoom['config']['pssBarMS']).'; ';
			$js .= 'jQuery.axZm.pssBarO = '.$this->ptj($zoom['config']['pssBarO']).'; ';
			$js .= 'jQuery.axZm.pssBarP = '.$this->ptj($zoom['config']['pssBarP']).'; ';
		}
		
		// Scroll (mousewheel)
		$js .= 'jQuery.axZm.scroll = '.$this->ptj($zoom['config']['scroll']).'; ';
		$js .= 'jQuery.axZm.mouseScrollEnable = '.$this->ptj($zoom['config']['mouseScrollEnable']).'; ';
		 
		$js .= $this->arrayToJSObject($zoom['config']['scrollBrowserExcl'],'jQuery.axZm.scrollBrowserExcl', false, $rn, false).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['scrollBrowserExclPar'],'jQuery.axZm.scrollBrowserExclPar', false, $rn, false).'; ';
		
		$js .= 'jQuery.axZm.scrollAnm = '.$this->ptj($zoom['config']['scrollAnm']).'; ';
		$js .= 'jQuery.axZm.scrollSpeed = '.$this->ptj($zoom['config']['scrollSpeed']).'; ';
		$js .= 'jQuery.axZm.scrollZoom = '.$this->ptj($zoom['config']['scrollZoom']).'; ';
		$js .= 'jQuery.axZm.scrollMotion = '.$this->ptj($zoom['config']['scrollMotion']).'; ';
		$js .= 'jQuery.axZm.scrollPanR = '.$this->ptj($zoom['config']['scrollPanR']).'; ';
		$js .= 'jQuery.axZm.scrollAjax = '.$this->ptj($zoom['config']['scrollAjax']).'; ';
		$js .= 'jQuery.axZm.scrollPause = '.$this->ptj($zoom['config']['scrollPause']).'; ';
		$js .= 'jQuery.axZm.scrollOutReversed = '.$this->ptj($zoom['config']['scrollOutReversed']).'; ';  
		$js .= 'jQuery.axZm.scrollOutCenter = '.$this->ptj($zoom['config']['scrollOutCenter']).'; ';  
		

		// Selector
		$js .= 'jQuery.axZm.zoomSelectionColor = '.$this->ptj($zoom['config']['zoomSelectionColor']).'; ';
		$js .= 'jQuery.axZm.zoomSelectionOpacity = '.$this->ptj($zoom['config']['zoomSelectionOpacity']).'; ';
		$js .= 'jQuery.axZm.zoomOuterColor = '.$this->ptj($zoom['config']['zoomOuterColor']).'; ';
		$js .= 'jQuery.axZm.zoomOuterOpacity = '.$this->ptj($zoom['config']['zoomOuterOpacity']).'; ';
		$js .= 'jQuery.axZm.zoomBorderColor = '.$this->ptj($zoom['config']['zoomBorderColor']).'; ';
		$js .= 'jQuery.axZm.zoomBorderWidth = '.$this->ptj($zoom['config']['zoomBorderWidth']).'; ';
		$js .= 'jQuery.axZm.zoomSelectionAnm = '.$this->ptj($zoom['config']['zoomSelectionAnm']).'; ';
		$js .= 'jQuery.axZm.zoomSelectionCross = '.$this->ptj($zoom['config']['zoomSelectionCross']).'; ';
		$js .= 'jQuery.axZm.zoomSelectionCrossOp = '.$this->ptj($zoom['config']['zoomSelectionCrossOp']).'; ';
		$js .= 'jQuery.axZm.zoomSelectionMod = '.$this->ptj($zoom['config']['zoomSelectionMod']).'; ';  
		$js .= 'jQuery.axZm.zoomSelectionProp = '.$this->ptj($zoom['config']['zoomSelectionProp']).'; ';  
		
		// Description area
		$js .= 'jQuery.axZm.zoomShowButtonDescr = '.$this->ptj($zoom['config']['zoomShowButtonDescr']).'; ';
		
		//if ($zoom['config']['zoomShowButtonDescr']){
			$js .= $this->arrayToJSObject($zoom['config']['mapButTitle'],'jQuery.axZm.mapButTitle', false, $rn, false).'; ';  
		//}
		
		$js .= 'jQuery.axZm.descrAreaTransp = '.$this->ptj($zoom['config']['descrAreaTransp']).'; ';
		$js .= 'jQuery.axZm.descrAreaHideTimeOut = '.$this->ptj($zoom['config']['descrAreaHideTimeOut']).'; ';
		$js .= 'jQuery.axZm.descrAreaShowTimeOut = '.$this->ptj($zoom['config']['descrAreaShowTimeOut']).'; ';
		$js .= 'jQuery.axZm.descrAreaHideTime = '.$this->ptj($zoom['config']['descrAreaHideTime']).'; ';
		$js .= 'jQuery.axZm.descrAreaShowTime = '.$this->ptj($zoom['config']['descrAreaShowTime']).'; ';
		$js .= 'jQuery.axZm.descrAreaHeight = '.$this->ptj($zoom['config']['descrAreaHeight']).'; ';
		$js .= 'jQuery.axZm.descrAreaMotion = '.$this->ptj($zoom['config']['descrAreaMotion']).'; ';

		// Dragging
		$js .= 'jQuery.axZm.zoomDragPhysics = '.$this->ptj($zoom['config']['zoomDragPhysics']).'; ';
		$js .= 'jQuery.axZm.zoomDragAnm = '.$this->ptj($zoom['config']['zoomDragAnm']).'; ';  
		$js .= 'jQuery.axZm.zoomDragSpeed = '.$this->ptj($zoom['config']['zoomDragSpeed']).'; ';
		$js .= 'jQuery.axZm.zoomDragAjax = '.$this->ptj($zoom['config']['zoomDragAjax']).'; ';
		$js .= 'jQuery.axZm.zoomDragMotion = '.$this->ptj($zoom['config']['zoomDragMotion']).'; ';
		
		// jscrollPane
		$js .= 'jQuery.axZm.scrollPane = '.$this->ptj($zoom['config']['scrollPane']).'; ';
		$js .= 'jQuery.axZm.scrollPaneTheme = '.$this->ptj($zoom['config']['scrollPaneTheme']).'; ';  
		$js .= $this->arrayToJSObject($zoom['config']['scrollPaneOptionsVert'],'jQuery.axZm.scrollPaneOptionsVert', false, $rn, false).'; ';  
		$js .= $this->arrayToJSObject($zoom['config']['scrollPaneOptionsFull'],'jQuery.axZm.scrollPaneOptionsFull', false, $rn, false).'; '; 
		
		
		// Helper
		$js .= 'jQuery.axZm.pyrDialog = '.$this->ptj($zoom['config']['pyrDialog']).'; ';
		$js .= 'jQuery.axZm.gPyramidDialog = '.$this->ptj($zoom['config']['gPyramidDialog']).'; ';
		$js .= 'jQuery.axZm.zoomStat = '.$this->ptj($zoom['config']['zoomStat']).'; '; // depreciated
		$js .= 'jQuery.axZm.zoomStatHeight = '.$this->ptj($zoom['config']['zoomStatHeight']).'; '; // depreciated
		$js .= 'jQuery.axZm.useSess = '.$this->ptj($zoom['config']['useSess']).'; ';
		$js .= 'jQuery.axZm.cursorWait = '.$this->ptj($zoom['config']['cursorWait']).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['cursor'],'jQuery.axZm.cursor', false, $rn, false).'; ';
		
		$js .= 'jQuery.axZm.fullZoomBorder = '.$this->ptj($zoom['config']['fullZoomBorder']).'; ';
		$js .= 'jQuery.axZm.fullZoomOutBorder = '.$this->ptj($zoom['config']['fullZoomOutBorder']).'; ';
		
		$js .= 'jQuery.axZm.zoomTimeOut = '.$this->ptj($zoom['config']['zoomTimeOut']).'; ';
		$js .= 'jQuery.axZm.quirks = '.$this->ptj($zoom['config']['quirks']).'; ';
		$js .= 'jQuery.axZm.zoomWarningBrowser = '.$this->ptj($zoom['config']['zoomWarningBrowser']).'; ';
		$js .= 'jQuery.axZm.msInterp = '.$this->ptj($zoom['config']['msInterp']).'; ';
		$js .= 'jQuery.axZm.help = '.$this->ptj($zoom['config']['help']).'; ';
		$js .= 'jQuery.axZm.helpTransp = '.$this->ptj($zoom['config']['helpTransp']).'; ';
		$js .= 'jQuery.axZm.helpTime = '.$this->ptj($zoom['config']['helpTime']).'; ';
		$js .= 'jQuery.axZm.zoomLoadFile = '.$this->ptj($zoom['config']['zoomLoadFile']).'; ';
		$js .= 'jQuery.axZm.zoomLoadSess = '.$this->ptj($zoom['config']['zoomLoadSess']).'; ';
		$js .= 'jQuery.axZm.layVertCenter = '.$this->ptj($zoom['config']['layVertCenter']).'; ';
		$js .= 'jQuery.axZm.innerMargin = '.$this->ptj($zoom['config']['innerMargin']).'; '; 
		$js .= 'jQuery.axZm.cornerRadius = '.$this->ptj($zoom['config']['cornerRadius']).'; '; 
		$js .= 'jQuery.axZm.vWtrmrk = '.$this->ptj($zoom['config']['vWtrmrk']).'; ';  
		
		if (is_array($zoom['config']['notTouchUA']) && !empty($zoom['config']['notTouchUA'])){
			$js .= $this->arrayToJSObject($zoom['config']['notTouchUA'],'jQuery.axZm.notTouchUA', false, $rn, false).'; ';
		}
		
		// Layers
		$js .= 'jQuery.axZm.backLayer = '.$this->ptj($zoom['config']['backLayer']).'; ';
		$js .= 'jQuery.axZm.backDiv = '.$this->ptj($zoom['config']['backDiv']).'; ';
		$js .= 'jQuery.axZm.backInnerDiv = '.$this->ptj($zoom['config']['backInnerDiv']).'; ';
		$js .= 'jQuery.axZm.picLayer = '.$this->ptj($zoom['config']['picLayer']).'; ';
		$js .= 'jQuery.axZm.overLayer = '.$this->ptj($zoom['config']['overLayer']).'; ';
		
		// Stat info
		$js .= 'jQuery.axZm.zoomLogInfo = '.$this->ptj($zoom['config']['zoomLogInfo']).'; ';
		$js .= 'jQuery.axZm.zoomLogJustLevel = '.$this->ptj($zoom['config']['zoomLogJustLevel']).'; ';
		$js .= 'jQuery.axZm.zoomLogLevel  = '.$this->ptj($zoom['config']['zoomLogLevel']).'; ';
		$js .= 'jQuery.axZm.zoomLogTime = '.$this->ptj($zoom['config']['zoomLogTime']).'; '; 
		$js .= 'jQuery.axZm.zoomLogTraffic = '.$this->ptj($zoom['config']['zoomLogTraffic']).'; ';
		$js .= 'jQuery.axZm.zoomLogSeconds = '.$this->ptj($zoom['config']['zoomLogSeconds']).'; ';
		$js .= 'jQuery.axZm.zoomLogLoading = '.$this->ptj($zoom['config']['zoomLogLoading']).'; ';

		// Help Text
		$js .= 'jQuery.axZm.helpTxt = '.$this->ptj($zoom['config']['helpTxt']).'; ';
		
		$js .= 'jQuery.axZm.tapHideAll = '.$this->ptj($zoom['config']['tapHideAll']).'; ';
		
		// Icon files
		if ($zoom['config']['naviBigZoom']){ 
			$zoom['config']['icons']['zoomIn'] = $zoom['config']['icons']['zoomInBig'];
			$zoom['config']['icons']['zoomOut'] = $zoom['config']['icons']['zoomOutBig'];
		}
		$js .= 'jQuery.axZm.buttonSet = '.$this->ptj($zoom['config']['buttonSet']).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['icons'],'jQuery.axZm.icons', false, $rn, false).'; ';
		$js .= $this->arrayToJSObject($zoom['config']['mNavi'],'jQuery.axZm.mNavi', false, $rn, false).'; ';

		if ($zoom['config']['fullScreenEnable']){
			$js .= 'jQuery.axZm.fullScreenEnable = '.$this->ptj($zoom['config']['fullScreenEnable']).'; ';
			$js .= 'jQuery.axZm.fullScreenNaviBar = '.$this->ptj($zoom['config']['fullScreenNaviBar']).'; ';
			
			$js .= 'jQuery.axZm.fullScreenCornerButton = '.$this->ptj($zoom['config']['fullScreenCornerButton']).'; ';
			$js .= 'jQuery.axZm.fullScreenCornerButtonPos = '.$this->ptj($zoom['config']['fullScreenCornerButtonPos']).'; ';
			$js .= 'jQuery.axZm.fullScreenCornerButtonMarginX = '.$this->ptj($zoom['config']['fullScreenCornerButtonMarginX']).'; ';
			$js .= 'jQuery.axZm.fullScreenCornerButtonMarginY = '.$this->ptj($zoom['config']['fullScreenCornerButtonMarginY']).'; ';
			$js .= 'jQuery.axZm.fullScreenCornerButtonMouseOver = '.$this->ptj($zoom['config']['fullScreenCornerButtonMouseOver']).'; ';

			$js .= 'jQuery.axZm.fullScreenNaviButton = '.$this->ptj($zoom['config']['fullScreenNaviButton']).'; ';
			$js .= 'jQuery.axZm.fullScreenExitText = '.$this->ptj($zoom['config']['fullScreenExitText']).'; ';
			$js .= 'jQuery.axZm.fullScreenExitTimeout = '.$this->ptj($zoom['config']['fullScreenExitTimeout']).'; ';

			$js .= 'jQuery.axZm.fullScreenVertGallery = '.$this->ptj($zoom['config']['fullScreenVertGallery']).'; ';
			$js .= 'jQuery.axZm.fullScreenHorzGallery = '.$this->ptj($zoom['config']['fullScreenHorzGallery']).'; ';
			$js .= 'jQuery.axZm.fullScreenRel = '.$this->ptj($zoom['config']['fullScreenRel']).'; ';
			
			$js .= 'jQuery.axZm.fullScreenMapFract = '.$this->ptj($zoom['config']['fullScreenMapFract']).'; ';
			$js .= 'jQuery.axZm.fullScreenMapWidth = '.$this->ptj($zoom['config']['fullScreenMapWidth']).'; ';
			$js .= 'jQuery.axZm.fullScreenMapHeight = '.$this->ptj($zoom['config']['fullScreenMapHeight']).'; ';
			$js .= $this->arrayToJSObject($zoom['config']['fullScreenKeepZoom'],'jQuery.axZm.fullScreenKeepZoom', false, $rn, false).'; ';  
			$js .= 'jQuery.axZm.fullScreenApi = '.$this->ptj($zoom['config']['fullScreenApi']).'; ';
		}

		// Gallery array
		if ($zoom['config']['galArray']){
			$js .= $this->arrayToJSObject($zoom['config']['galArray'],'jQuery.axZm.zoomGA', false, $rn, false).'; ';
		}
		
		// Pack Javascript
		if ($pack){
			$myPacker = new JavaScriptPacker($js, 'Normal', true, false);
			$js = $myPacker->pack();		
		}

		elseif ($rn){
			$js = str_replace('; ',";".$rnStr,$js);
		}		
		

		$js = "\n<script type=\"text/javascript\">$rnStr$js$rnStr</script>$rnStr";

		return $js;
	}


	/**
	  * Outputs all needed variables in javascript by loading a different gallery set over ajax
	  * @access public
	  * @param array $zoom
	  * @param bool $rn Linebreak after each line of code
	  * @param bool $pack Use packer for output
	  * @return HTML-Output
	  **/		
	public function drawZoomJsGallerySet($zoom, $rn = false, $pack = true){
		$rnStr = '';
		if ($rn){$rnStr = "\n";}
		
		$js = '';
		
		$js .= 'jQuery.axZm.zoomID = '.$this->ptj($_GET['zoomID']).'; ';
		$js .= 'jQuery.axZm.randNum = '.$this->ptj($this->rndNum(24)).'; ';
		
		$js .= 'jQuery.axZm.icon = '.$this->ptj($zoom['config']['icon']).'; ';
		$js .= 'jQuery.axZm.iconDir = '.$this->ptj($zoom['config']['icon']).'; '; // Alias
		
		$js .= 'jQuery.axZm.thumbs = '.$this->ptj($zoom['config']['thumbs']).'; ';
		$js .= 'jQuery.axZm.smallImgPath = '.$this->ptj($zoom['config']['thumbs']).'; '; // Alias
		
		$js .= 'jQuery.axZm.smallImg = '.$this->ptj($zoom['config']['thumbs'].$zoom['config']['smallImgName']).'; ';
	
		$js .= 'jQuery.axZm.iw = '.$this->ptj($zoom['config']['smallImgSize'][0]).'; ';
		$js .= 'jQuery.axZm.ih = '.$this->ptj($zoom['config']['smallImgSize'][1]).'; ';
		$js .= 'jQuery.axZm.ow = '.$this->ptj($zoom['config']['orgImgSize'][0]).'; ';
		$js .= 'jQuery.axZm.oh = '.$this->ptj($zoom['config']['orgImgSize'][1]).'; ';

		$js .= 'jQuery.axZm.smallFileSize = '.$this->ptj($zoom['config']['smallFileSize']).'; ';
		$js .= 'jQuery.axZm.parToPass = '.$this->ptj($zoom['config']['parToPass']).'; ';
		
		// Ver. 2.1.5+
		//$js .= 'jQuery.axZm.fileType = '.$this->ptj($this->getl('.', $zoom['config']['orgImgName'])).'; ';
		//$js .= 'jQuery.axZm.fileBase = '.$this->ptj($this->getf('.', $zoom['config']['orgImgName'])).'; ';

		if ($zoom['config']['cropNoObj']){
			$js .= 'jQuery.axZm.pic = '.$this->ptj($zoom['config']['pic']).'; ';
			$js .= 'jQuery.axZm.orgPath = '.$this->ptj($zoom['config']['pic']).'; '; // Alias
		}
			
		// Gallery array
		if ($zoom['config']['galArray']){
			$js .= $this->arrayToJSObject($zoom['config']['galArray'],'jQuery.axZm.zoomGA', false, $rn, false).'; ';
		}	

		if ($zoom['config']['spinMod']){			
			if (isset($zoom['config']['zAxis'])){
				$js .= $this->arrayToJSObject($zoom['config']['zAxis'],'jQuery.axZm.zAxis', false, $rn, false).'; ';
				$js .= $this->arrayToJSObject($zoom['config']['zFolder'],'jQuery.axZm.zFolder', false, $rn, true).'; ';
				$js .= 'jQuery.axZm.spinReverseZ = '.$this->ptj($zoom['config']['spinReverseZ']).'; ';
				$js .= 'jQuery.axZm.spinSensitivityZ = '.$this->ptj($zoom['config']['spinSensitivityZ']).'; ';
				
				if (isset($_GET['firstAxis'])){
					$js .= 'jQuery.axZm.firstAxis = '.$this->ptj($_GET['firstAxis']).'; ';
				} elseif(isset($zoom['config']['firstAxis'])){
					$js .= 'jQuery.axZm.firstAxis = '.$this->ptj($zoom['config']['firstAxis']).'; '; 
				} else {
					$js .= 'jQuery.axZm.firstAxis = false; ';
				}
				
			}else{
				$js .= 'jQuery.axZm.zAxis = false; ';
				$js .= 'jQuery.axZm.zFolder = false; ';
				$js .= 'jQuery.axZm.firstAxis = false; ';
			}
		}

		// Dialog if first image has been made...
		if (isset($this->returnMakeFirstImage)){
			if (!is_bool($this->returnMakeFirstImage)){
				$js .= $this->removeScriptTags($this->returnMakeFirstImage);
			}
		}
		
		// Dialog if zoom tiles have been made...
		if (isset($this->returnMakeZoomTiles)){
			if (!is_bool($this->returnMakeZoomTiles)){
				$js .= $this->removeScriptTags($this->returnMakeZoomTiles);
			}
		}
		
		// Dialog after thumbs generation
		if (isset($this->returnMakeAllThumbs)){
			if (!is_bool($this->returnMakeAllThumbs)){
				$js .= $this->removeScriptTags($this->returnMakeAllThumbs);
			}
		}
		
		// Error dialog if images missing on filesystem
		if (isset($this->fileErrorDialog)){
			if (!is_bool($this->fileErrorDialog)){
				$js .= $this->removeScriptTags($this->fileErrorDialog);
			}
		}
		
		// Pack Javascript
		if ($pack){
			$myPacker = new JavaScriptPacker($js, 'Normal', true, false);
			$js = $myPacker->pack();		
		}

		elseif ($rn){
			$js = str_replace('; ',";".$rnStr,$js);
		}
		
		/*$js = "\n<script type=\"text/javascript\">$rnStr$js$rnStr</script>$rnStr";*/
		return $js;
	}

	function getBrowserInfo() {
		$SUPERCLASS_NAMES  = "gecko,mozilla,mosaic,webkit";
		$SUPERCLASSES_REGX = "(?:".str_replace(",", ")|(?:", $SUPERCLASS_NAMES).")";
	
		$SUBCLASS_NAMES    = "opera,msie,firefox,chrome,safari";
		$SUBCLASSES_REGX   = "(?:".str_replace(",", ")|(?:", $SUBCLASS_NAMES).")";
	
		$browser      = "unsupported";
		$majorVersion = "0";
		$minorVersion = "0";
		$fullVersion  = "0.0";
		$os           = 'unsupported';
	
		$userAgent    = strtolower($_SERVER['HTTP_USER_AGENT']);
	
		$found = preg_match("/(?P<browser>".$SUBCLASSES_REGX.")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches);
		if (!$found) {
			$found = preg_match("/(?P<browser>".$SUPERCLASSES_REGX.")(?:\D*)(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches);
		}
	   
		if ($found) {
			$browser      = $matches["browser"];
			$majorVersion = $matches["majorVersion"];
			$minorVersion = $matches["minorVersion"];
			$fullVersion  = $matches["majorVersion"].$matches["minorVersion"];
			if ($browser != "safari") {
				if (preg_match("/version\/(?P<majorVersion>\d*)(?P<minorVersion>(?:\.\d*)*)/i",$userAgent, $matches)){
					$majorVersion = $matches["majorVersion"];
					$minorVersion = $matches["minorVersion"];
					$fullVersion  = $majorVersion.".".$minorVersion;
				}
			}
			if ($browser == "msie") {
				if (stristr($userAgent, 'Trident/5.0')){
					$majorVersion = 9;
					$minorVersion = 0;
					$fullVersion  = $majorVersion.".".$minorVersion;				
				}
			}
		}
	
		if (strpos($userAgent, 'linux')) {
			$os = 'linux';
		}
		else if (strpos($userAgent, 'macintosh') || strpos($userAgent, 'mac os x')) {
			$os = 'mac';
		}
		else if (strpos($userAgent, 'windows') || strpos($userAgent, 'win32')) {
			$os = 'windows';
		}
	
		return array(
			"browser"      => $browser,
			"majorVersion" => $majorVersion,
			"minorVersion" => $minorVersion,
			"fullVersion"  => $fullVersion,
			"os"           => $os);
	}	
	
	function icon($zoom, $name, $ins = ''){
		return "src=\"".$zoom['config']['icon'].$zoom['config']['icons'][$name]['file'].$ins.'.'.$zoom['config']['icons'][$name]['ext']."\" width=\"".$zoom['config']['icons'][$name]['w']."\" height=\"".$zoom['config']['icons'][$name]['h']."\" alt=\"".$zoom['config']['mapButTitle'][$name]."\" unselectable=\"on\" title=\"\"";
	}

	/**
	  * This method outputs the main html for zoom
	  * @access public
	  * @param array $zoom
	  * @param array $zoomTmp
	  * @param array $op Options
	  * @return HTML-Output
	  **/

	public function drawZoomBox($zoom, $zoomTmp){
		
		// Browser
		$browser = $this->getBrowserInfo();
		
		$return='';
		
		$zoomW=$zoom['config']['smallImgSize'][0];
		$zoomH=$zoom['config']['smallImgSize'][1];
		
		if ($zoom['config']['keepBoxW']){$zoomW=$zoom['config']['picX'];}
		if ($zoom['config']['keepBoxH']){$zoomH=$zoom['config']['picY'];}

		$extPix = intval($zoom['config']['innerMargin']*2);
		$extPixBoth = 0;
		$extPixGal = 0;
		$zoomGalWidth = 0;
		
		if ($zoom['config']['useGallery'] AND $zoom['config']['galleryMarginLeft']){
			$extPix += $zoom['config']['galleryMarginLeft'];
			$extPixBoth += $zoom['config']['galleryMarginLeft'];
		}
		
		
		
		// Vertical gallery
		if ($zoom['config']['useGallery']){
			$galleryThumbSpace = $zoom['config']['galPicX'] + 
			$zoom['config']['galleryCssMargin'] + 
			$zoom['config']['galleryCssBorderWidth'] * 2 + 
			$zoom['config']['galleryCssPadding'] * 2;
			
			$gallerySpace = $zoom['config']['galleryScrollbarWidth'] + $galleryThumbSpace;
			
			if ($zoom['config']['galleryLines'] > 1){
				$gallerySpace += ($zoom['config']['galleryLines'] - 1) * $galleryThumbSpace;
			}
			
			$extPixGal = $gallerySpace + $extPix;
			$zoomGalWidth = $gallerySpace;
		}else{
			$extPixGal = $extPix;
		}
		
		$zoomGalHeight = $zoomH + $extPix;
		
		if ($zoom['config']['displayNavi']){
			$zoomGalHeight += $zoom['config']['naviHeight'];
		}
		
		
		if ($zoom['config']['zoomStat'] AND $zoom['config']['zoomStatHeight']){
			$zoomGalHeight += (int)$zoom['config']['zoomStatHeight'];
		}
		
		// Add height for horizontal gallery
		if ($zoom['config']['useHorGallery']){
			$zoomGalHeight += (int)$zoom['config']['galHorHeight'] + $zoom['config']['galHorMarginTop'] + $zoom['config']['galHorMarginBottom'];
		}
		
		// Add height for the slider
		if ($zoom['config']['spinMod'] && $zoom['config']['spinSlider']){
			$zoomGalHeight += $zoom['config']['spinSliderContainerHeight'];
		}
		

		$deviderTD = "<td style='width: ".($zoom['config']['naviGroupSpace'])."px'>";
		$deviderTD .= "<img src='".$zoom['config']['icon']."empty.gif' style='width: ".round($zoom['config']['naviGroupSpace']/2)."px; height: 10px;' alt='' title=''>";
		$deviderTD .= "</td>";
		
		// Prev Next buttons
		$galleryNavi = '';
		if ($zoom['config']['galleryNavi']){
		
			$galleryNavi = "<table cellspacing='0' cellpadding='0' style='height: ".$zoom['config']['galleryNaviHeight']."px'><tbody><tr>";
				$galleryNavi .= "<td style='width: ".($zoom['config']['galleryButtonSpace'] + $zoom['config']['icons']['prev']['w'])."px; text-align: left; vertical-align: middle;'>";
					$galleryNavi .= "<img id='zoomNaviPrev' ".$this->icon($zoom, 'prev').">";
				$galleryNavi .= "</td>";	
				
				if ($zoom['config']['galleryPlayButton']){
					$galleryNavi .= "<td style='width: ".($zoom['config']['galleryButtonSpace'] + $zoom['config']['icons']['play']['w'])."px; text-align: left; vertical-align: middle;'>";
						$galleryNavi .= "<img id='zoomNaviPlayPause' ".$this->icon($zoom, 'play').">";
					$galleryNavi .= "</td>";
				}
				
				$galleryNavi .= "<td style='width: ".($zoom['config']['icons']['next']['w'])."px; text-align: left; vertical-align: middle;'>";
					$galleryNavi .= "<img id='zoomNaviNext' ".$this->icon($zoom, 'next').">";
				$galleryNavi .= "</td>";
			$galleryNavi .= "</tr></tbody></table>";
			
			$galleryNavi = "<DIV id='zoomGalleryNaviButtons' style='float: right'>".$galleryNavi."</DIV>";
		}
		

		// Horizontal Gallery
		$horGallery = '';
		if ($zoom['config']['useHorGallery']){
			$horGalHeightExt = 0;
			if($zoom['config']['quirks'] AND $browser['browser'] == 'msie'){
				$horGalHeightExt = $zoom['config']['galHorMarginTop'] + $zoom['config']['galHorMarginBottom'];
			}
			$horGallery .= "<DIV id='zoomGalHorCont' class='zoomGalleryHorizontalContainer' style='padding: ".$zoom['config']['galHorMarginTop']."px 0px ".$zoom['config']['galHorMarginBottom']."px 0px; width:".($zoomW+$extPix)."px; height: ".($zoom['config']['galHorHeight']+$horGalHeightExt)."px'>";
				$horGallery .= "<DIV id='zoomGalHor' class='zoomGalleryHorizontal' style='margin: 0px 0px 0px ".$zoom['config']['innerMargin']."px; width:".$zoomW."px; height: ".$zoom['config']['galHorHeight']."px'>";
					
					$horGalArrowMarginTop = floor(($zoom['config']['galHorHeight'] - $zoom['config']['icons']['arrowLeft']['h'])/2);

					if ($zoom['config']['galHorArrows']){
						$horGallery .= "<DIV id='zoomGalHorArrowLeft' class='zoomGalleryHorizontalArrow' style='float: left; height: ".$zoom['config']['galHorHeight']."px; width: ".$zoom['config']['galHorArrowWidth']."px'>";
							$horGallery .= "<img id='horGalLeft' align='left' style='margin-top: ".$horGalArrowMarginTop."px;' ".$this->icon($zoom, 'arrowLeft').">";
						$horGallery .= "</DIV>";
					}
					
					$horGalleryInnerWidth = $zoomW - ( ($zoom['config']['galHorArrows'] === true) ? ($zoom['config']['galHorArrowWidth']*2) : 0);
					
					$horGalleryInnerCorner = '';
					if ($zoom['config']['galHorInnerCorner'] AND $zoom['config']['galHorInnerCornerWidth']){
						$horGalCornW = $zoom['config']['galHorInnerCornerWidth'];
						$horGalleryInnerCornerImage = $zoom['config']['icon'].$zoom['config']['galHorInnerCornerImage'];
						$horGalleryInnerCorner = "
						<DIV class='zoomGalleryHorizontalCorner' style='left: 0px; top: 0px; width: ".$horGalCornW."px; height: ".$horGalCornW."px; background-image: url($horGalleryInnerCornerImage);'></DIV>
						<DIV class='zoomGalleryHorizontalCorner' style='right: 0px; top: 0px; width: ".$horGalCornW."px; height: ".$horGalCornW."px; background-image: url($horGalleryInnerCornerImage); background-position: -".$horGalCornW."px 0px;'></DIV>
						<DIV class='zoomGalleryHorizontalCorner' style='right: 0px; bottom: 0px; width: ".$horGalCornW."px; height: ".$horGalCornW."px; background-image: url($horGalleryInnerCornerImage); background-position: -".$horGalCornW."px -".$horGalCornW."px;'></DIV>
						<DIV class='zoomGalleryHorizontalCorner' style='left: 0px; bottom: 0px; width: ".$horGalCornW."px; height: ".$horGalCornW."px; background-image: url($horGalleryInnerCornerImage); background-position: 0px -".$horGalCornW."px;'></DIV>						
						";
					}

					$horGallery .= "
					<DIV id='zoomGalHorArea' style='position: relative; float: left; overflow: hidden; width: ".$horGalleryInnerWidth."px; height: ".$zoom['config']['galHorHeight']."px;'>
						<DIV id='zoomGalHorDiv' style='position: absolute; width:99999px; height: ".$zoom['config']['galHorHeight']."px;'></DIV>
						$horGalleryInnerCorner
					</DIV>";
					
					if ($zoom['config']['galHorArrows']){
						$horGallery .= "<DIV id='zoomGalHorArrowRight' class='zoomGalleryHorizontalArrow' style='float: right; height: ".$zoom['config']['galHorHeight']."px; width: ".$zoom['config']['galHorArrowWidth']."px'>";
							$horGallery .= "<img id='horGalRight' align='right' style='margin-top: ".$horGalArrowMarginTop."px;' ".$this->icon($zoom, 'arrowRight').">";
						$horGallery .= "</DIV>";
					}
					
					
				$horGallery .= "</DIV>";
			$horGallery .= "</DIV>";
		}
		
		// Slider for 360 spin
		$zoomSliderSpin = '';
		if ($zoom['config']['spinMod'] && $zoom['config']['spinSlider']){
			$zoomSliderSpin .= "<DIV id='zoomSliderSpinContainer' class='zoomSliderSpinContainer' style='width:".($zoomW+$extPix)."px; height:".$zoom['config']['spinSliderContainerHeight']."px;'>";
				$zoomSliderSpin .= "<DIV style='height: 30px; margin: ".$zoom['config']['spinSliderContainerPadding']['top']."px ".$zoom['config']['spinSliderContainerPadding']['right']."px ".$zoom['config']['spinSliderContainerPadding']['bottom']."px  ".$zoom['config']['spinSliderContainerPadding']['left']."px;'>";
					$zoomSliderWrapWidth = $zoomW+$extPix-$zoom['config']['spinSliderContainerPadding']['right']-$zoom['config']['spinSliderContainerPadding']['left'];
					if ($zoom['config']['spinSliderPlayButton']){
						$zoomSliderSpin .= "<DIV style='margin-right: 20px; width: ".$zoom['config']['icons']['spinPlay']['w']."px; height: ".$zoom['config']['icons']['spinPlay']['h']."px; float: left;'>";
							$zoomSliderSpin .= "<img id='spinPlayPause' ".$this->icon($zoom, 'spinPlay')." style='display: none'>";
						$zoomSliderSpin .= "</DIV>";
						
						$zoomSliderWrapWidth = $zoomSliderWrapWidth - $zoom['config']['icons']['spinPlay']['w'] - 20;
					}
					
					$zoomSliderSpin .= "<DIV style='float: left; width: ".$zoomSliderWrapWidth."px; padding-top: ".$zoom['config']['spinSliderTopMargin']."px;'>";
						$zoomSliderSpin .= "<DIV id='zoomSliderSpin' style='font-size: ".$zoom['config']['spinSliderHandleSize']."px; height: ".$zoom['config']['spinSliderHeight']."px; ".($zoom['config']['spinSliderWidth'] ? ' width: '.$zoom['config']['spinSliderWidth'].'px;' : '')."'></DIV>";
					$zoomSliderSpin .= "</DIV>";
					
				$zoomSliderSpin .= "</DIV>";
			$zoomSliderSpin .= "</DIV>";
		}
		
		// Navigation
		$zoomNavi = '';
		
		if ($zoom['config']['spinSliderPosition'] == 'naviTop'){
			$zoomNavi .= $zoomSliderSpin;
		}
		
		$zoomNavi .= "<DIV id='zoomNavigation' class='zoomNavigation'  style='width:".($zoomW+$extPix)."px; height:".$zoom['config']['naviHeight']."px; display: ".( $zoom['config']['displayNavi'] ? 'block' : 'none' )."'>"; //
			// $zoom['config']['innerMargin']
			// $zoom['config']['cornerRadius']

			$navFloat = 'left';

			if ($zoom['config']['useGallery'] AND $zoom['config']['galleryMarginLeft'] AND $zoom['config']['galleryPos'] == 'left'){
				$navFloat = 'right';
			}

			$navMargin = "margin: ".$zoom['config']['naviTopMargin']."px ".$zoom['config']['innerMargin']."px 0px ".$zoom['config']['innerMargin']."px;";
			$navWidth = $zoomW;
			
			// Min margin
			if ($zoom['config']['innerMargin'] < $zoom['config']['naviMinPadding']){ 
				$dMargin = $zoom['config']['naviMinPadding'] - $zoom['config']['innerMargin']; 
				if ($zoom['config']['useGallery']){ // only on side
					$navWidth = $navWidth - $dMargin;
					if ($zoom['config']['galleryPos'] == 'left'){
						$navMargin = "margin: ".$zoom['config']['naviTopMargin']."px ".($dMargin+$zoom['config']['innerMargin'])."px 0px 0px;";
					}elseif($zoom['config']['galleryPos'] == 'right'){
						$navMargin = "margin: ".$zoom['config']['naviTopMargin']."px 0px 0px ".($dMargin+$zoom['config']['innerMargin'])."px;";
					}
				}else{
					$navWidth = $navWidth - ($dMargin * 2);
					$navMargin = "margin: ".$zoom['config']['naviTopMargin']."px ".($dMargin+$zoom['config']['innerMargin'])."px 0px ".($dMargin+$zoom['config']['innerMargin'])."px;";
				}
			}
			
			$zoomNavi .= "<DIV id='zoomNaviInner' style='display: inline; float: $navFloat; text-align: left; padding: 0px; width: ".$navWidth."px; $navMargin'>";
 
				$zoomNavi .= "<table id='zoomNaviTableOuter' cellspacing='0' cellpadding='0' style='padding: 0px; margin: 0px; width:".($navWidth)."px; height: ".($zoom['config']['naviHeight']-$zoom['config']['naviTopMargin'])."px'><tbody><tr>";
					
					$naviInfo = '';
					$naviInfoAlign = ($zoom['config']['naviFloat'] == 'right') ? 'left' : 'right';
					
					// Optionaly disable zoom level
					if (!$zoom['config']['zoomLogInfoDisabled']){
						if ($zoom['config']['zoomLogInfo']){
							$naviInfo .= "<td>";
								$naviInfo .= "<div class='zoomLogHolder' id='zoomLogHolder' style='float: ".$naviInfoAlign."; text-align: ".$naviInfoAlign.";'>";
									$naviInfo .= "<div id='zoomTime' class='zoomLog'></div>";
									$naviInfo .= "<div id='zoomLevel' class='zoomLog'></div>";
									$naviInfo .= "<div id='zoomTraffic' class='zoomLog'></div>";
								$naviInfo .= "</div>";
							$naviInfo .= "</td>";
						}elseif ($zoom['config']['zoomLogJustLevel']){
							$naviInfo .= "<td style='vertical-align: top;'>
								<div class='zoomLogHolder' id='zoomLogHolder' style='float: ".$naviInfoAlign."; text-align: ".$naviInfoAlign.";'>
									<div id='zoomLevel' class='zoomLogJustLevel'></div>
								</div>
							</td>";							
						}
					}
					
					$naviButtons = "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle;'>";
						
						// Button Navi
						$naviButtons .= "<table id='zoomNaviTable' cellspacing='0' cellpadding='0' style='float: ".$zoom['config']['naviFloat']."'><tbody><tr>";
								
							$toolsSpacer = false;
							
							// Pan button
							if ($zoom['config']['naviPanButSwitch']){
								$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['pan']['w'] + $zoom['config']['naviSpace'])."px'>";
									$naviButtons .= "<img id='zoomNaviPan' ".$this->icon($zoom, 'pan').">";
								$naviButtons .= "</td>";
								$toolsSpacer = true;
							}
							
							// Spin button
							if ($zoom['config']['spinMod'] && $zoom['config']['naviSpinButSwitch']){
								$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['spin']['w'] + $zoom['config']['naviSpace'])."px'>";
									$naviButtons .= "<img id='zoomNaviSpin' ".$this->icon($zoom, 'spin').">";
								$naviButtons .= "</td>";
								$toolsSpacer = true;
							}
							
							// Crop button
							if ($zoom['config']['naviCropButSwitch']){
								$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['crop']['w'] + $zoom['config']['naviSpace'])."px'>";
									$naviButtons .= "<img id='zoomNaviCrop' ".$this->icon($zoom, 'crop').">";
								$naviButtons .= "</td>";
								$toolsSpacer = true;
							}
						
							// Spacer cell
							if ($toolsSpacer){
								$naviButtons .= $deviderTD;
							}
							
							// Zoom In, Out
							if ($zoom['config']['naviZoomBut']){
								if ($zoom['config']['naviBigZoom']){
							
									$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['zoomOutBig']['w'] + $zoom['config']['naviSpace'])."px'>";
										$naviButtons .= "<img id='zoomNaviOut' ".$this->icon($zoom, 'zoomOutBig').">";
									$naviButtons .= "</td>";
									
									$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['zoomInBig']['w'] + $zoom['config']['naviSpace'])."px'>";
										$naviButtons .= "<img id='zoomNaviIn' ".$this->icon($zoom, 'zoomInBig').">";
									$naviButtons .= "</td>";
							
								} else {
									$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['zoomIn']['w'] + $zoom['config']['naviSpace'])."px'>";
										$naviButtons .= "<table cellspacing='0' cellpadding='0'><tbody>";
											$naviButtons .= "<tr><td style='width: ".($zoom['config']['icons']['zoomIn']['w'])."px;'><img id='zoomNaviIn' style='vertical-align: bottom; margin-bottom: 3px' ".$this->icon($zoom, 'zoomIn')."></td></tr>";
											$naviButtons .= "<tr><td style='width: ".($zoom['config']['icons']['zoomOut']['w'])."px; vertical-align: top;'><img id='zoomNaviOut' ".$this->icon($zoom, 'zoomOut')."></td></tr>";
										$naviButtons .= "</tbody></table>";
									$naviButtons .= "</td>";
								}
							
								// Devider
								$naviButtons .= $deviderTD;
							}
							
							// Top, left, right, bottom NAVI
							if ($zoom['config']['naviPanBut']){
							
								$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['moveLeft']['w']+4 + $zoom['config']['icons']['moveTop']['w'] + $zoom['config']['icons']['moveRight']['w']+2)."px'>";
	
									$naviButtons .= "<table cellspacing='0' cellpadding='0'><tbody><tr>";
										$naviButtons .= "<td style='width: ".($zoom['config']['icons']['moveLeft']['w']+2)."px; vertical-align: middle;'><img id='zoomNaviML' style='margin-right:2px' ".$this->icon($zoom, 'moveLeft')."></td>";
										$naviButtons .= "<td style='width: ".($zoom['config']['icons']['moveTop']['w'])."px; vertical-align: middle;'>";
										$naviButtons .= "<img id='zoomNaviMT' style='vertical-align: bottom; margin-bottom: 2px' ".$this->icon($zoom, 'moveTop').">";
										$naviButtons .= "<img id='zoomNaviMB' style='vertical-align: top;' ".$this->icon($zoom, 'moveBottom').">";
										$naviButtons .= '</td>';
										$naviButtons .= "<td style='width: ".($zoom['config']['icons']['moveRight']['w']+2)."px; vertical-align: middle;'><img id='zoomNaviMR' style='margin-left:2px' ".$this->icon($zoom, 'moveRight')."></td>";
									$naviButtons .= "</tr></tbody></table>";
	
								$naviButtons .= "</td>";
								
								// Devider
								$naviButtons .= $deviderTD;
							}
							
							// Restore
							if ($zoom['config']['naviRestoreBut']){
								$naviButtons .= "<td valign='middle' style='text-align: left; width: ".($zoom['config']['icons']['reset']['w'] + $zoom['config']['naviSpace'])."px'>";
									$naviButtons .= "<img id='zoomNavi100' ".$this->icon($zoom, 'reset').">";
								$naviButtons .= "</td>";
							}

						$naviButtons .= "</tr></tbody></table>";
						
					$naviButtons .= "</td>";			

					// Navi other controls
					if (
						($zoom['config']['useFullGallery'] && $zoom['config']['galFullButton']) || 
						($zoom['config']['mapButton'] && $zoom['config']['useMap']) || 
						$zoom['config']['help'] || 
						$zoom['config']['naviHotspotsBut'] || 
						($zoom['config']['fullScreenNaviButton'] && $zoom['config']['fullScreenEnable']) || 
						($zoom['config']['downloadButton'] && $zoom['config']['allowDownload']) || 
						($zoom['config']['fullScreenEnable'] && $zoom['config']['fullScreenNaviButton'])
					){
						
							
						$zoomNaviControls = "<table id='zoomNaviControls' cellspacing='0' cellpadding='0' style='float: ".$zoom['config']['naviFloat']."'><tbody><tr>";
						$zoomNaviControlsWidth = 0;
						
						// Inline Gallery
						if ($zoom['config']['useFullGallery'] AND $zoom['config']['galFullButton']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['gallery']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['gallery']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomGalButton' ".$this->icon($zoom, 'gallery').">";
							$zoomNaviControls .= "</td>";								
						}
						
						// Map on / off
						if ($zoom['config']['mapButton'] AND $zoom['config']['useMap']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['map']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['map']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomMapButton' ".$this->icon($zoom, 'map', '_switched').">";
							$zoomNaviControls .= "</td>";								
						}
						
						// Help
						if ($zoom['config']['help']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['help']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['help']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomHelp' ".$this->icon($zoom, 'help').">";
							$zoomNaviControls .= "</td>";
						}
						
						// Download Buton
						if ($zoom['config']['downloadButton'] && $zoom['config']['allowDownload']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['download']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['download']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomDownload' ".$this->icon($zoom, 'download').">";
							$zoomNaviControls .= "</td>";								
						}

						if ($zoom['config']['naviHotspotsBut']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['hotspots']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['hotspots']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomHotspots' ".$this->icon($zoom, 'hotspots').">";
							$zoomNaviControls .= "</td>";								
						}
						
						// Fullscreen button
						if ($zoom['config']['fullScreenEnable'] AND $zoom['config']['fullScreenNaviButton']){
							$zoomNaviControlsWidth += ($zoom['config']['icons']['fullScreen']['w'] + $zoom['config']['naviSpace']);
							
							$zoomNaviControls .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".($zoom['config']['icons']['fullScreen']['w'] + $zoom['config']['naviSpace'])."px'>";
								$zoomNaviControls .= "<img id='zoomFullScreenButton' ".$this->icon($zoom, 'fullScreen').">";
							$zoomNaviControls .= "</td>";								
						}	

						$zoomNaviControls .= "</tr></tbody></table>";
						
						$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".$zoomNaviControlsWidth."px'>";
							$naviButtons .= $zoomNaviControls;
						$naviButtons .= "</td>";	
							
					}		
								
					// Gallery Navi
					if ($zoom['config']['galleryNavi'] AND $zoom['config']['galleryNaviPos'] == 'navi'){
						$galleryNaviWidth = $zoom['config']['icons']['prev']['w'] + $zoom['config']['icons']['next']['w'] + $zoom['config']['galleryButtonSpace'];
						if ($zoom['config']['galleryPlayButton']){
							$galleryNaviWidth += $zoom['config']['galleryButtonSpace'] + $zoom['config']['icons']['play']['w'];
						}
						$galleryNaviWidth += $zoom['config']['naviGroupSpace'];
						$naviButtons .= "<td style='text-align: ".$zoom['config']['naviFloat']."; vertical-align: middle; width: ".$galleryNaviWidth."px'>";
						$naviButtons .= $galleryNavi;
						$naviButtons .= "</td>";
					}


					// End Button Navi
					
					
				if ($zoom['config']['naviFloat'] == 'right'){
					$zoomNavi .= $naviInfo . $naviButtons;
				}else{
					$zoomNavi .= $naviButtons . $naviInfo;
				}							
					
					
				$zoomNavi .= "</tr></tbody></table>";
				
			$zoomNavi .= "</DIV>";
		// End zoomNavigation
		$zoomNavi .= "</DIV>";
		
		if ($zoom['config']['spinSliderPosition'] == 'naviBottom'){
			$zoomNavi .= $zoomSliderSpin;
		}
		
		// Vertical gallery
		$vertGallery = '';
		if ($zoom['config']['useGallery']){
			$vertGallery = "<DIV id='zoomGalleryVerticalContainer' class='zoomGalleryVerticalContainer' style='float: ".$zoom['config']['galleryPos']."; width: ".($extPixGal-$extPix)."px; height:".($zoomGalHeight-$extPixBoth)."px;'>";
				
				// Gallery Navigation
				if ($zoom['config']['galleryNavi'] AND in_array($zoom['config']['galleryNaviPos'], array('top', 'bottom'))){
					$galleryNaviVert = "<DIV class='zoomGalleryVerticalNavi' id='zoomGalleryNavi' style='text-align: left; padding: 0px; width: ".($zoomGalWidth)."px; height: ".$zoom['config']['galleryNaviHeight']."px;'>";
						foreach ($zoom['config']['galleryNaviMargin'] as $k=>$v){$galleryNaviMargin.=$v.'px ';}
						$galleryNaviVert .= "<DIV style='display: inline; margin: $galleryNaviMargin; float: right; padding: 0px;'>";
						$galleryNaviVert .= $galleryNavi;
						$galleryNaviVert .= "</DIV>";
					$galleryNaviVert .= "</DIV>";
				} 
				
				if ($zoom['config']['galleryNavi'] AND $zoom['config']['galleryNaviPos'] == 'top'){
					$vertGallery .= $galleryNaviVert;
				}

				
				$galHeightReduce = 0;
				if ($zoom['config']['galleryNavi'] AND in_array($zoom['config']['galleryNaviPos'], array('top', 'bottom'))){

					$galHeightReduce = $zoom['config']['galleryNaviHeight'];
				}
				
				$vertGallery .= "<DIV id='zoomGallery' class='zoomGalleryVertical' style='width: ".$zoomGalWidth."px; height:".($zoomGalHeight - $extPixBoth - $galHeightReduce)."px;'></DIV>";
				
				if ($zoom['config']['galleryNavi'] AND $zoom['config']['galleryNaviPos'] == 'bottom'){
					$vertGallery .= $galleryNaviVert;
				}
				
			$vertGallery .= "</DIV>";
		}
		

		//////////////////////
		//// Start return ////
		//////////////////////
		
		// Outer Div
		if ($zoom['config']['layHorCenter']){
			if ($zoom['config']['quirks'] AND $browser['browser'] == 'msie'){
				// Centering layout for transitional
				$return .= "<DIV id='zoomAll' class='zoomAll' style='margin: 0 auto; width: ".($zoomW+$extPixGal)."px; overflow-x: hidden; text-align: center;'>";			
			}else{
				$return .= "<DIV id='zoomAll' class='zoomAll' style='margin: 0 auto; width: ".($zoomW+$extPixGal)."px; overflow-x: hidden;'>";
			}
		}else{
			$return .= "<DIV id='zoomAll' class='zoomAll' style='margin: 0; width: ".($zoomW+$extPixGal)."px; overflow-x: hidden;'>";
		}
		
		
			// Top Margin
			if ($zoom['config']['layVertCenter'] === true){
				$return .= "<DIV id='zoomTopMargin' style='clear: both; float: left; width:".($zoomW+$extPixGal)."px; height: 0px; line-height: 0px;'></DIV>";	
			}
			elseif (intval($zoom['config']['layVertCenter']) >= 1){
				$return .= "<DIV id='zoomTopMargin' style='clear: both; float: left; width:".($zoomW+$extPixGal)."px; height: ".$zoom['config']['layVertCenter']."px; line-height: 1px;'></DIV>";	
			}				

			// Vertical gallery
			if ($zoom['config']['useGallery']){
				$return .= $vertGallery;
			}
			
			// Horizontal gallery
			if ($zoom['config']['useHorGallery'] AND $zoom['config']['galHorPosition'] == 'top1'){
				$return .= $horGallery;
			}
			
			// Position Navi
			if ($zoom['config']['naviPos'] == 'top'){
				$return .= $zoomNavi;
			}
			
			// Position Slider
			if ($zoom['config']['spinSliderPosition'] == 'top'){
				$return .= $zoomSliderSpin;
			}
			
			// Horizontal gallery
			if ($zoom['config']['useHorGallery'] AND $zoom['config']['galHorPosition'] == 'top2'){
				$return .= $horGallery;
			}						
			
			
			$return .= "<DIV id='zoomBorder' class='zoomBorder' style='width:".($zoomW+$extPix)."px; height:".($zoomH+$extPix-$extPixBoth)."px;'>";

				$zoomContainerFloat = 'float: left;';
				if ($zoom['config']['useGallery'] AND $zoom['config']['galleryMarginLeft'] AND $zoom['config']['galleryPos'] == 'left'){
					$zoomContainerFloat = 'float: right;';
				}

				$return .= "<DIV id='zoomContainer' class='zoomContainer' style='width:".($zoomW)."px; height:".($zoomH)."px; text-align: left; margin: ".$zoom['config']['innerMargin']."px; $zoomContainerFloat'>";
					
					$return .= "<DIV id='zoomLoaderHolder' class='zoomLoaderHolder'>";
						$return .= "<DIV id='zoomLoader' class='".$zoom['config']['zoomLoaderClass']."'></DIV>";
					$return .= "</DIV>";
					
					// Warning
					$return .= "<DIV id='zoomWarning' class='zoomWarning'></DIV>";

					// Back Pic
					$return .= "<DIV id='".$zoom['config']['backDiv']."' class='zoomedBack' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
						$return .= "<DIV id='".$zoom['config']['backInnerDiv']."' class='zoomedBackImage' style='width:".($zoomW)."px; height:".($zoomH)."px;'><img src='".$zoom['config']['icon']."empty.gif' id='".$zoom['config']['backLayer']."' style='position: static; width:".($zoom['config']['smallImgSize'][0])."px; height:".($zoom['config']['smallImgSize'][1])."px;' alt='' unselectable=\"on\"></DIV>";
					$return .= "</DIV>";
					

					// Zoomed Image
					$return .= "<DIV id='zoomedImageContainer' class='zoomedImageContainer' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
						$return .= "<DIV id='zoomedImage' class='zoomedImage' style='width:".($zoomW)."px; height:".($zoomH)."px;'><img src=\"".$zoom['config']['icon']."empty.gif\" id=\"".$zoom['config']['picLayer']."\" style=\"position: static; width:".($zoom['config']['smallImgSize'][0])."px; height:".($zoom['config']['smallImgSize'][1])."px;\" alt=\"\" unselectable=\"on\"></DIV>";
					$return .= "</DIV>";
					
					// Transparent Overpic
					$return .= "<DIV id='zoomLayer' class='zoomLayer' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
						if ($zoom['config']['vWtrmrk']){
							$return .= "<DIV id='zoomWtrmrk' class='".$zoom['config']['vWtrmrk']."' style='width:".($zoomW)."px; height:".($zoomH)."px;'></DIV>";
						}
						//$return .= "<DIV style='opacity: 0.75; z-index: 2; background-color: red; position: absolute; left: ".($zoomW-12)."px; top: 10px; width: 2px; height:".($zoomH-20)."px;'></DIV>";
						$return .= "<img src='".$zoom['config']['icon']."empty.gif' id='".$zoom['config']['overLayer']."' class='zoomLayerImg' style='width:".($zoomW)."px; height:".($zoomH)."px;' alt='' unselectable='on'>";
					$return .= "</DIV>";
					
					// Full Area Gallery
					if ($zoom['config']['useFullGallery'] && !$zoom['config']['spinMod']){
						$return .= "<DIV id='zoomFullGalleryHolder' class='zoomFullGalleryHolder' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
							$return .= "<DIV id='zoomFullGallery' class='zoomFullGallery' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
							$return .= "</DIV>";
						$return .= "</DIV>";
					}
					
					// Help
					if ($zoom['config']['help']){
						$return .= "<DIV id='zoomedHelpHolder' class='zoomedHelpHolder' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
							$return .= "<DIV id='zoomedHelp' class='zoomedHelp' style='left: ".$zoom['config']['helpMargin']."px; top: ".$zoom['config']['helpMargin']."px; width:".($zoomW-($zoom['config']['helpMargin']*2))."px; height:".($zoomH-($zoom['config']['helpMargin']*2))."px;'>";
							
							if ($zoom['config']['helpUrl']){
								$return .= "<iframe SRC='".$zoom['config']['helpUrl']."' WIDTH='".($zoomW-($zoom['config']['helpMargin']*2))."' HEIGHT='".($zoomH-($zoom['config']['helpMargin']*2))."' FRAMEBORDER='0'></iframe>";
							}
							if ($zoom['config']['helpTxt']){
								// html with images can cause IE8 to not finisch loading the page
								// the text ($zoom['config']['helpTxt']) will be set via javascript...
							}
							$return .= "</DIV>";
						$return .= "</DIV>";					
					}
					
					// Description
					$return .= "<DIV id='zoomDescrHolder' class='zoomDescrHolder' style='width:".($zoomW)."px; height:".($zoomH)."px;'>";
						$return .= "<DIV id='zoomDescr' class='zoomDescr' style='width:".($zoomW)."px; height: ".$zoom['config']['descrAreaHeight']."px; top: ".($zoomH-$zoom['config']['descrAreaHeight'])."px;'></DIV>";
					$return .= "</DIV>";		
					
				
				// End zoomContainer
				$return .= "</DIV>";
				
			// End zoomBorder
			$return .= "</DIV>";
			
			if ($zoom['config']['useHorGallery'] AND $zoom['config']['galHorPosition'] == 'bottom1'){
				$return .= $horGallery;
			}
			
			if ($zoom['config']['naviPos'] == 'bottom'){
				$return .= $zoomNavi;
			}
			
			if ($zoom['config']['spinSliderPosition'] == 'bottom'){
				$return .= $zoomSliderSpin;
			}

			if ($zoom['config']['useHorGallery'] AND $zoom['config']['galHorPosition'] == 'bottom2'){
				$return .= $horGallery;
			}
			
			if ($zoom['config']['zoomStat']){
				$return .= "<DIV id='zoomAdmin' class='zoomAdmin' style='width:".($zoomW+$extPix)."px; height:".$zoom['config']['zoomStatHeight']."px'><div id='zoomAdminHtml' style='padding:0 5px'></div></DIV>";
			}
			
			// Pseudo Div for ajax
			$return .= "<DIV id='zoomOpr' style='height: 0px; visibility: hidden; display: none; overflow: hidden;'><a href='http://www.ajax-zoom.com'>jQuery Image Zoom & Pan Gallery</a></DIV>";

			// Visual configuration block
			if ($zoom['config']['visualConf']){
				$return .= $this->visualConf($zoom, $zoomTmp, $zoomW, $extPixGal);
			}
			
			if ($zoom['config']['layVertBotMrg'] === true){
				$return .= "<DIV style='clear: both; float: left; width:".($zoomW+$extPixGal)."px; height: 0px; line-height: 0px;'></DIV>";
			} elseif (intval($zoom['config']['layVertBotMrg']) >= 1){
				$return .= "<DIV style='clear: both; float: left; width:".($zoomW+$extPixGal)."px; height: ".$zoom['config']['layVertBotMrg']."px; line-height: 1px;'></DIV>";
			}
		
		// End Layout
		$return .= "</DIV>";
		
		if ($zoom['config']['cornerRadius'] > 0){
			$return = "<DIV id='zoomCornerRadius' class='zoomCornerRadius' style='display: block; width: ".($zoomW+$extPixGal)."px; padding: ".$zoom['config']['cornerRadius']."px;'>".$return."</DIV>";
		}
		
		// Dialog if first image has been made...
		if (isset($this->returnMakeFirstImage)){
			if (!is_bool($this->returnMakeFirstImage)){
				$return .= $this->returnMakeFirstImage;
			}
		}
		
		// Dialog if zoom tiles have been made...
		if (isset($this->returnMakeZoomTiles)){
			if (!is_bool($this->returnMakeZoomTiles)){
				$return .= $this->returnMakeZoomTiles;
			}
		}
		
		// Dialog after thumbs generation
		if (isset($this->returnMakeAllThumbs)){
			if (!is_bool($this->returnMakeAllThumbs)){
				$return .= $this->returnMakeAllThumbs;
			}
		}
		
		// Error dialog if images missing on filesystem
		if (isset($this->fileErrorDialog)){
			if (!is_bool($this->fileErrorDialog)){
				$return .= $this->fileErrorDialog;
			}
		}

		return $return;
	}

	/**
	  * Forms for visial configuration or demo
	  * @access public
	  * @param array $zoom
	  * @param array $zoomTmp
	  * @param int $zoomW Calculated width
	  * @param int $extPixGal Calculated width
	  * @return HTML-Output
	  **/
	
	function visualConf($zoom,$zoomTmp,$zoomW,$extPixGal){
		$autoSubmit = false; // 'this.form.submit()' or false
		
		$return = '';
		$return .= "<DIV style='clear: both; float: left; height:15px; line-height:1px;'>&nbsp;</DIV>";	
			
		// Options Ajax for Demo
		// Top Border
		
		$return .= "<DIV id='zoomDemoContainer' style='float: left;'>";
			/*
			if ($zoom['config']['cornerRadius']){
				$return .= "<DIV class='zoom-border-container' style='width:".($zoomW+$extPixGal)."px;'><div class='zoom-top-left'></div><div class='zoom-top-right'></div></DIV>";
			}
			*/
			
			$return .= "<DIV id='zoomAjaxDemoButton' class='zoomAjaxDemoButton' style='width:".($zoomW+$extPixGal)."px;'><div style='padding: 0px 5px;'><p style='margin-top:11px'>VISUAL CONFIGURATION FOR DEMO APPLICATION OR CMS BACKEND (OPTIONAL!)</p></div></DIV>";
			
			$return .= '<DIV id="zoomAjaxDemo" style="float: left; display: none; width:'.($zoomW+$extPixGal).'px; background-color:#000000;" >'; // onMouseOver="jQuery(function(){jQuery.noDemoHide=true;});"
					
				$arrayMotion=array(
					'swing',
					'jswing',
					'linear',
					'easeInQuad',
					'easeOutQuad',
					'easeInOutQuad',
					'easeInCubic',
					'easeOutCubic',
					'easeInOutCubic',
					'easeInQuart', 
					'easeOutQuart',
					'easeInOutQuart', 
					'easeInQuint',
					'easeOutQuint', 
					'easeInOutQuint', 
					'easeInSine', 
					'easeOutSine', 
					'easeInOutSine', 
					'easeInExpo',
					'easeOutExpo',
					'easeInOutExpo',
					'easeInCirc',
					'easeOutCirc',
					'easeInOutCirc', 
					'easeInElastic', 
					'easeOutElastic',
					'easeInOutElastic',
					'easeInBack',
					'easeOutBack',
					'easeInOutBack',
					'easeInBounce',
					'easeOutBounce', 
					'easeInOutBounce'
				);
				
											
				$arrayJpgQual=array(10,20,30,40,50,60,65,70,75,80,85,90,95,97,100);
				$arrayDigits=array(0,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
				$arrayZoomBy=array(20,25,30,35,40,45,50,60,65,70,75,100,125,150,175,200,250,300,400,500,750,1000,1500,2000);
				$arrayZoomClick=array(50,100,200,300,400,500,750,1000,1250,1500,2000,2500,3000,4000,5000);
				$arrayMoveBy=array(20,25,30,35,40,45,50,60,65,70,75,100,125,150,175,200,250);
				$arrayZoomMove=array(50,100,200,300,400,500,750,1000,1250,1500,2000,2500,3000);
				$arrayOpacity=array(0,0.5,1,1.5,2,2.5,3,4,5,6,7,8,9,9.5,10);
				$arrayBorderWidth=array(1,2,3,4,5);
				$arrayLoaderPos=array('Center', 'TopLeft', 'TopRight', 'BottomLeft', 'BottomRight');
				$arrayMapFract=array(10,15,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,70,80,90,100);
				$arrayBg=array('wallpaper1.jpg', 'wallpaper2.jpg');
					
				$return .= "<table cellspacing='0' cellpadding='0' style='margin: 10px 5px 5px 5px; display: block;'><tbody><tr><td style='width:49%' valign='top'>";

						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; color: #F4E10A">MOTION SETTINGS</DIV>';
						
						$return .= '<FORM id="aniForm" action="" onsubmit="return false;">';

						$return .= '<DIV class="zoomText">';
							$return .= "<input type='checkBox' id='motionSwitch' value='1' onClick=\"demoShowSwitch(); this.blur();\" checked> - Preview motion settings. Note: the preview will not perform serverside resizing";				
						$return .= '</DIV>';
						
						// Zoom In
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoClickRatio(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomBy, $zoom['config']['pZoom'], $opr=false, $add='%');
						$return .= '</select> - Click ZOOM IN</DIV>';
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoClickOutRatio(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomBy, $zoom['config']['pZoomOut'], $opr=false, $add='%');
						$return .= "</select> - Click ZOOM OUT</DIV>";
	
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMoveRatio(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayMoveBy, $zoom['config']['pMove'], $opr=false, $add='%');
						$return .= "</select> - Move buttons by</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoClickSpeed(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomClick, $zoom['config']['zoomSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Click/Plus ZOOM IN Speed</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoClickZoomOut(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomClick, $zoom['config']['zoomOutSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Right Click / Minus ZOOM OUT Speed</DIV>";						 
				
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMoveSpeed(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomMove, $zoom['config']['moveSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Sidewards (buttons) speed</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoRestoreSpeed(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomMove, $zoom['config']['restoreSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Restore speed</DIV>";	

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoTraverseSpeed(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomMove, $zoom['config']['traverseSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Traverse speed</DIV>";	
						
						// Motion functions
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionIn(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseIn'], $opr='ucfirst', $add=false);
						$return .= "</select> - ZOOM IN Motion</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionOut(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseOut'], $opr='ucfirst', $add=false);
						$return .= "</select> - ZOOM OUT Motion</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionMove(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseMove'], $opr='ucfirst', $add=false);
						$return .= "</select> - Sidewards Motion</DIV>";	
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionRestore(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseRestore'], $opr='ucfirst', $add=false);
						$return .= "</select> - Restore Motion</DIV>";												

						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionTraverse(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseTraverse'], $opr='ucfirst', $add=false);
						$return .= "</select> - Traverse Motion</DIV>";	
						
						// Image status loader
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">IMAGE LOADER</DIV>';
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoLoaderPos(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayLoaderPos, $zoom['config']['zoomLoaderPos'], $opr=false, $add=false);
						$return .= "</select> - Loader Position</DIV>";								


						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoLoaderTransp(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayDigits, ($zoom['config']['zoomLoaderTransp']*100), $opr=false, $add='%');
						$return .= "</select> - Loader Transparency</DIV>";
						/* Where is loader position ??? */

						$return .= "</FORM>"; // END aniForm
						
						// PHP
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">IMAGE QUALITY AND PHP</DIV>';
						
						$return .= '<FORM id="demoOptions" action="" onsubmit="return false;">';
						$return .= "<DIV style='display: none'><input type='hidden' name='submitO' value='1'></DIV>";
						
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<select name='demoQ' onChange=\"jQuery.optSubmit();this.blur();\" style='width:80px'>";
							$return .= $this->sOptions($arrayJpgQual, $zoom['config']['qual'], $opr=false, $add=false);
							$return .= "</select> - JPG Quality";							
						$return .= '</DIV>';


						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<input type='radio' name='demoO' value='gd' onClick=\"jQuery.optSubmit();this.blur();\"";
							if (!$zoom['config']['im']){$return .= ' checked';}
							$return .= "> - GD&nbsp;&nbsp;";
							$return .= "<input type='radio' name='demoO' onClick=\"jQuery.optSubmit();this.blur();\" value='im'";
							if ($zoom['config']['im']){$return .= ' checked';}
							$return .= "> - ImageMagick";								
						$return .= '</DIV>';

						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">CROPPING METHOD</DIV>';					
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<DIV>";
								$return .= "<input type='radio' name='demoP' value='1' onClick=\"jQuery.optSubmit();this.blur();\"";
								if (!$zoom['config']['gPyramid'] AND !$zoom['config']['pyrTiles']){$return .= " checked";}
								$return .= "> - Crop from Original (slow) OK < 5-7 MP";
							$return .= "</DIV>";
								
							$return .= "<DIV>";

								$return .= "<input type='radio' name='demoP' value='2' onClick=\"jQuery.optSubmit();this.blur();\"";
								if ($zoom['config']['gPyramid']){$return .= " checked";}
								$return .= "> - Image Pyramid (faster) OK < 11-15 MP";
							$return .= "</DIV>";
							
							$return .= "<DIV>";
								$return .= "<input type='radio' name='demoP' value='3' onClick=\"jQuery.optSubmit();this.blur();\"";
								if ($zoom['config']['pyrTiles']){$return .= " checked";}
								$return .= "> - Image Pyramid with Tiles (very fast) ";
							$return .= "</DIV>";									
						$return .= '</DIV>';

						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">WATERMARK</DIV>';
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<input type='checkBox' name='demoW' value='1' onClick=\"jQuery.optSubmit();this.blur();\"";
							if ($zoom['config']['watermark']){$return .= ' checked';}
							$return .= "> - Watermark PNG Image Demo";						
						$return .= '</DIV>';

						$return .= '<DIV class="zoomText">';
							$return .= "<input type='checkBox' name='demoT' value='1' onClick=\"jQuery.optSubmit();this.blur();\"";
							if ($zoom['config']['text']){$return .= ' checked';}
							$return .= "> - Watermark Text Demo";
						$return .= '</DIV>';
						
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<span class='zoomTextS'>You can set a bunch of other settings for watermarking</span>";
						$return .= '</DIV>';

						$return .= "</FORM>"; // end demoOptions
						
					$return .= "</td><td style='width:49%' valign='top'>";

						// Slector
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; color: #F4E10A">SELECTOR SPECIFIC SETTINGS</DIV>';
						
						$return .= '<FORM id="selForm" action="" onsubmit="return false;">';
						$return .= '<DIV class="zoomText" style="margin-bottom:3px">';
						$return .= '<span class="colHex">#</span><input type="text" class="txt" value="008000" id="demoColorArea">';
						$return .= " - Selector color</DIV>";	
	
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoSelOpacity(this.value);this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayOpacity, ($zoom['config']['zoomSelectionOpacity']*10), $opr=create_function('$a','return ($a*10);'), $add='%');
						$return .= "</select> - Selector opacity</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px">';
						$return .= '<span class="colHex">#</span><input type="text" class="txt" value="000000" id="demoColorOuter">';
						$return .= " - Outer color</DIV>";	
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoOuterOpacity(this.value);this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayOpacity, ($zoom['config']['zoomOuterOpacity']*10), $opr=create_function('$a','return ($a*10);'), $add='%');
						$return .= "</select> - Outer opacity</DIV>";

						$return .= '<DIV class="zoomText" style="margin-bottom:3px">';
						$return .= '<span class="colHex">#</span><input type="text" class="txt" value="ff0000" id="demoColorBorder">';
						$return .= " - Selector border color</DIV>";	
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoBorder(this.value);this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayBorderWidth, $zoom['config']['zoomBorderWidth'], $opr=false, $add='px');
						$return .= "</select> - Selector border thickness</DIV>";


						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoCropSpeed(this.value); this.blur();" style="width:80px">';
						$return .= $this->sOptions($arrayZoomMove, $zoom['config']['cropSpeed'], $opr=false, $add='ms');
						$return .= "</select> - Selector ZOOM IN speed</DIV>";		
										
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoMotionCrop(this.value);this.blur();" style="width:120px">';
						$return .= $this->sOptions($arrayMotion, $zoom['config']['zoomEaseCrop'], $opr='ucfirst', $add=false);
						$return .= "</select> - S. ZOOM IN Motion</DIV>";
						
						$return .= "</FORM>"; // END selForm
						
						// LAYOUT
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">LAYOUT</DIV>';

						$return .= '<DIV class="zoomText" style="margin-bottom:3px">';
						$return .= '<span class="colHex">#</span><input type="text" class="txt" value="3E3E3E" id="demoColorStage">';
						$return .= " - Stage color</DIV>";
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px">';
						$return .= '<span class="colHex">#</span><input type="text" class="txt" value="FFFFFF" id="demoBodyColor">';
						$return .= " - Body color</DIV>";
						
						$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select onChange="demoBodyBack(this.value); this.blur();" style="width:80px">';
						$return .= "<option value=''>None</option>";
						$return .= $this->sOptions($arrayBg, false, $opr='ucfirst', $add=false);
						$return .= "</select> - Body background</DIV>";
							
						$return .= '<FORM id="demoMix" action="'.$_SERVER['PHP_SELF'].'" style="background-color: #1A1A1A; padding: 5px; border: #FFFFFF 1px solid;">';
							
							$return .= "<DIV style='display:none'><input type='hidden' name='zoomID' value='".$_GET['zoomID']."'></DIV>";
							$return .= "<DIV style='display:none'><input type=\"hidden\" name=\"demoMix\" value=\"1\"></DIV>";
							
							$return .= "<div class='zoomText' style='margin-bottom: 5px; color: #F4E10A;'>[The following settings will reload page]</div>";
															
							// Resolution
							$return .= '<DIV class="zoomText"><select name="demoRes" onChange="'.$autoSubmit.'" style="width:80px">';
							$return .= $this->sOptions($zoom['config']['posRes'], $zoom['config']['picDim'], $opr=false, $add='px');
							$return .= "</select> - Demo Resolutions</DIV>";

							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
								$return .= "<span class='zoomTextS'>You can configure whatever resolution you want</span>";
							$return .= '</DIV>';
							
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">VERTICAL GALLERY</DIV>';
							
							// Vertical gallery switch
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
								$return .= "<input type='radio' name='demoGal' value='yes' onClick=\"".$autoSubmit." jQuery('#zoomDemoVertGal').css('display','block');\"";
								if ($zoom['config']['useGallery']){$return .= ' checked';}
								$return .= "> - Yes ";
								$return .= "<input type='radio' name='demoGal' value='no' onClick=\"".$autoSubmit." jQuery('#zoomDemoVertGal').css('display','none');\"";
								if (!$zoom['config']['useGallery']){$return .= ' checked';}
								$return .= "> - No veritval Gallery";		
								//$return .= "<input type='checkbox' id='demoGal' name='demoGal' value='yes' onClick=\"".$autoSubmit." subOpt('demoGal', 'zoomDemoVertGal');\"> - Use veritval Gallery";
							$return .= '</DIV>';	
							
							$return .= "<DIV id='zoomDemoVertGal' style='background-color: #3C3C3C; padding: 5px; display: ".($zoom['config']['useGallery'] ? 'block' : 'none')."'>";			
							
								// Vertical gallery columns
								$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoGalCol" onChange="'.$autoSubmit.'" style="width:80px">';
								$return .= $this->sOptions($zoom['config']['posColumns'], $zoom['config']['galleryLines'], $opr=false, $add=false);
								$return .= "</select> - Vertical Gallery Columns</DIV>";
								
								// Vertical gallery Resolution
								$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoGalRes" onChange="'.$autoSubmit.'" style="width:80px">';
								$return .= $this->sOptions($zoom['config']['galRes'], $zoom['config']['galleryPicDim'], $opr=false, $add='px');
								$return .= "</select> - Vertical Gallery Resolution</DIV>";
								
								// Vertical gallery Position

								$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoGalPos" onChange="'.$autoSubmit.'" style="width:80px">';
								$return .= $this->sOptions(array('left', 'right'), $zoom['config']['galleryPos'], $opr='ucfirst', $add=false);
								$return .= "</select> - Vertical Gallery Position</DIV>";								
							
							$return .= '</DIV>';
							
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">INLINE GALLERY</DIV>';
							
							// Gallery switch inline
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
								$return .= "<input type='radio' name='demoFullGal' value='yes' onClick=\"".$autoSubmit." jQuery('#zoomDemoInlineGal').css('display','block');\"";
								if ($zoom['config']['useFullGallery']){$return .= ' checked';}
								$return .= "> - Yes ";
								$return .= "<input type='radio' name='demoFullGal' value='no' onClick=\"".$autoSubmit." jQuery('#zoomDemoInlineGal').css('display','none');\"";
								if (!$zoom['config']['useFullGallery']){$return .= ' checked';}
								$return .= "> - No Inline Gallery";		
							$return .= '</DIV>';	
							
							$return .= "<DIV id='zoomDemoInlineGal' style='background-color: #3C3C3C; padding: 5px; display: ".($zoom['config']['useFullGallery'] ? 'block' : 'none')."'>";			
									
								$return .= '<DIV class="zoomText"><select name="demoFullGalRes" onChange="'.$autoSubmit.'" style="width:80px">';
								$return .= $this->sOptions($zoom['config']['galRes'], $zoom['config']['galleryFullPicDim'], $opr=false, $add='px');
								$return .= "</select> - Inline Gallery Resolution</DIV>";
								
								$return .= '<DIV class="zoomText" style="margin-top: 3px;">';								
									$return .= "<input type='radio' name='demoFullGalAuto' value='yes' onClick=\"".$autoSubmit."\"";
									if ($zoom['config']['galFullAutoStart']){$return .= ' checked';}
									$return .= "> - Yes ";
									$return .= "<input type='radio' name='demoFullGalAuto' value='no' onClick=\"".$autoSubmit."\"";
									if (!$zoom['config']['galFullAutoStart']){$return .= ' checked';}
									$return .= "> - No Inline Gallery Autostart";		
								$return .= '</DIV>';									
								
							
							$return .= '</DIV>';
							
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">HORIZONTAL GALLERY</DIV>';
							
							// Gallery switch Horizontal
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
								$return .= "<input type='radio' name='demoHorGal' value='yes' onClick=\"".$autoSubmit."jQuery('#zoomDemoHorGal').css('display','block');\"";
								if ($zoom['config']['useHorGallery']){$return .= ' checked';}
								$return .= "> - Yes ";
								$return .= "<input type='radio' name='demoHorGal' value='no' onClick=\"".$autoSubmit."jQuery('#zoomDemoHorGal').css('display','none');\"";
								if (!$zoom['config']['useHorGallery']){$return .= ' checked';}
								$return .= "> - No Horizontal Gallery";		
							$return .= '</DIV>';
							
							$return .= "<DIV id='zoomDemoHorGal' style='background-color: #3C3C3C; padding: 5px; display: ".($zoom['config']['useHorGallery'] ? 'block' : 'none')."'>";			
								// Hor gallery Position
								$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoGalHorPos" onChange="'.$autoSubmit.'" style="width:80px">';
								$return .= $this->sOptions(array('top1'=>'Top 1', 'top2'=>'Top 2', 'bottom1'=>'Bottom 1', 'bottom2'=>'Bottom 2'), $zoom['config']['galHorPosition'], $opr=false, $add=false);
								$return .= "</select> - Horizontal Gallery Position</DIV>";				
							$return .= '</DIV>';
							
							// Gallery Navi
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">GALLERY NAVI</DIV>';

							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
								$return .= "<input type='radio' name='demoGalNavi' value='yes' onClick=\"".$autoSubmit."\"";
								if ($zoom['config']['galleryNavi']){$return .= ' checked';}
								$return .= "> - Yes ";
								$return .= "<input type='radio' name='demoGalNavi' value='no' onClick=\"".$autoSubmit."\"";
								if (!$zoom['config']['galleryNavi']){$return .= ' checked';}
								$return .= "> - No Prev / Next buttons";
							$return .= '</DIV>';	

							$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">ZOOM MAP</DIV>';
							
							// Map show / hide
							$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
								$return .= "<input type='radio' name='demoMap' value='no' onClick=\"".$autoSubmit."jQuery('#zoomDemoMapProp').css('display','none');\"";
								if (!$zoom['config']['useMap']){$return .= ' checked';}
								$return .= "> - Hide ";
								$return .= "<input type='radio' name='demoMap' value='yes' onClick=\"".$autoSubmit."jQuery('#zoomDemoMapProp').css('display','block');\"";
								if ($zoom['config']['useMap']){$return .= ' checked';}
								$return .= "> - Show map";		
							$return .= '</DIV>';	
							
							$return .= "<DIV id='zoomDemoMapProp' style='background-color: #3C3C3C; padding: 5px; display: ".($zoom['config']['useMap'] ? 'block' : 'none')."'>";
							
								// Map Draggable
								$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
									$return .= "<input type='radio' name='demoMapDrag' value='no' onClick=\"".$autoSubmit."\"";
									if (!$zoom['config']['dragMap']){$return .= ' checked';}
									$return .= "> - No ";
									$return .= "<input type='radio' name='demoMapDrag' value='yes' onClick=\"".$autoSubmit."\"";
									if ($zoom['config']['dragMap']){$return .= ' checked';}
									$return .= "> - Map draggable";		
								$return .= '</DIV>';	
	
								// Map Autohide
								$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
									$return .= "<input type='radio' name='demoMapVis' value='no' onClick=\"".$autoSubmit."\"";
									if (!$zoom['config']['zoomMapVis']){$return .= ' checked';}
									$return .= "> - No ";
									$return .= "<input type='radio' name='demoMapVis' value='yes' onClick=\"".$autoSubmit."\"";
									if ($zoom['config']['zoomMapVis']){$return .= ' checked';}
									$return .= "> - Map visible on start";		
								$return .= '</DIV>';
								
								// Map Animate
								$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';								
									$return .= "<input type='radio' name='demoMapAnim' value='no' onClick=\"".$autoSubmit."\"";
									if (!$zoom['config']['zoomMapAnimate']){$return .= ' checked';}
									$return .= "> - No ";
									$return .= "<input type='radio' name='demoMapAnim' value='yes' onClick=\"".$autoSubmit."\"";
									if ($zoom['config']['zoomMapAnimate']){$return .= ' checked';}
									$return .= "> - Animate Map";		
								$return .= '</DIV>';
								
								// Map Size in persentage of area
								$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoMapSize" onChange="'.$autoSubmit.'" style="width:80px">';
								foreach ($arrayMapFract as $k){
									$return .= "<option value='$k'";
									if ($k/100 == $zoom['config']['mapFract']){$return .= " selected";}
									$return .= ">".($k)." %</option>";
								}
								$return .= "</select> - Map size</DIV>";		
								
							$return .= '</DIV>';
							
							// Navigation Position
							$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoNavPos" onChange="'.$autoSubmit.'" style="width:80px">';
							$return .= $this->sOptions(array('top', 'bottom'), $zoom['config']['naviPos'], $opr='ucfirst', $add=false);
							$return .= "</select> - Navigation Position</DIV>";			
							
							if (!$autoSubmit){
								$return .= '<DIV class="zoomText" style="margin-bottom:3px; text-align: right;">';
									$return .= '<input type="button" onClick="this.form.submit()" value="Submit">';
								$return .= "</DIV>";
							}
							
						
						$return .= "</FORM>";
		
						
						// Other
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px; margin-top: 7px; color: #F4E10A">OTHER</DIV>';
						$return .= '<FORM id="demoOther" action="" onsubmit="return false;">';
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<input type='checkbox' name='msInterp' value='1' onClick=\"demoIeInterp(this.checked); this.blur();\" ".($zoom['config']['msInterp'] ? ' checked' : '')."> - Bicubic interpolation (IE < 8)";						
						$return .= '</DIV>';		
						$return .= '<DIV class="zoomText" style="margin-bottom: 3px;">';
							$return .= "<input type='checkbox' name='demoPhysics' value='1' onClick=\"demoPhys(this.checked); this.blur();\" ".($zoom['config']['zoomDragPhysics'] ? ' checked' : '')."> - Smooth dragging (throw picture)";						
						$return .= '</DIV>';						
						$return .= "</FORM>";
						
						// DOCTYPE
						$return .= '<FORM id="demoDoctype" action="">';
							$serverPar = $this->zoomServerPar('arr','demoDoctype',false,$_SERVER['QUERY_STRING']);
							if (is_array($serverPar)){
								foreach ($serverPar as $k=>$v){
									$return .= "<DIV><input type=\"hidden\" name=\"$k\" value=\"$v\"></DIV>";
								}
							}

							$return .= '<DIV class="zoomText" style="margin-bottom:3px"><select name="demoDoctype" onChange="this.form.submit()" style="width:160px">';
							foreach ($this->doctype as $k => $v){
								$doc = array_keys($v);
								$return .= "<option value='$k'";
								if ($k == $zoom['config']['doctype']){$return .= " selected";}
								$return .= ">".($doc[0])."</option>";
							}
							$return .= "</select> - Doctype</DIV>";
						
						$return .= "</FORM>";
						
					$return .= "</td></tr></tbody></table>";
				
				// End zoomAjaxDemo
				$return .= "</DIV>";
			/*
			if ($zoom['config']['cornerRadius']){
				$return .= "<DIV class='zoom-border-container' style='width:".($zoomW+$extPixGal)."px;'><div class='zoom-bottom-left'></div><div class='zoom-bottom-right'></div></DIV>";
			}
			*/
			
		$return .= "<DIV style='clear: both; float: left; height:10px; line-height:1px;'>&nbsp;</DIV>";	
		
		// http pic navigation
		/*
		if ($zoom['config']['cornerRadius']){
			$return .= "<DIV class='zoom-border-container' style='width:".($zoomW+$extPixGal)."px;'><div class='zoom-top-left'></div><div class='zoom-top-right'></div></DIV>";
		}
		*/
		
		// Dropdown for folders
		$zoomTmp['folderSelect']="<FORM style=\"margin:0px; padding:0px\" method=\"GET\" action=\"".$_SERVER['PHP_SELF']."\">";
			$zoomTmp['zoomServerPar'] = $this->zoomServerPar('arr',array('zoomID','zoomDir'),false,$_SERVER['QUERY_STRING']);
			if (!empty($zoomTmp['zoomServerPar'])){
				foreach ($zoomTmp['zoomServerPar'] as $k=>$v){
					$zoomTmp['folderSelect'].="<DIV style='display: none'><input type=\"hidden\" name=\"$k\" value=\"$v\"></DIV>";
				}
			}
			$zoomTmp['folderSelect'].="<DIV><SELECT name=\"zoomDir\" onChange=\"this.form.submit()\" style=\"\">"; 
				foreach ($zoomTmp['folderArray'] as $k=>$v){
					$zoomTmp['folderSelect'].="<option value='$k'";
					if ($k==$_GET['zoomDir']){$zoomTmp['folderSelect'].=" selected";}
					$zoomTmp['folderSelect'].=">$k. ".ucfirst($v)."</option>";
				}
			$zoomTmp['folderSelect'].="</SELECT></DIV>";
		$zoomTmp['folderSelect'].="</FORM>";
		
		// Dropdown for images
		$zoomTmp['dropSelect']="<FORM style=\"margin:0px; padding:0px\" method=\"GET\" action=\"".$_SERVER['PHP_SELF']."\">";
			$zoomTmp['zoomServerPar'] = $this->zoomServerPar('arr',array('zoomID'),false,$_SERVER['QUERY_STRING']);
			if (!empty($zoomTmp['zoomServerPar'])){
				foreach ($zoomTmp['zoomServerPar'] as $k=>$v){
					$zoomTmp['dropSelect'].="<DIV style='display: none'><input type=\"hidden\" name=\"$k\" value=\"$v\"></DIV>";
				}
			}
	
			$zoomTmp['dropSelect'].="<DIV><SELECT name=\"zoomID\" onChange=\"this.form.submit()\" style=\"\" id=\"axZmComboExample\">"; 
				foreach ($zoom['config']['pic_list_array'] as $k=>$v){
					$zoomTmp['dropSelect'] .= "<option value='$k'";
					if ($k==$_GET['zoomID']){$zoomTmp['dropSelect'].=" selected";}
					$v = $k.'. '.str_replace('_',' ',ucfirst($this->getf('.',$v))).' &rarr; ';
					$v .= $zoom['config']['pic_list_data'][$k]['imgSize'][0].'x'.$zoom['config']['pic_list_data'][$k]['imgSize'][1].' PX &rarr; ';
					$v .= round((($zoom['config']['pic_list_data'][$k]['imgSize'][0]*$zoom['config']['pic_list_data'][$k]['imgSize'][1])/1000000),1).' MP &rarr; ';
					$v .= $this->zoomFileSmartSize($zoom['config']['pic_list_data'][$k]['fileSize'],1);
					
					$zoomTmp['dropSelect'].='>'.$v."</option>";
				}
			$zoomTmp['dropSelect'] .= "</SELECT></DIV>";

		$zoomTmp['dropSelect'] .= "</FORM>";
		
		
		$return.= "<DIV id='zoomPicSelect' class='zoomAjaxDemoButton' style='width:".($zoomW+$extPixGal)."px;'>";
			$return.= "<DIV style='clear: both; margin:10px 5px; text-align: right;'>";
			$return.= isset($zoomTmp['dropSelect']) ? "<DIV style='float: right'>".$zoomTmp['dropSelect']."</DIV>" : '';
			$return.= isset($zoomTmp['folderSelect']) ? "<DIV style='float: left'>".$zoomTmp['folderSelect']."</DIV>" : '';
			$return.= "</DIV>";
		$return.= "</DIV>";
		/*
		if ($zoom['config']['cornerRadius']){
			$return .= "<DIV class='zoom-border-container' style='width:".($zoomW+$extPixGal)."px;'><div class='zoom-bottom-left'></div><div class='zoom-bottom-right'></div></DIV>";
		}
		*/
			
		// End zoomDemoContainer
		$return .= "</DIV>";	
		return $return;
	}
	
	
	
}
?>