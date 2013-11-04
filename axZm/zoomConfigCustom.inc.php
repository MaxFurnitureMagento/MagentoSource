<?php
/**
* Plugin: jQuery AJAX-ZOOM, zoomConfigCustom.inc.php
* Copyright: Copyright (c) 2010 Jacobi Vadim
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


// Following configurations are for the examples provided in "examples" folder.

// Some of the above settings are overridden depending on $_GET['example'] custom value
if ($_GET['example'] == 1){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true;
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = true; 
	$zoom['config']['layVertCenter'] = true;
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1;
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 2){
	$zoom['config']['picDim'] = '720x480';
	$zoom['config']['useFullGallery'] = true;
	$zoom['config']['galleryFullPicDim'] = "100x100";
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['galleryAutoPlay'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;
	$zoom['config']['zoomLoaderPos'] = 'Center';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['layHorCenter'] = false;
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1; 

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 3){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = true;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = true; 
	$zoom['config']['galleryPicDim'] = '80x80';
	$zoom['config']['galleryLines'] = 3; 
	$zoom['config']['layHorCenter'] = true;
	$zoom['config']['layVertCenter'] = true; 
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 4){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 1;
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 5){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['galleryAutoPlay'] = false;
	$zoom['config']['galFullAutoStart'] = true;
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true;
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['layHorCenter'] = false;
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 6){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = true;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = true;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = true; 
	$zoom['config']['galleryPicDim'] = '70x70'; 
	$zoom['config']['galleryLines'] = 3; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1; 

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 7){
	$zoom['config']['picDim'] = "480x320";
	$zoom['config']['naviPos'] = "top";
	$zoom['config']['naviFloat'] = "right";
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true;
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = true;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['useGallery'] = false; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 1;
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 8){
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
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
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 0.8; 
	
	$zoom['config']['zoomSlider'] = true; // Ver. 3.0.2
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 9){
	$zoom['config']['buttonSet'] = 'flat';
	$zoom['config']['picDim'] = '320x480';
	$zoom['config']['help'] = false;
	$zoom['config']['galFullButton'] = false;
	$zoom['config']['naviGroupSpace'] = 10;
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['galleryPicDim'] = '70x70';
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
	$zoom['config']['galleryFullPicDim'] = '70x70';
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['mapBorder']['top'] = 0; 
	$zoom['config']['mapBorder']['right'] = 1; 
	$zoom['config']['mapBorder']['bottom'] = 1; 
	$zoom['config']['mapBorder']['left'] = 0; 
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['naviMinPadding'] = 0;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapButton'] = false;
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 0.8; 

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
	
	$zoom['config']['fullScreenNaviButton'] = false;
}

elseif ($_GET['example'] == 10){
	$zoom['config']['visualConf'] = true;
	$zoom['config']['zoomSlider'] = true;
	$zoom['config']['layHorCenter'] = true;  
	$zoom['config']['layVertCenter'] = true;  
	$zoom['config']['zoomDragAjax'] = 1000;
}

elseif ($_GET['example'] == 11){
	$zoom['config']['picDim'] = '420x280'; 
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['galHorPosition'] = 'bottom2';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['zoomMapRest'] = true;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 0.8;
	$zoom['config']['naviBigZoom'] = false;
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
	
	$zoom['config']['zoomSlider'] = true;
}

elseif ($_GET['example'] == 12){
	$zoom['config']['picDim'] = '420x280';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['useGallery'] = true;
	$zoom['config']['galleryPicDim'] = '70x70';
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['zoomMapRest'] = true;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 5;
	$zoom['config']['innerMargin'] = 5;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 0.8; 
	$zoom['config']['naviBigZoom'] = false;
	$zoom['config']['pssBar'] = false;
 
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 13){
	$zoom['config']['picDim'] = '420x280';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['galHorPosition'] = 'bottom2';
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['help'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = false;
	$zoom['config']['galleryNavi'] = false;
	$zoom['config']['useMap'] = true; 
	$zoom['config']['dragMap'] = false; 
	$zoom['config']['zoomMapRest'] = true;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['zoomMapContainment'] = 'body';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 0.8; 
	$zoom['config']['naviBigZoom'] = false;
	$zoom['config']['zoomLoaderEnable'] = false;
 
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

elseif ($_GET['example'] == 14){
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; 
	$zoom['config']['zoomDragPhysics'] = false; 
	$zoom['config']['zoomDragAnm'] = false; 
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; 

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; 
	$zoom['config']['pyrTilesFadeLoad'] = 250; 
	
	$zoom['config']['pssBar'] = false;
	$zoom['config']['useFullGallery'] = false;
	
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
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
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false; 
	$zoom['config']['galleryThumbOverOpaque'] = 1; 
	$zoom['config']['galleryThumbOutOpaque'] = 0.8;
}

elseif ($_GET['example'] == 15){
	$zoom['config']['picDim'] = "480x360";
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; 
	$zoom['config']['zoomDragPhysics'] = false; 
	$zoom['config']['zoomDragAnm'] = false; 
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; 

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; 
	$zoom['config']['pyrTilesFadeLoad'] = 250; 
	
	$zoom['config']['pssBar'] = false;
	$zoom['config']['useFullGallery'] = false;
	
	$zoom['config']['mapPos'] = 'bottomRight';
	$zoom['config']['galleryPos'] = 'left';
	$zoom['config']['naviFloat'] = 'left';
	$zoom['config']['galleryLines'] = 4;
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
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
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false; 
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

	$zoom['config']['cropNoObj'] = true;
}

elseif ($_GET['example'] == 16){
	$zoom['config']['picDim'] = "480x320";
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['useFullGallery'] = true;
	$zoom['config']['useGallery'] = false; 
	
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true;
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';
	$zoom['config']['pssBar'] = false;  
	
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['dragMap'] = false;
	
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false;
	
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/';  
	$zoom['config']['zoomDragPhysics'] = false;  
	$zoom['config']['zoomDragAnm'] = false;  
	$zoom['config']['zoomDragSpeed'] = 500; 
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc';  

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000;  
	$zoom['config']['pyrTilesFadeLoad'] = 150;  
}

// 3D Zoom & Spin
elseif ($_GET['example'] == 17){
	$zoom['config']['picDim'] = "600x400";
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; 
	$zoom['config']['zoomDragPhysics'] = false; 
	$zoom['config']['zoomDragAnm'] = false; 
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; 

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; 
	$zoom['config']['pyrTilesFadeLoad'] = 250; 
	
	$zoom['config']['pssBar'] = false;
	$zoom['config']['useFullGallery'] = true;
	$zoom['config']['galFullButton'] = false;

	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['visualConf'] = false;

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
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false; 

	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;

	$zoom['config']['cropNoObj'] = true;
	
	$zoom['config']['spinMod'] = true;
	$zoom['config']['galleryNoThumbs'] = true;
	$zoom['config']['firstMod'] = 'spin';
	$zoom['config']['zoomSlider'] = true; // Ver. 3.0.2
	$zoom['config']['spinEffect']['enabled'] = false; // Ver. 3.0.2
}

elseif ($_GET['example'] == 18){

	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 0.5;
	$zoom['config']['pssBar'] = false;
	$zoom['config']['firstMod'] = 'pan';
	$zoom['config']['zoomSlider'] = 'pan';
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['picDim'] = '400x600';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['useFullGallery'] = true;
	$zoom['config']['galleryFullPicDim'] = '90x90';
	$zoom['config']['useGallery'] = false;
	$zoom['config']['visualConf'] = false;
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
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;
	
	$zoom['config']['galleryFadeInSize'] = 1;
	$zoom['config']['galleryFadeInSpeed'] = 300;
	$zoom['config']['galleryFadeInOpacity'] = 0.5;
	$zoom['config']['galleryFadeInAnm'] = 'Right';
 
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 150; // integer	
	
	$zoom['config']['fullScreenNaviBar'] = false;
}

elseif ($_GET['example'] == 19){

	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 1;
	$zoom['config']['pssBar'] = false;
	$zoom['config']['firstMod'] = 'pan';
	$zoom['config']['zoomSlider'] = 'pan';

	$zoom['config']['autoZoom']['enabled'] = true; // bool
	$zoom['config']['autoZoom']['onlyFirst'] = true; // bool
	$zoom['config']['autoZoom']['speed'] = 500; // integer
	$zoom['config']['autoZoom']['motion'] = 'easeOutQuad'; // string
	$zoom['config']['autoZoom']['pZoom'] = '100%'; // mixed int, string: 'fill', 'max' or %, e.g. '50%'		
	
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['picDim'] = '360x540';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['visualConf'] = false;
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
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;
 
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 150; // integer
	
	$zoom['config']['fullScreenNaviBar'] = false;
}

elseif ($_GET['example'] == 20){
	$zoom['config']['picDim'] = '600x400'; // inner dimensions player
	$zoom['config']['displayNavi'] = true; // enable / disable zoom buttons
	
	$zoom['config']['useHorGallery'] = true;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['useFullGallery'] = false;
	
	$zoom['config']['visualConf'] = false;
	$zoom['config']['scrollPane'] = true;
	$zoom['config']['galleryNavi'] = false; 
	$zoom['config']['galleryNaviPos'] = 'bottom'; 

	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
 
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 
	
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false; 
	$zoom['config']['layVertBotMrg'] = false; 
	
	$zoom['config']['galleryThumbOverOpaque'] = 1;
	$zoom['config']['galleryThumbOutOpaque'] = 0.8; 
	
	$zoom['config']['zoomSlider'] = true; // Ver. 3.0.2
	
	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

// Animation
elseif ($_GET['example'] == 21){
	$zoom['config']['picDim'] = "600x400";
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; 
	$zoom['config']['zoomDragPhysics'] = false; 
	$zoom['config']['zoomDragAnm'] = false; 
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; 

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; 
	$zoom['config']['pyrTilesFadeLoad'] = 250; 
	
	$zoom['config']['pssBar'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galFullButton'] = false;

	$zoom['config']['naviFloat'] = 'right';
	$zoom['config']['visualConf'] = false;

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
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false; 

	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center'; 

	$zoom['config']['helpMargin'] = 0;
	$zoom['config']['help'] = false;
	$zoom['config']['mapButton'] = true;

	$zoom['config']['cropNoObj'] = true;
	
	$zoom['config']['spinMod'] = true;
	$zoom['config']['firstMod'] = 'spin';
	$zoom['config']['zoomSlider'] = true; // Ver. 3.0.2
	$zoom['config']['spinSliderPosition'] = 'naviTop'; // Possible values: naviTop, naviBottom, top, bottom
	
	$zoom['config']['spinSliderWidth'] = false; // false = auto
	$zoom['config']['spinSliderContainerPadding'] = array('top'=>5, 'right'=>20, 'bottom'=>5, 'left' =>10);
	$zoom['config']['spinSliderContainerHeight'] = 42;

	$zoom['config']['spinDemoTime'] = 4000; // int ms
	$zoom['config']['spinEffect']['enabled'] = false;
	
	$zoom['config']['naviSpinButSwitch'] = false;
	$zoom['config']['naviTopMargin'] = 0; 

	$zoom['config']['cueFrames'] = false;  
	$zoom['config']['spinSliderPlayButton'] = true;  
	
	$zoom['config']['spinSliderTopMargin'] = 10;
	
	$zoom['config']['spinAreaDisable'] = true;  
	$zoom['config']['spinToMotion'] = 'easeOutQuad';  
}

// Mouse hover zoom
elseif ($_GET['example'] == 22){
	$zoom['config']['displayNavi'] = false;
	$zoom['config']['mapParent'] = 'mapContainer';
	$zoom['config']['mapFract'] = 0.7;
	$zoom['config']['pssBar'] = false;
	$zoom['config']['firstMod'] = 'pan';
	$zoom['config']['zoomSlider'] = false;
	$zoom['config']['restoreSpeed'] = 1;
	$zoom['config']['zoomMapSwitchSpeed'] = 1; // int, ms
	$zoom['config']['galleryInnerFade'] = false;
	$zoom['config']['galleryFadeInSpeed'] = 1;
	$zoom['config']['galleryFadeOutSpeed'] = 1;
	$zoom['config']['pZoom'] = 125;
	$zoom['config']['pZoomOut'] = 25;
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['autoZoom']['enabled'] = true; // bool
	$zoom['config']['autoZoom']['onlyFirst'] = false; // bool
	$zoom['config']['autoZoom']['speed'] = 1; // integer
	$zoom['config']['autoZoom']['motion'] = 'swing'; // string
	$zoom['config']['autoZoom']['pZoom'] = 'max'; // mixed int, string: 'fill', 'max' or %, e.g. '50%'		
	
	$zoom['config']['picDim'] = '530x500';
	
	$zoom['config']['useHorGallery'] = false;
	
	$zoom['config']['useFullGallery'] = true;
	$zoom['config']['galleryNoThumbs'] = false;
	$zoom['config']['galleryFullPicDim'] = "62x62"; // string
	
	$zoom['config']['useGallery'] = false;
	$zoom['config']['visualConf'] = false;
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
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;

	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 150; // integer

	$zoom['config']['mapWidth'] = 250;
	$zoom['config']['mapHeight'] = false;
	$zoom['config']['allowDynamicThumbs'] = false;
	
	$zoom['config']['fullScreenNaviBar'] = false;
}

elseif ($_GET['example'] == 23){

	$zoom['config']['displayNavi'] = false;
	
	$zoom['config']['pssBar'] = false;
	$zoom['config']['visualConf'] = false;
	$zoom['config']['firstMod'] = 'pan';
	
	$zoom['config']['zoomSlider'] = true;
	$zoom['config']['zoomSliderPosition'] = 'topLeft'; // string
	$zoom['config']['zoomSliderMarginV'] = 167; // int
	$zoom['config']['zoomSliderMarginH'] = 15; // int
	$zoom['config']['zoomSliderOpacity'] = 1; // float [0.0 - 1.0]

	
	$zoom['config']['mapSelSmoothDrag'] = false;	
	$zoom['config']['picDim'] = '600x400';
	
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['galHorMarginBottom'] = 0;
	$zoom['config']['galHorFlow'] = true;
	$zoom['config']['galHorArrows'] = false;
		
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['galleryFullPicDim'] = '120x120';
	$zoom['config']['galFullAutoStart'] = true;

	$zoom['config']['useGallery'] = true;
	$zoom['config']['galleryPicDim'] = '120x120'; // string
	$zoom['config']['galleryScrollbarWidth'] = 10;

	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = false;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['mapBorder']['top'] = 0; 
	$zoom['config']['mapBorder']['right'] = 1; 
	$zoom['config']['mapBorder']['bottom'] = 1; 
	$zoom['config']['mapBorder']['left'] = 0; 
	
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 0;
	$zoom['config']['zoomLogInfo'] = false;  
	$zoom['config']['zoomLogJustLevel'] = true; 
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1; 
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['layHorCenter'] = false; 
	$zoom['config']['layVertCenter'] = false;
	$zoom['config']['layVertBotMrg'] = false;
 
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 150; // integer

	$zoom['config']['galleryFadeInSize'] = 1;
	$zoom['config']['zoomFadeIn'] = 1000;
	$zoom['config']['galleryFadeInSpeed'] = 1000;
	$zoom['config']['galleryFadeInOpacity'] = 0.0;
	$zoom['config']['galleryFadeInAnm'] = 'Center';
	
	$zoom['config']['fullScreenNaviBar'] = false;
	$zoom['config']['fullScreenMapFract'] = false;
	$zoom['config']['fullScreenMapWidth'] = false;
	$zoom['config']['fullScreenMapHeight'] = 120;
	$zoom['config']['fullScreenRel'] = 'content';
	
	$zoom['config']['buttonSet'] = 'flat';
	$zoom['config']['galleryNavi'] = false;
}

elseif ($_GET['example'] == 24){
	$zoom['config']['picDim'] = '600x600';
	$zoom['config']['useHorGallery'] = false;
	$zoom['config']['useFullGallery'] = false;
	$zoom['config']['useGallery'] = false;
	$zoom['config']['mapSelSmoothDrag'] = false;	
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false;
	$zoom['config']['galleryNavi'] = false;
	
	$zoom['config']['zoomLogInfo'] = false;
	$zoom['config']['zoomLogJustLevel'] = true;
	$zoom['config']['help'] = false;
	
	$zoom['config']['fullScreenNaviButton'] = false;
	$zoom['config']['fullScreenCornerButton'] = false;
	$zoom['config']['fullScreenExitText'] = false;

	// load image tiles directly block
	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['pyrTilesPath'] = $zoom['config']['installPath'].'/pic/zoomtiles_80/'; //string
	$zoom['config']['zoomDragPhysics'] = false; // boolean
	$zoom['config']['zoomDragAnm'] = false; // boolean
	$zoom['config']['zoomDragSpeed'] = 500; // int
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000; // integer
	$zoom['config']['pyrTilesFadeLoad'] = 250; // integer	
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc'; // easeOutCirc
}

// Magento implementation, see mods/magento/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'magento'){

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
	
	$zoom['config']['pyrQual'] = 80;

	$zoom['config']['zoomDragPhysics'] = false;  
	$zoom['config']['zoomDragAnm'] = false;  
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc';

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000;
	$zoom['config']['pyrTilesFadeLoad'] = 250;
	
	$zoom['config']['pssBar'] = false;
	
	$zoom['config']['picDim'] = "780x450"; 

	$zoom['config']['cropNoObj'] = true;
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 
	
	$zoom['config']['useGallery'] = true;
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	
	$zoom['config']['visualConf'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true;  
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;  
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['layHorCenter'] = false;  
	$zoom['config']['layVertCenter'] = false;  
	$zoom['config']['layVertBotMrg'] = false;  
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
	
	// Settings for 360 spin
	if ($_GET['3dDir']){
		$zoom['config']['picDim'] = "740x430"; 
		
		$zoom['config']['useFullGallery'] = true;
		$zoom['config']['galFullButton'] = false;
	
		$zoom['config']['naviFloat'] = 'right';
		
		$zoom['config']['galleryNavi'] = false; 
		$zoom['config']['useGallery'] = false;
	
		$zoom['config']['helpMargin'] = 0;
		$zoom['config']['help'] = false;
		$zoom['config']['mapButton'] = true;
		
		$zoom['config']['spinMod'] = true;
		$zoom['config']['galleryNoThumbs'] = true;
		$zoom['config']['firstMod'] = 'spin'; // crop, pan, spin
		$zoom['config']['zoomSlider'] = true;
		
		$zoom['config']['spinEffect']['enabled'] = false;
	} 
}

// xt:Commerce implementation, see mods/xtc/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'xtc'){

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
	
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['cTimeCompare'] = true;

	$zoom['config']['zoomDragPhysics'] = false;  
	$zoom['config']['zoomDragAnm'] = false;  
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;
	$zoom['config']['zoomDragMotion'] = 'easeOutCirc';

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 1000;
	$zoom['config']['pyrTilesFadeLoad'] = 250;
	
	$zoom['config']['pssBar'] = false;
	
	$zoom['config']['picDim'] = "800x450"; 

	$zoom['config']['cropNoObj'] = true;
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 
	
	$zoom['config']['useGallery'] = true;
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	
	$zoom['config']['visualConf'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['mapSelSmoothDrag'] = false;
	
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true;  
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;  
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['layHorCenter'] = false;  
	$zoom['config']['layVertCenter'] = false;  
	$zoom['config']['layVertBotMrg'] = false;  
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
	
	// Settings for 360 spin
	if ($_GET['3dDir']){
		$zoom['config']['picDim'] = "760x430"; 
		
		$zoom['config']['useFullGallery'] = true;
		$zoom['config']['galFullButton'] = false;
	
		$zoom['config']['naviFloat'] = 'right';
		
		$zoom['config']['galleryNavi'] = false; 
		$zoom['config']['useGallery'] = false;
	
		$zoom['config']['helpMargin'] = 0;
		$zoom['config']['help'] = false;
		$zoom['config']['mapButton'] = true;
		
		$zoom['config']['spinMod'] = true;
		$zoom['config']['galleryNoThumbs'] = true;
		$zoom['config']['firstMod'] = 'spin'; // crop, pan, spin
		$zoom['config']['zoomSlider'] = true;
		
		$zoom['config']['spinEffect']['enabled'] = false;
	} 
}
// Oxid implementation, see mods/oxid/readme_manual.txt
// The following configuration parameters are overrides of the above.
// You can change or add any parameter.
elseif ($_GET['example'] == 'oxid'){
 
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
	
	$zoom['config']['pyrQual'] = 80;
	$zoom['config']['cTimeCompare'] = true;

	$zoom['config']['zoomDragPhysics'] = false;  
	$zoom['config']['zoomDragAnm'] = false;  
	$zoom['config']['zoomDragSpeed'] = 500;
	$zoom['config']['zoomDragAjax'] = 2500;

	$zoom['config']['pyrLoadTiles'] = true;
	$zoom['config']['pyrTilesFadeInSpeed'] = 300;
	$zoom['config']['pyrTilesFadeLoad'] = 250;
	
	$zoom['config']['pssBar'] = false;
	
	$zoom['config']['picDim'] = "800x450"; 
	$zoom['config']['cropNoObj'] = true;
	
	$zoom['config']['galleryNavi'] = true;
	$zoom['config']['useFullGallery'] = false; 
	
	$zoom['config']['useGallery'] = true;
	$zoom['config']['galleryPicDim'] = '100x100';
	$zoom['config']['galleryMarginLeft'] = 5; 
	$zoom['config']['galleryCssPadding'] = 5; 
	$zoom['config']['galleryCssBorderWidth'] = 1; 
	$zoom['config']['galleryCssDescrHeight'] = 1; 
	$zoom['config']['galleryCssDescrPadding'] = 2; 
	$zoom['config']['galleryCssMargin'] = 6; 
	
	$zoom['config']['visualConf'] = false;
	$zoom['config']['zoomMapRest'] = false;
	$zoom['config']['zoomMapContainment'] = '#zoomAll';
	$zoom['config']['help'] = false;
	$zoom['config']['cornerRadius'] = 0;
	$zoom['config']['innerMargin'] = 1;
	$zoom['config']['dragMap'] = false;
	$zoom['config']['zoomLogInfo'] = false; 
	$zoom['config']['zoomLogJustLevel'] = true;  
	$zoom['config']['zoomLoaderClass'] = 'zoomLoader3';
	$zoom['config']['zoomLoaderTransp'] = 1;  
	$zoom['config']['zoomLoaderPos'] = 'Center';  
	$zoom['config']['zoomMapAnimate'] = false;
	$zoom['config']['zoomMapVis'] = false; 
	$zoom['config']['layHorCenter'] = false;  
	$zoom['config']['layVertCenter'] = false;  
	$zoom['config']['layVertBotMrg'] = false;  
	$zoom['config']['galleryThumbOverOpaque'] = 1;  
	$zoom['config']['galleryThumbOutOpaque'] = 1;  
}
?>