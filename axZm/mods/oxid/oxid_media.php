<?php 
/**
* Plugin: jQuery AJAX-ZOOM, Oxid PHP helper file: oxid_media.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/
 
?>

<style type="text/css" media="screen"> 
.axZmThumb{
	border: 1px solid #DDDDDD; 
	margin: 0; padding: 0; 
	text-align: center; 
	vertical-align: middle;
	line-height: 0px;
}
.axZmContainer{
	overflow: hidden; 
	border:1px solid #CCCCCC;
}

.axZmClickZoomMsg{
	position: absolute; 
	display: block;
	float: left; 
	left: 8px; 
	text-align: right;
	padding: 0; 
	margin: 0; 
}

.axZmThumbContainer{
	border-top: 1px solid #CCCCCC;
	margin: 0px 0px 10px 0px;
	padding: 0;
}

.axZmThumbs{
	position: absolute; 
	display: block;
	float: left; 
	left: 8px; 
	list-style: none outside none; 
	text-align: left;
	padding: 0; 
	margin: 0; 
}

.axZmThumbs li{
	float: left; 
	margin: 0px 5px 5px 0px;
	padding: 0px;
}
 
</style>

<?php 

$axZm = array();
$zoomData = array();
$axZm['oxConfig'] = oxConfig::getInstance();

// Detail image size (first image)
$axZm['detailSize'] = $axZm['oxConfig']->getConfigParam('aDetailImageSizes');
$axZm['detailSize'] = explode('*', $axZm['detailSize']['oxpic1']);
 
// Thumbnail size
$axZm['thumbSize'] = explode('*', $axZm['oxConfig']->getConfigParam('sIconsize'));

// Url to the shop
$axZm['sShopURL'] = $axZm['oxConfig']->getConfigParam('sShopURL');

// Transiotion speed between images
$axZm['transitionSpeed'] = 300;

// Show zoom icon
$axZm['zoomIcon'] = true;

// 'mouseover' or 'click' for thumbs. 
// if 'mouseover' $axZm['smallImagePreload'] should be set to true
// if 'mouseover' click on the thumb will open AJA-ZOOM too
$axZm['thumbSwitch'] = 'click';

// Text under the image or false
$axZm['zoomText'] = 'Click on above image to zoom'; 

// Adjust some css of the template with javascript
$axZm['adjTemplate'] = true;

// Remove standard gallery with javascript
$axZm['removeMorePics'] = true;

//$axZm['noPictures'] = $this->_tpl_vars['oView']->getActPicture()

// Get all images
$axZm['pictures'] = $this->_tpl_vars['oView']->getPictures();
$axZm['icons'] = $this->_tpl_vars['oView']->getIcons();

if (!empty($axZm['pictures'])){
	foreach ($axZm['pictures'] as $k=>$v){ 
		$axZm['masterImg'][$k] = str_replace($axZm['sShopURL'],'',$v);
		$axZm['imgSize'][$k] = getimagesize(getShopBasePath().$axZm['masterImg'][$k]);
		$axZm['masterImg'][$k] = str_replace('out/pictures','',$axZm['masterImg'][$k]);
		
		$zoomData[$k]['f'] = basename($axZm['masterImg'][$k]);
		$zoomData[$k]['p'] = dirname($axZm['masterImg'][$k]);
	}
	$zoomData = strtr(base64_encode(addslashes(gzcompress(serialize($zoomData),9))), '+/=', '-_,');
}else{
	$axZm['noPictures'] = true;
}
 

// Image title
$axZm['imgAlt'] = strip_tags($this->_tpl_vars['product']->oxarticles__oxtitle->value);
if ($this->_tpl_vars['oxarticles__oxvarselect']->value){
	$axZm['imgAlt'] .= ' '.$this->_tpl_vars['oxarticles__oxvarselect']->value;
}

// Preview image
echo '<div class="picture axZmContainer" style="width: '.($axZm['detailSize'][0]).'px; height: '.($axZm['detailSize'][1]).'px">';

	echo '<div id="axZm-product-image" style="position: absolute; height: '.$axZm['detailSize'][1].'px; width: '.$axZm['detailSize'][0].'px;">';
	echo '<a href="javascript: void(0)" id="axZm-product-link" style="margin: 0px; padding: 0px; position: absolute; z-index: 1; display: block; width: '.$axZm['detailSize'][0].'px; height: '.$axZm['detailSize'][1].'px;">';
	echo '<img id="axZm-img" src="'.$this->_tpl_vars['oView']->getActPicture().'" alt="'.$axZm['imgAlt'].'" title="" style="position: absolute;  margin: 0; padding: 0; z-index: 1; left: '.(round(($axZm['detailSize'][0]-$axZm['imgSize'][1][0])/2)).'px; top: '.(round(($axZm['detailSize'][1]-$axZm['imgSize'][1][1])/2)).'px;" />';
	echo '</a>';
	echo '</div>';

echo '</div>';

// Text under the preview image
if ($axZm['zoomText']){ 
	echo '<div class="axZmClickZoomMsg" style="top: '.($axZm['detailSize'][1]+10).'px;  width: '.($axZm['detailSize'][0]+2).'px;"><a onClick="javascript: void(0)" style="font-size: 7pt;" id="zoom_to_click_text">'.$axZm['zoomText'].'</a></div>';
	$axZm['javascript'] .= '
		jQuery(\'#zoom_to_click_text\').css(\'cursor\',\'pointer\').click(function(){
			jQuery(\'#axZm-product-link\').click();
		});
	';
}


if (count($axZm['pictures']) > 1){

	echo '<ul class="axZmThumbs" style="top: '.($axZm['detailSize'][1]+20+($axZm['zoomText'] ? 10 : 0)).'px; width: '.($axZm['detailSize'][0]+2).'px;">';
	for ($n=1; $n <= count($axZm['pictures']); $n++){
		// Gallery Images
		echo '<li>';
		echo '<table cellpadding="0" cellspacing="0" width="'.($axZm['thumbSize'][0]+2).'" height="'.($axZm['thumbSize'][1]+2).'"><tr><td class="axZmThumb">';
		echo '<a href="javascript: void(0)" on'.strtolower($axZm['thumbSwitch']).'="jQuery.fn.rollImage(\''.$axZm['pictures'][$n].'\', \''.strtr(base64_encode(addslashes(gzcompress(serialize($axZm['masterImg'][$n]),9))), '+/=', '-_,').'\', '.$axZm['transitionSpeed'].', '.(strtolower($axZm['thumbSwitch']) == 'mouseover' ? 'this' : 'null').'); return false;">';
		echo '<img src="'.$axZm['icons'][$n].'" alt="'.$axZm['imgAlt'].'" title="" style="margin: 0; '.(($axZm['imgSize'][$n][1] >= $axZm['imgSize'][$n][0]) ? ('height: '.$axZm['thumbSize'][1].'px; width: auto;') : ('width: '.$axZm['thumbSize'][0].'px; height: auto;') ).'" border="0" />';
		echo '</a>';
		echo '</td></tr></table>';
		echo '</li>';
	}
	echo '</ul>';
}

// Pass data to /axZm/mods/magento/magento_axZm.js
$axZm['javascript'] .= '
	jQuery.zoomData = \''.$zoomData.'\';
	jQuery.fn.zoomImage(\''.strtr(base64_encode(addslashes(gzcompress(serialize($axZm['masterImg'][1]),9))), '+/=', '-_,').'\');	
';

// Add enlarge icon over the small preview image with javascript
if ($axZm['zoomIcon']){
	$axZm['javascript'] .= '
		var iconLink = axZm_BaseUrl+\'/axZm/mods/oxid/zoom_icon.png\';
		var iconImage = new Image();
		jQuery(iconImage).load(function(){
			jQuery(this).attr(\'id\',\'zoom_to_click_icon\')
			.css({
				position: \'relative\', 
				zIndex: 3, 
				width: iconImage.width, 
				height: iconImage.height, 
				left: ('.($axZm['detailSize'][0]-5).'-iconImage.width), 
				top: ('.($axZm['detailSize'][1]-5).'-iconImage.height), 
				cursor: \'pointer\'
			})
			.click(function(){
				jQuery(\'#axZm-product-link\').click();
			}).appendTo(\'#axZm-product-image\');
		}).attr(\'src\', iconLink);
	';
}

// Output javascript
if ($axZm['javascript']){
	echo '
	<script type="text/javascript">
	jQuery(window).load(function(){
	jQuery.fn.adjustOxidTemplate('.($axZm['adjTemplate'] ? 'true' : 'false').', '.($axZm['removeMorePics'] ? 'true' : 'false').');
	'.$axZm['javascript'].'
	});</script>
	';
}

?>
