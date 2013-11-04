<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomConfigCustom.inc.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


#################################################################################################################
######### Following configurations are for the examples provided in "examples" folder.###########################
#################################################################################################################

/**
Each example in the download package as well as webshop plugin uses a special configuration options set. 
Default options in "/axZm/zoomConfig.inc.php" are overridden in "zoomConfigCustom.inc.php" which is included at the bottom of "zoomConfig.inc.php". 
This happens by passing an extra parameter "example=[some value]" to AJAX-ZOOM directly from examples or plugins over query string. 
To find out which "example" value is used see sourcecode of the implementation in question 
or inspect an ajax call to "/axZm/zoomLoad.php" with Firebug or an other developer tool. 
Thus your specific options set can be found in "zoomConfigCustom.inc.php" after elseif ($_GET['example'] == [some value]){. 

Please note that the value of example parameter passed over the query string to AJAX-ZOOM does not always correspond 
to the number of an example found in /examples folder of the download package.

Because AJAX-ZOOM is updated very frequently and its options base grows accordingly, 
the best practice is to copy options you need to change from "zoomConfig.inc.php" to "zoomConfigCustom.inc.php" 
after elseif ($_GET['example'] == [some value]). Ofcourse you can create your own [some value] in "zoomConfigCustom.inc.php". 
By keeping "zoomConfig.inc.php" as it is ($zoom['config']['licenceKey'] and $zoom['config']['licenceType'] can be copied as well) 
you will be able to update your customized implementation by simply overwriting all files except "zoomConfigCustom.inc.php" and custom css file.   
*/

if ($_GET['example'] == 1){
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1;
}

elseif ($_GET['example'] == 2){
	$zoom['config']['picDim'] = '720x480';
	$zoom['config']['galleryFullPicDim'] = "100x100";
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1; 
}

elseif ($_GET['example'] == 3){
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = true;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = true; 
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryLines'] = 3; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 
}

elseif ($_GET['example'] == 4){
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1;
}

elseif ($_GET['example'] == 5){
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['galFullAutoStart'] = true;
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 
}

elseif ($_GET['example'] == 6){
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = true;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = true; 
	$zoom['config']['galleryPicDim'] = '70x70'; 
	$zoom['config']['galleryLines'] = 3; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 
}

elseif ($_GET['example'] == 7){
	$zoom['config']['picDim'] = "480x320";
	$zoom['config']['naviPos'] = "top";
	$zoom['config']['naviFloat'] = "right";
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['useGallery'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1;
}

elseif ($_GET['example'] == 8){
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryFullPicDim'] = '75x60';
	$zoom['config']['galleryNavi'] = true; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomSlider'] = true;
}

elseif ($_GET['example'] == 9){
	$zoom['config']['picDim'] = '320x480';
	$zoom['config']['buttonSet'] = 'flat';
	$zoom['config']['help'] = false;
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviGroupSpace'] = 10;
	
	$zoom['config']['useGallery'] = false;
	$zoom['config']['galleryPicDim'] = '70x70';
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['galleryFullPicDim'] = '70x70';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['naviMinPadding'] = 0;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapButton'] = false;
	$zoom['config']['fullScreenNaviButton'] = false;
}

elseif ($_GET['example'] == 10){
	$zoom['config']['visualConf'] = true;
	$zoom['config']['zoomSlider'] = true;
	$zoom['config']['pssBar'] = true;
	$zoom['config']['cropNoObj'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	
	// do not load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = false;
	$zoom['config']['pyrQual'] = 100;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles/'; //string
	$zoom['config']['zoomDragSpeed'] = 500; 
	$zoom['config']['zoomDragAjax'] = 1000;
}

elseif ($_GET['example'] == 11){
	$zoom['config']['picDim'] = '420x280'; 
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['galHorPosition'] = 'bottom2';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['galleryNavi'] = false; 
	
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['naviBigZoom'] = false;
	$zoom['config']['zoomSlider'] = true;
}

elseif ($_GET['example'] == 12){
	$zoom['config']['picDim'] = '420x280';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['galleryPicDim'] = '70x70';	
	$zoom['config']['galleryNavi'] = false; 
	
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false; 
	$zoom['config']['naviBigZoom'] = false;
}

elseif ($_GET['example'] == 13){
	$zoom['config']['picDim'] = '420x280';
	$zoom['config']['galHorPosition'] = 'bottom2';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['scrollPane'] = false;
	$zoom['config']['galleryNavi'] = false;

	$zoom['config']['dragMap'] = false; 
	
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = 'body';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['naviBigZoom'] = false;
	$zoom['config']['zoomLoaderEnable'] = false;
}

elseif ($_GET['example'] == 14){
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryFullPicDim'] = '75x60';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
}

elseif ($_GET['example'] == 15){
	$zoom['config']['picDim'] = "480x360";
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['mapPos'] = 'bottomRight';
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['naviFloat'] = 'left';
	$zoom['config']['galleryLines'] = 4;
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryNavi'] = true; 
	$zoom['config']['galleryNaviPos'] = 'navi'; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1;	
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = false;
	$zoom['config']['autoZoom']['enabled'] = true;  
	$zoom['config']['autoZoom']['onlyFirst'] = true;
	$zoom['config']['autoZoom']['speed'] = 500;  
	$zoom['config']['autoZoom']['motion'] = 'easeOutQuad';  
	$zoom['config']['autoZoom']['pZoom'] = 'fill';  
}

elseif ($_GET['example'] == 16){
	$zoom['config']['picDim'] = "480x320";
	$zoom['config']['useGallery'] = false; 
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;  
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['dragMap'] = false;
}

// 3D Zoom & Spin
elseif ($_GET['example'] == 17){
	$zoom['config']['picDim'] = "600x400";
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;
	$zoom['config']['spinMod'] = true;
	$zoom['config']['galleryNoThumbs'] = true;
	$zoom['config']['firstMod'] = 'spin';
	$zoom['config']['zoomSlider'] = true;
}

elseif ($_GET['example'] == 18){
	$zoom['config']['picDim'] = '400x600';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 0.5;
	$zoom['config']['zoomSlider'] = true;
	$zoom['config']['mapSelSmoothDrag'] = false;
	$zoom['config']['galleryFullPicDim'] = '90x90';
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = false;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapBorder']['top'] = 1; 
	$zoom['config']['mapBorder']['right'] = 1; 
	$zoom['config']['mapBorder']['bottom'] = 1; 
	$zoom['config']['mapBorder']['left'] = 1; 
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = true;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['galleryFadeInSize'] = 1;
	$zoom['config']['galleryFadeInSpeed'] = 300;
	$zoom['config']['galleryFadeInOpacity'] = 0.5;
	$zoom['config']['galleryFadeInAnm'] = 'Right';
	$zoom['config']['fullScreenNaviBar'] = false;
}

elseif ($_GET['example'] == 19){
	$zoom['config']['picDim'] = '360x540';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 1;
	$zoom['config']['zoomSlider'] = 'pan';
	$zoom['config']['autoZoom']['enabled'] = true; 
	$zoom['config']['autoZoom']['onlyFirst'] = true; 
	$zoom['config']['autoZoom']['speed'] = 500; 
	$zoom['config']['autoZoom']['motion'] = 'easeOutQuad'; 
	$zoom['config']['autoZoom']['pZoom'] = '100%'; 
	$zoom['config']['mapSelSmoothDrag'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['useGallery'] = false;	
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = false;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapBorder']['top'] = 1; 
	$zoom['config']['mapBorder']['right'] = 1; 
	$zoom['config']['mapBorder']['bottom'] = 1; 
	$zoom['config']['mapBorder']['left'] = 1; 
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = true;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['fullScreenNaviBar'] = false;
}

elseif ($_GET['example'] == 20){
	$zoom['config']['picDim'] = '600x400';  
	$zoom['config']['displayNavi'] = true; 
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['zoomSlider'] = true;
}

// Animation
elseif ($_GET['example'] == 21){
	$zoom['config']['picDim'] = "600x400";
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['galleryNoThumbs'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['galleryHorPicDim'] = '60x60';
	$zoom['config']['galHorHeight'] = 70;
	$zoom['config']['galHorCssPadding'] = 0;
	$zoom['config']['galHorCssDescrHeight'] = 0;
	$zoom['config']['galHorCssDescrPadding'] = 0;
	$zoom['config']['galHorScrollToCurrent'] = false;
	$zoom['config']['galHorInnerCorner'] = false;
	$zoom['config']['galHorArrows'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;
	$zoom['config']['spinMod'] = true;
	$zoom['config']['firstMod'] = 'spin';
	$zoom['config']['spinSlider'] = true;
	$zoom['config']['spinSliderPosition'] = 'naviTop'; 
	$zoom['config']['spinSliderWidth'] = false; 
	$zoom['config']['spinSliderContainerPadding'] = array('top'=>5, 'right'=>20, 'bottom'=>5, 'left' =>10);
	$zoom['config']['spinSliderContainerHeight'] = 42;
	$zoom['config']['spinDemoTime'] = 4000; 
	$zoom['config']['naviSpinButSwitch'] = false;
	$zoom['config']['naviTopMargin'] = 0; 
	$zoom['config']['cueFrames'] = false;  
	$zoom['config']['spinSliderPlayButton'] = true;  
	$zoom['config']['spinSliderTopMargin'] = 10; 
	$zoom['config']['spinToMotion'] = 'easeOutQuad';  
}

// Mouse hover zoom
elseif ($_GET['example'] == 22){
	$zoom['config']['picDim'] = '530x500';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 0.7;
	$zoom['config']['restoreSpeed'] = 1;
	$zoom['config']['zoomMapSwitchSpeed'] = 1; 
	$zoom['config']['galleryInnerFade'] = false;
	$zoom['config']['galleryFadeInSpeed'] = 1;
	$zoom['config']['galleryFadeOutSpeed'] = 1;
	$zoom['config']['pZoom'] = 25;
	$zoom['config']['pZoomOut'] = 25;
	$zoom['config']['mapSelSmoothDrag'] = false;
	$zoom['config']['autoZoom']['enabled'] = true; 
	$zoom['config']['autoZoom']['onlyFirst'] = false; 
	$zoom['config']['autoZoom']['speed'] = 1; 
	$zoom['config']['autoZoom']['motion'] = 'swing'; 
	$zoom['config']['autoZoom']['pZoom'] = 'max'; 
	$zoom['config']['galleryNoThumbs'] = false;
	$zoom['config']['galleryFullPicDim'] = "62x62"; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = false;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapBorder']['top'] = 0; 
	$zoom['config']['mapBorder']['right'] = 0; 
	$zoom['config']['mapBorder']['bottom'] = 0; 
	$zoom['config']['mapBorder']['left'] = 0; 
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = true;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['mapWidth'] = 250;
	$zoom['config']['mapHeight'] = false;
	$zoom['config']['allowDynamicThumbs'] = false;
	$zoom['config']['fullScreenNaviBar'] = false;
	$zoom['config']['mapMouseOver'] = true;
	$zoom['config']['gallerySlideNaviOnlyFullScreen'] = true;
}

elseif ($_GET['example'] == 23){
	$zoom['config']['picDim'] = '600x400';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapSelSmoothDrag'] = false;	
	$zoom['config']['galHorMarginBottom'] = 0;
	$zoom['config']['galHorFlow'] = true;
	$zoom['config']['galHorArrows'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galleryFullPicDim'] = '120x120';
	$zoom['config']['galFullAutoStart'] = true;
	$zoom['config']['galleryPicDim'] = '120x120'; 
	$zoom['config']['galleryScrollbarWidth'] = 10;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = false;
	$zoom['config']['dragMap'] = false; 	
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['galleryFadeInSize'] = 1;
	$zoom['config']['zoomFadeIn'] = 1000;
	$zoom['config']['galleryFadeInSpeed'] = 1000;
	$zoom['config']['galleryFadeInOpacity'] = 0.0;
	$zoom['config']['galleryFadeInAnm'] = 'Center';
	$zoom['config']['fullScreenNaviBar'] = false;
	$zoom['config']['fullScreenMapFract'] = false;
	$zoom['config']['fullScreenMapWidth'] = false;
	$zoom['config']['fullScreenMapHeight'] = 120;
	$zoom['config']['galleryNavi'] = false;
	
	$zoom['config']['fullScreenNaviBar'] = false;
	
	$zoom['config']['mNavi']['enabled'] = true;
	$zoom['config']['mNavi']['offsetVertFS'] = 10;
	$zoom['config']['mNavi']['fullScreenShow'] = true;
	$zoom['config']['mNavi']['gravity'] = 'bottom';
	$zoom['config']['mNavi']['mouseOver'] = true;
	$zoom['config']['mNavi']['order'] = array(
		'mZoomIn' => 5, 
		'mZoomOut' => 0
	);	
}

elseif ($_GET['example'] == 24){
	$zoom['config']['picDim'] = '600x600';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['mapSelSmoothDrag'] = false;	
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['galleryNavi'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['fullScreenNaviButton'] = false;
	$zoom['config']['fullScreenCornerButton'] = false;
	$zoom['config']['fullScreenExitText'] = false;
}

elseif ($_GET['example'] == 25){
	$zoom['config']['picDim'] = '520x412'; 
	$zoom['config']['galHorPosition'] = 'bottom2';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['galleryPicDim'] = '70x70';
	$zoom['config']['galleryLines'] = 2;
 	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['galleryNaviPos'] = 'navi';
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['naviBigZoom'] = true;
	$zoom['config']['galleryFadeInAnm'] = 'Right';
	$zoom['config']['galleryFadeInSize'] = 1; 	 
	$zoom['config']['zoomSliderPosition'] = 'topLeft';
	$zoom['config']['zoomSliderMarginV'] = 150;
	$zoom['config']['fullScreenGallery'] = false;
	$zoom['config']['gallerySlideNaviOnlyFullScreen'] = true;
}

elseif ($_GET['example'] == 'spinIpad'){
	$zoom['config']['picDim'] = "720x480";
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;
	$zoom['config']['spinMod'] = true;
	$zoom['config']['galleryNoThumbs'] = true;
	$zoom['config']['firstMod'] = 'spin';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['spinSlider'] = false;
	$zoom['config']['fullScreenNaviBar'] = false;
	
	$zoom['config']['mNavi']['enabled'] = true;
	$zoom['config']['mNavi']['ellementRows'] = 1;
	$zoom['config']['mNavi']['offsetVertFS'] = 10;
	$zoom['config']['mNavi']['fullScreenShow'] = true;
	$zoom['config']['mNavi']['gravity'] = 'bottomLeft';
	$zoom['config']['mNavi']['mouseOver'] = false;
	$zoom['config']['mNavi']['cssClass'] = '';
	$zoom['config']['mNavi']['order'] = array(
		'mPan' => 5, 
		'mSpin' => 0
	);
}

elseif ($_GET['example'] == 'spinAnd2D'){
	$zoom['config']['picDim'] = "600x400";
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['mapBorder']['top'] = 1; 
	$zoom['config']['mapBorder']['right'] = 1; 
	$zoom['config']['mapBorder']['bottom'] = 1; 
	$zoom['config']['mapBorder']['left'] = 1; 
	$zoom['config']['mapFract'] = 0.20;	
	$zoom['config']['zoomShowButtonDescr'] = false;	
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;
	$zoom['config']['galleryNoThumbs'] = true;
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['fullScreenNaviBar'] = false;

	$zoom['config']['mNavi']['enabled'] = true;
	$zoom['config']['mNavi']['ellementRows'] = 1;
	$zoom['config']['mNavi']['offsetVertFS'] = 10;
	$zoom['config']['mNavi']['fullScreenShow'] = true;
	$zoom['config']['mNavi']['gravity'] = 'bottomLeft';
	$zoom['config']['mNavi']['mouseOver'] = false;
	$zoom['config']['mNavi']['cssClass'] = '';
	$zoom['config']['mNavi']['order'] = array(
		'mPan' => 5, 
		'mSpin' => 0
	);
	
	// 360 settings
	if (isset($_GET['image360'])){
		$zoom['config']['spinMod'] = true;
		$zoom['config']['firstMod'] = 'spin';
		$zoom['config']['spinSlider'] = false;
		
	}else{
		$zoom['config']['mNavi']['order'] = array(
			'mReset' => 0
		);
	}
}

elseif ($_GET['example'] == 'imageSlider'){
	$zoom['config']['picDim'] = '800x400';
	$zoom['config']['useGallery'] = false;
	
	$zoom['config']['mNavi']['enabled'] = false;
	
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryFullPicDim'] = "170x100";
	$zoom['config']['galFullCssMargin'] = 7;
	$zoom['config']['galFullCssPadding'] = 7;
	if (isset($_GET['autoPlay']) && $_GET['autoPlay'] == 'true'){
		$zoom['config']['galleryAutoPlay'] = true;
	}
	
	if (isset($_GET['playPauseInterval']) && intval($_GET['playPauseInterval'])){
		$zoom['config']['galleryPlayInterval'] = intval($_GET['playPauseInterval']);
	}
	$zoom['config']['fullScreenEnable'] = false;
	
	if (isset($_GET['fullScreen']) && $_GET['fullScreen'] != 'false'){
		$zoom['config']['fullScreenEnable'] = true;
		$zoom['config']['fullScreenCornerButton'] = true;
		$zoom['config']['fullScreenCornerButtonPos'] = $_GET['fullScreen']; 
	}
	
	if (isset($_GET['openAsFullscreen']) && $_GET['openAsFullscreen'] == 'true'){
		$zoom['config']['fullScreenEnable'] = true;
		$zoom['config']['fullScreenExitText'] = false;
		$zoom['config']['fullScreenCornerButton'] = false;
	}
	
	$zoom['config']['help'] = false;
	$zoom['config']['zoomLoaderEnable'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['galleryFadeInAnm'] = 'SwipeHorz';
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['fullScreenNaviBar'] = false;
	
	// zoomSlider
	$zoom['config']['zoomSliderContainerPadding'] = 10;
	if (isset($_GET['zoomSliderPos']) && $_GET['zoomSliderPos'] != 'false'){
		$zoom['config']['zoomSlider'] = true;
	}
	
	// Prev/Next arrows
	$zoom['config']['gallerySlideNavi'] = isset($_GET['prevNextArrows']) && $_GET['prevNextArrows'] == 'true' ? true : false;
	$zoom['config']['gallerySlideNaviMouseOver'] = isset($_GET['prevNextArrowsAutoHide']) && $_GET['prevNextArrowsAutoHide'] == 'true' ? true : false;
	$zoom['config']['gallerySlideNaviOnlyFullScreen'] = false;
}

elseif ($_GET['example'] == 'mouseOverExtension'){
	$zoom['config']['picDim'] = '600x800';
	$zoom['config']['galleryFullPicDim'] = "100x100"; 
	$zoom['config']['useGallery'] = false;
	$zoom['config']['fullScreenVertGallery'] = true;
	$zoom['config']['galleryNavi'] = false;
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1; 
	
	$zoom['config']['fullScreenNaviBar'] = false;

	$zoom['config']['mNavi']['enabled'] = true;
	$zoom['config']['mNavi']['ellementRows'] = 1;
	$zoom['config']['mNavi']['offsetVertFS'] = 10;
	$zoom['config']['mNavi']['fullScreenShow'] = true;
	$zoom['config']['mNavi']['gravity'] = 'bottomLeft';
	$zoom['config']['mNavi']['mouseOver'] = false;
	$zoom['config']['mNavi']['cssClassFS'] = '';
	
	$zoom['config']['mNavi']['order'] = array(
		'mPan' => 5, 
		'mCrop' => 10, 
		'mZoomIn' => 5, 
		'mZoomOut' => 0,
		'mReset' => 25
	);
	
	$zoom['config']['mNavi']['customPos'] = array(
		'mReset' => array(
			'css' => array(
				'bottom' => 10, 
				'right' => 10, 
				'position' => 'absolute', 
				'zIndex' => 123
			), 
			'parentID' => '', 
			'mouseOver' => true
		)
	);

}

elseif ($_GET['example'] == 'hotSpotEdit'){
	$zoom['config']['picDim'] = "720x480";
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['useGallery'] = false;
	
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;

	if (!isset($_GET['zoomData'])){
		$zoom['config']['spinMod'] = true;
		$zoom['config']['firstMod'] = 'spin';
	}
	
	$zoom['config']['galleryNoThumbs'] = true;
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['spinSlider'] = false;
	$zoom['config']['fullScreenNaviBar'] = false;
	$zoom['config']['mNavi']['enabled'] = true;
	$zoom['config']['mNavi']['gravity'] = 'bottom';
	$zoom['config']['mNavi']['alignment'] = 'horz';
	$zoom['config']['mNavi']['padding'] = 5;
	$zoom['config']['mNavi']['offsetHorz'] = 0;
	$zoom['config']['mNavi']['offsetVert'] = -10;
	$zoom['config']['mNavi']['hover'] = true;
	$zoom['config']['mNavi']['parentID'] = 'testCustomNavi';
	$zoom['config']['mNavi']['order'] = array(
		'mPan' => 7, 
		'mSpin' => 7, 
		'mCrop' => 25, 
		'mZoomOut' => 7, 
		'mZoomIn' => 25, 
		'mReset' => 25, 
		'mSpinPlay' => 7, 
		'mPrev' => 7, 
		'mNext' => 25, 
		'mHotspots' => 7, 
		'mMap' => 0
	);
}

#################################################################################################################
##################################### CMS & WEBSHOP MODULES / PLUGINS ###########################################
#################################################################################################################


// Magento implementation, see mods/magento/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'magento'){
	$zoom['config']['picDim'] = "780x450"; 
	$zoom['config']['jsUiAll'] = true;
	$zoom['config']['pic'] = $zoom['config']['installPath'].'/media/catalog/product/';
    $zoom['config']['thumbs'] = $zoom['config']['installPath'].'/axZm/pic/zoomthumb/';
	$zoom['config']['gallery'] = $zoom['config']['installPath'].'/axZm/pic/zoomgallery/';
    $zoom['config']['temp'] = $zoom['config']['installPath'].'/axZm/pic/temp/';
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/axZm/pic/zoomtiles/';
	$zoom['config']['zoomLoadFile'] = $zoom['config']['installPath'].'/axZm/zoomLoad.php';
	$zoom['config']['zoomLoadSess'] = $zoom['config']['installPath'].'/axZm/zoomSess.php';
	$zoom['config']['icon'] = $zoom['config']['installPath'].'/axZm/icons/'; 
	$zoom['config']['js'] = $zoom['config']['installPath'].'/axZm/'; 
	$zoom['config']['fontPath'] = $zoom['config']['installPath'].'/axZm/fonts/';
	$zoom['config']['tempCache'] = $zoom['config']['installPath']."/axZm/cache/";
	$zoom['config']['mapPath'] = $zoom['config']['installPath'].'/axZm/pic/zoommap/';
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['mapSelSmoothDrag'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
	
	// Settings for 360 spin
	if (isset($_GET['3dDir'])){
		$zoom['config']['picDim'] = "740x430"; 
		$zoom['config']['galFullButton'] = false;
		$zoom['config']['naviFloat'] = 'right';
		$zoom['config']['galleryNavi'] = false; 
		$zoom['config']['useGallery'] = false;
		$zoom['config']['helpMargin'] = 0;
		$zoom['config']['help'] = false;
		$zoom['config']['mapButton'] = true;
		$zoom['config']['spinMod'] = true;
		$zoom['config']['galleryNoThumbs'] = true;
		$zoom['config']['firstMod'] = 'spin';
		$zoom['config']['zoomSlider'] = true;		
	} 
	
	// Width and Height if embedded, it is set in media.phtml
	if (isset($_GET['embedWidth']) && isset($_GET['embedHeight'])){
		$_GET['embedWidth'] = intval($_GET['embedWidth']);
		$_GET['embedHeight'] = intval($_GET['embedHeight']);
		$zoom['config']['picDim'] = ($_GET['embedWidth']-$zoom['config']['innerMargin']*2).'x'.($_GET['embedHeight']-$zoom['config']['innerMargin']*2);
		$zoom['config']['useGallery'] = false;
		
		$zoom['config']['zoomMapSwitchSpeed'] = 1; 
		$zoom['config']['galleryInnerFade'] = true;
		$zoom['config']['galleryFadeInSize'] = 1;
		$zoom['config']['galleryFadeInSpeed'] = 1;
		$zoom['config']['galleryFadeOutSpeed'] = 1;
	}
	
	// Different display modi, it is set in media.phtml
	if (isset($_GET['displayModus'])){
		if ($_GET['displayModus'] == 'left'){
			if (!isset($_GET['3dDir'])){
				$zoom['config']['naviPanButSwitch'] = false; 
			}
			$zoom['config']['zoomLogInfoDisabled'] = true;
			$zoom['config']['naviPanBut'] = false;
			$zoom['config']['naviBigZoom'] = true;
			$zoom['config']['naviCropButSwitch'] = false; 
			
			//$zoom['config']['galleryNavi'] = true; 
			$zoom['config']['galleryPlayButton'] = false;
		} 
		elseif ($_GET['displayModus'] == 'flyout'){
			
			if (isset($_GET['mapWidth']) && isset($_GET['mapHeight'])){
				$zoom['config']['mapWidth'] = intval($_GET['mapWidth']);
				$zoom['config']['mapHeight'] = intval($_GET['mapHeight']);
	 		}
			
			if (isset($_GET['flyoutWidth']) && isset($_GET['flyoutHeight'])){
				$zoom['config']['picDim'] = intval($_GET['flyoutWidth']).'x'.intval($_GET['flyoutHeight']);
			}
			
			$zoom['config']['useGallery'] = false;
			$zoom['config']['displayNavi'] = false;
			$zoom['config']['fullScreenNaviBar'] = true;
			$zoom['config']['naviPanBut'] = false;
			$zoom['config']['naviPanButSwitch'] = false;
			$zoom['config']['naviCropButSwitch'] = false; 
			$zoom['config']['mapButton'] = false;
			$zoom['config']['mapParent'] = 'mapContainer';
			$zoom['config']['mapFract'] = 0.7;
			$zoom['config']['restoreSpeed'] = 1;
			$zoom['config']['zoomMapSwitchSpeed'] = 1; 
			$zoom['config']['galleryInnerFade'] = false;
			$zoom['config']['galleryFadeInSpeed'] = 1;
			$zoom['config']['galleryFadeOutSpeed'] = 1;
			$zoom['config']['pZoom'] = 25;
			$zoom['config']['pZoomOut'] = 25;
			$zoom['config']['mapSelSmoothDrag'] = false;
			$zoom['config']['autoZoom']['enabled'] = true; 
			$zoom['config']['autoZoom']['onlyFirst'] = false; 
			$zoom['config']['autoZoom']['speed'] = 1; 
			$zoom['config']['autoZoom']['motion'] = 'swing'; 
			$zoom['config']['autoZoom']['pZoom'] = 'max'; 
			$zoom['config']['zoomMapRest'] = false;
			$zoom['config']['zoomMapContainment'] = false;
			$zoom['config']['dragMap'] = false;
			$zoom['config']['mapBorder']['top'] = 0; 
			$zoom['config']['mapBorder']['right'] = 0; 
			$zoom['config']['mapBorder']['bottom'] = 0; 
			$zoom['config']['mapBorder']['left'] = 0; 
			$zoom['config']['zoomMapAnimate'] = false;
			$zoom['config']['zoomMapVis'] = true;
			$zoom['config']['cornerRadius'] = 0;
			$zoom['config']['innerMargin'] = 0;
			$zoom['config']['mapMouseOver'] = true;
		}
	}
	
	// Remove toolbar etc for "TouchStyle" option, it is set in media.phtml
	if (isset($_GET['zoomTouchStyle']) && $_GET['zoomTouchStyle'] == 'yes'){
		$zoom['config']['cornerRadius'] = 0;
		$zoom['config']['innerMargin'] = 0;
		
		$zoom['config']['spinSlider'] = false;
		$zoom['config']['displayNavi'] = false;
		$zoom['config']['fullScreenNaviBar'] = false;
		
		if (isset($_GET['displayModus'])){
			if ($_GET['displayModus'] == 'embedded' || $_GET['displayModus'] == 'left'){
				$zoom['config']['galleryNavi'] = true;
			}else{
				$zoom['config']['galleryNavi'] = false;
			}
		}else{
			$zoom['config']['galleryNavi'] = false;
		}
		
		
		$zoom['config']['mapBorder']['top'] = 1; 
		$zoom['config']['mapBorder']['right'] = 1; 
		$zoom['config']['mapBorder']['bottom'] = 1; 
		$zoom['config']['mapBorder']['left'] = 1; 
		
		
		$zoom['config']['mNavi']['enabled'] = true;
		$zoom['config']['mNavi']['ellementRows'] = 1;
		$zoom['config']['mNavi']['offsetVertFS'] = 10;
		$zoom['config']['mNavi']['fullScreenShow'] = true;
		$zoom['config']['mNavi']['gravity'] = 'bottomLeft';
		$zoom['config']['mNavi']['mouseOver'] = false;
		$zoom['config']['mNavi']['cssClass'] = '';
		$zoom['config']['mNavi']['cssClassFS'] = '';
		
		if (!isset($_GET['3dDir'])){
			$zoom['config']['mNavi']['order'] = array(
				'mReset' => 5, 
				'mZoomIn' => 5, 
				'mZoomOut' => 0
			);		
		}else{
			$zoom['config']['mNavi']['order'] = array(
				'mSpin' => 5, 
				'mPan' => 10
			);
		}

	}
}

// xt:Commerce implementation, see mods/xtc/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'xtc'){
	$zoom['config']['picDim'] = "800x450";
	$zoom['config']['cTimeCompare'] = true;	 
	$zoom['config']['jsUiAll'] = true;
	$zoom['config']['pic'] = $zoom['config']['installPath'];
    $zoom['config']['thumbs'] = $zoom['config']['installPath'].'/axZm/pic/zoomthumb/';
	$zoom['config']['gallery'] = $zoom['config']['installPath'].'/axZm/pic/zoomgallery/';
    $zoom['config']['temp'] = $zoom['config']['installPath'].'/axZm/pic/temp/';
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/axZm/pic/zoomtiles/';
	$zoom['config']['zoomLoadFile'] = $zoom['config']['installPath'].'/axZm/zoomLoad.php';
	$zoom['config']['zoomLoadSess'] = $zoom['config']['installPath'].'/axZm/zoomSess.php';
	$zoom['config']['icon'] = $zoom['config']['installPath'].'/axZm/icons/'; 
	$zoom['config']['js'] = $zoom['config']['installPath'].'/axZm/'; 
	$zoom['config']['fontPath'] = $zoom['config']['installPath'].'/axZm/fonts/'; 
	$zoom['config']['tempCache'] = $zoom['config']['installPath']."/axZm/cache/";
	$zoom['config']['mapPath'] = $zoom['config']['installPath'].'/axZm/pic/zoommap/';
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
	
	// Settings for 360 spin
	if ($_GET['3dDir']){
		$zoom['config']['picDim'] = "760x430"; 
		$zoom['config']['galFullButton'] = false;
		$zoom['config']['naviFloat'] = 'right';
		$zoom['config']['galleryNavi'] = false; 
		$zoom['config']['useGallery'] = false;
		$zoom['config']['helpMargin'] = 0;
		$zoom['config']['help'] = false;
		$zoom['config']['mapButton'] = true;
		$zoom['config']['spinMod'] = true;
		$zoom['config']['galleryNoThumbs'] = true;
		$zoom['config']['firstMod'] = 'spin';
		$zoom['config']['zoomSlider'] = true;
	} 
}
// Oxid implementation, see mods/oxid/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'oxid'){
 	$zoom['config']['picDim'] = "800x450";
	$zoom['config']['jsUiAll'] = true;
	$zoom['config']['cTimeCompare'] = true;
	$zoom['config']['pic'] = $zoom['config']['installPath'].'/out/pictures/master/';
    $zoom['config']['thumbs'] = $zoom['config']['installPath'].'/axZm/pic/zoomthumb/';
	$zoom['config']['gallery'] = $zoom['config']['installPath'].'/axZm/pic/zoomgallery/';
    $zoom['config']['temp'] = $zoom['config']['installPath'].'/axZm/pic/temp/';
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/axZm/pic/zoomtiles/';
	$zoom['config']['zoomLoadFile'] = $zoom['config']['installPath'].'/axZm/zoomLoad.php';
	$zoom['config']['zoomLoadSess'] = $zoom['config']['installPath'].'/axZm/zoomSess.php';
	$zoom['config']['icon'] = $zoom['config']['installPath'].'/axZm/icons/'; 
	$zoom['config']['js'] = $zoom['config']['installPath'].'/axZm/'; 
	$zoom['config']['fontPath'] = $zoom['config']['installPath'].'/axZm/fonts/'; 
	$zoom['config']['tempCache'] = $zoom['config']['installPath']."/axZm/cache/";
	$zoom['config']['mapPath'] = $zoom['config']['installPath'].'/axZm/pic/zoommap/';
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 

	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	  
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	  
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
}

// Wordpress plugin
if (isset($_GET['picDim'])){
	$picDim = explode('x', $_GET['picDim']);
	$picDim[0] = intval($picDim[0]);
	$picDim[1] = intval($picDim[1]);
	if ($picDim[0] > 1100){$picDim[0] = 1100;}
	if ($picDim[1] > 900){$picDim[0] = 900;}
	$zoom['config']['picDim'] = $picDim[0].'x'.$picDim[1];
	$zoom['config']['cTimeCompare'] = true;
}


?>