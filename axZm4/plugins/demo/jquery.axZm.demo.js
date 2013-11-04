/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.demo.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


/***************************************************/
/////////////////////////////////////////////////////
//////////////// DEMO FUNCTIONS /////////////////////
/////////////////////////////////////////////////////
/***************************************************/



jQuery.ajaxSubmitCustom = function (formName,targetDiv,ajaxUrl){
	jQuery('#'+formName).ajaxSubmit ({
		target: '#'+targetDiv,
		url: ajaxUrl,
		type: 'get'
	}); 
	return false; 
}

jQuery.colPick=function(fld,ff){
	jQuery('#'+fld).ColorPicker({
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn('fast');
			demoColorDisable();
			return false;
		},
		onBeforeShow: function(){
			demoColorDisable();
			jQuery(this).ColorPickerSetColor(this.value);
		},
		onHide: function (colpkr) {
			var atr=jQuery('#'+fld).attr('value');
			eval (ff+'(\'#'+atr+'\')');
			demoColorDisable();
			jQuery(colpkr).fadeOut('fast');
			return false;
		},
		onSubmit: function(hsb, hex, rgb) {
			jQuery('#'+fld).val(hex);
			eval (ff+'(\'#'+hex+'\')');
			sclTo(1000);
			jQuery('#'+fld).ColorPickerHide();
		},
		onChange: function (hsb, hex, rgb) {
			eval (ff+'(\'#'+hex+'\')');
		}
	})
	.bind('keyup', function(){
		demoColorDisable();
		jQuery(this).ColorPickerSetColor(this.value);
	});			
}

function sclTo(tOut){
	jQuery(window).scrollTo('#zoomAll', {
		duration: 300, 
		onAfter:function(){
			setTimeout(function(){
				demoColorDisable();
			}, tOut);
		}
	});
}

function demoShowSwitch(){
	if (jQuery('#motionSwitch:checked').val() == 1){
		jQuery.demoAnm=true;
	}else{
		jQuery.demoAnm=false;
	}
}

function demoDisableForm(formName){
	jQuery('#'+formName).attr("disabled", "disabled");
}

function demoEnableForm(formName){
	jQuery('#'+formName).removeAttr("disabled");
}

function demoDisableForms(){
	demoDisableForm('aniForm');
	demoDisableForm('selForm');
	demoDisableForm('demoOptions');
}

function demoEnableForms(){
	demoEnableForm('aniForm');
	demoEnableForm('selForm');
	demoEnableForm('demoOptions');	
}
	
jQuery.demoAnimate = function(options) {
	var settings = {
		layer: jQuery.axZm['picLayer'], 
		backLayer: jQuery.axZm['backLayer'],
		speed: jQuery.axZm['zoomSpeed'],
		motion: jQuery.axZm['zoomEaseIn'],
		factor: jQuery.axZm['pZoom'],
		move: jQuery.axZm['pMove'],
		moveSpeed: jQuery.axZm['moveSpeed'],
		moveMotion: jQuery.axZm['zoomEaseMove'],
		outSpeed: jQuery.axZm['restoreSpeed'],
		outMotion: jQuery.axZm['zoomEaseRestore'],
		pause: 1000,
		// functions
		zoomIn: false,
		zoomSelect: false,
		zoomPan: false,
		// for zoom pan
		zoomPanDir: false
	};
	// save defaults for zoom in, if zoom out
	var b = settings; 
	
	// extend defaults settings
	a = jQuery.extend(settings, options);
	
	var mapSwitched=false;
	
	// reaktivated map if it has been switched off for demo
	function reaktivateMap(){
		if (mapSwitched && jQuery.axZm['useMap']){
			if (jQuery.axZm['zoomMapButton']){
				jQuery.fn.axZm.zoomMapSwitchButton('zoomMapButton');
			}else{
				jQuery.fn.axZm.zoomMapToggle('show');
			}
		}
	}
	
	// Initial Position bevore animation
	function resetPic(){
		if (jQuery.axZm['useMap']){
			if (jQuery.axZm['zoomMapButton']){
				if (jQuery('#zoomMapHolder').css('display') != 'none'){
					jQuery.fn.axZm.zoomMapSwitchButton('zoomMapButton');
					mapSwitched=true;
				}
			}else{
				if (jQuery('#zoomMapHolder').css('zIndex')!=1){
					jQuery.fn.axZm.zoomMapToggle('hide');
					mapSwitched=true;
				}
			}
		}
		
		jQuery('#'+a.layer).attr('src', jQuery.axZm['smallImg']);
		jQuery('#'+a.layer).css({left:jQuery.axZm['zmLeftOffset'],top:jQuery.axZm['zmTopOffset'],width:jQuery.axZm['iw'],height:jQuery.axZm['ih']});
		jQuery('#'+a.backLayer).attr('src', jQuery.axZm['smallImg']);
		jQuery('#'+a.backLayer).css({left:jQuery.axZm['zmLeftOffset'],top:jQuery.axZm['zmTopOffset'],width:jQuery.axZm['iw'],height:jQuery.axZm['ih']});
		jQuery('#'+a.layer).show();
		jQuery('#'+a.backLayer).show();
		jQuery('#'+a.layer).css('position','absolute');
		jQuery('#'+a.backLayer).css('position','absolute');
	}
	
	// Initial position after animation
	function demoReset(){
		jQuery('#'+a.layer).fadeTo('fast',0.0,function(){
			jQuery('#'+a.backLayer).animate( {
				width: jQuery.axZm['iw'], 
				height: jQuery.axZm['ih'], 
				left: jQuery.axZm['zmLeftOffset'],
				top: jQuery.axZm['zmTopOffset']
				},a.outSpeed, a.outMotion, function(){
					jQuery('#'+jQuery.axZm['picLayer']).css({left:jQuery.axZm['zmLeftOffset'],top:jQuery.axZm['zmTopOffset'],width: jQuery.axZm['iw'],height: jQuery.axZm['ih']});
					jQuery('#'+jQuery.axZm['picLayer']).fadeTo('fast',1,function(){
						jQuery.fn.axZm.resetBack();
						demoEnableForms();				
						jQuery.fn.axZm.areaRestart();
						reaktivateMap();
						return true;
					});
			});
		});		
	}

	// Zoom in v = vars = a|b, rst = reset true, false
	function demoZoomIN(v, rst){
		var zoomRatio=((100 + v.factor)/100);
		jQuery('#'+v.layer).animate( {
				width: Math.ceil(jQuery.axZm['iw']*zoomRatio), 
				height: Math.ceil(jQuery.axZm['ih']*zoomRatio), 		
				left: - Math.ceil(((jQuery.axZm['iw']*zoomRatio)-jQuery.axZm['boxW'])/2),
				top: - Math.ceil(((jQuery.axZm['ih']*zoomRatio)-jQuery.axZm['boxH'])/2)
			}, 
			v.speed, v.motion, function(){
		}); 

		jQuery('#'+v.backLayer).animate( {
				width: Math.ceil(jQuery.axZm['iw']*zoomRatio), 
				height: Math.ceil(jQuery.axZm['ih']*zoomRatio), 		
				left: - Math.ceil(((jQuery.axZm['iw']*zoomRatio)-jQuery.axZm['boxW'])/2),
				top: - Math.ceil(((jQuery.axZm['ih']*zoomRatio)-jQuery.axZm['boxH'])/2)
			}, 
			v.speed, v.motion, function(){
				if (rst){
					setTimeout(function(){
						demoReset();
					}, 500);
				}else{
					return true;
				}				
		}); 		
	}
	
	function demoZoomPAN(v){
		var zoomRatio=((100 + v.factor)/100);
		jQuery('#'+v.layer).animate( {
				width: Math.ceil(jQuery.axZm['iw']*zoomRatio), 
				height: Math.ceil(jQuery.axZm['ih']*zoomRatio), 		
				left: 0,
				top: - Math.ceil(((jQuery.axZm['ih']*zoomRatio)-jQuery.axZm['boxH'])/2)
			}, 
			v.speed, v.motion, function(){
		}); 

		jQuery('#'+v.backLayer).animate( {
				width: Math.ceil(jQuery.axZm['iw']*zoomRatio), 
				height: Math.ceil(jQuery.axZm['ih']*zoomRatio), 		
				left: 0,
				top: - Math.ceil(((jQuery.axZm['ih']*zoomRatio)-jQuery.axZm['boxH'])/2)
			}, 
			v.speed, v.motion, function(){
				setTimeout(function(){
					// start sidewards motion
					var mT=0;
					var mL=Math.round((jQuery.axZm['boxW']*((v.move+100)/100))-jQuery.axZm['boxW']);
					if (mL>(jQuery.axZm['iw']*zoomRatio-jQuery.axZm['boxW'])){mL=jQuery.axZm['iw']*zoomRatio-jQuery.axZm['boxW'];}
					if (v.zoomPanDir){
						mT = Math.round((jQuery.axZm['boxH']*((v.move+100)/100))-jQuery.axZm['boxH']);
					}
					jQuery('#'+v.layer).animate( {
							left: '-=' + mL,
							top: '-=' + mT
						}, 
						v.moveSpeed, v.moveMotion, function(){
					}); 
					jQuery('#'+v.backLayer).animate( {
							left: '-=' + mL,
							top: '-=' + mT
						}, 
						v.moveSpeed, v.moveMotion, function(){
						if (v.zoomPanDir){
							setTimeout(function(){
								jQuery('#'+v.layer).animate( {
										left: '-=' + mL,
										top: '+=' + mT
									}, 
									v.moveSpeed, v.moveMotion, function(){
								});
								jQuery('#'+v.backLayer).animate( {
										left: '-=' + mL,
										top: '+=' + mT
									}, 
									v.moveSpeed, v.moveMotion, function(){
									setTimeout(function(){
										demoReset();
									}, 500);									
								});
							}, v.pause);
						}else{
							setTimeout(function(){
								demoReset();
							}, 500);
						}
					}); 					
				}, v.pause);
	
		}); 		
	}

	function demoShowSelect1(){
		var width = Math.round(jQuery.axZm['iw']/((a.factor+100)/100));
		var height = Math.round(jQuery.axZm['ih']/((a.factor+100)/100));
		var left = Math.round((jQuery.axZm['iw']-width)/2);
		var top = Math.round((jQuery.axZm['ih']-height)/2);
		jQuery.fn.axZm.areaDisable(false);
		jQuery.fn.axZm.areaResize(false,{x1: left, y1: top, x2: (width+left), y2: (height+top)});
		//jQuery('#'+jQuery.axZm['overLayer']).imgAreaSelect({x1: left, y1: top, x2: (width+left), y2: (height+top), parent: '#zoomLayer', enable: true, hide: false});
	}
	
	jQuery(window).scrollTo('#zoomAll', {
		duration: 300, 
		onAfter:function(){
			setTimeout(function(){
				resetPic();
				demoDisableForms();
				
				//if (jQuery.zoomHEIGHT && jQuery.zoomWIDTH){jQuery('#zoomOpr').load('zoom_load_session.php?reset=1');}
				
				if (a.zoomIn == true){
					demoZoomIN(a, true);
				}
				else if (a.zoomPan == true){
					demoZoomPAN(a);		
				}
				else if (a.zoomSelect == true){
					demoShowSelect1();
					setTimeout(function(){
						demoColorDisable();
						demoZoomIN(a, true);
					}, 1500);
				}				
			}, 500);
		}
	});
	


}


// Visual area creation
function demoShowSelect(){
	var width = Math.round(jQuery.axZm['iw']/2);
	var height = Math.round(jQuery.axZm['ih']/2);
	var a = jQuery.axZm['iw']-width-30;
	var b = jQuery.axZm['ih']-height-30;
	var c = a + width;
	var d = b + height;

	jQuery.fn.axZm.areaDisable(false);
	jQuery.fn.axZm.areaResize(false,{x1: a, y1: b, x2: c, y2: d});
}

function demoColorDisable(){
	jQuery.fn.axZm.areaDisable();
	jQuery.fn.axZm.areaRestart();
	/*
	jQuery('#zoomAll').mousemove(function(){
		jQuery('#zoomAll').unbind('mousemove');
		jQuery.fn.axZm.areaRestart();
	});
	*/
}

function demoColorArea(color){
	jQuery.axZm.zoomSelectionColor = color;
	demoShowSelect();
}

function demoColorOuter(color){
	jQuery.axZm['zoomOuterColor']=color;
	demoShowSelect();
}


function demoColorBorder(color){
	jQuery.axZm['zoomBorderColor'] = color;
	demoShowSelect();
}


function demoBorder(thickness){
	jQuery.axZm['zoomBorderWidth']=parseInt(thickness);
	jQuery(window).scrollTo('#zoomAll', {
		duration: 300, 
		onAfter:function(){
			setTimeout(function(){
				demoShowSelect();
				setTimeout(function(){
					demoColorDisable();
				}, 2000);
			}, 500);
		}
	});
}

function demoSelOpacity(opacity){
	jQuery.axZm['zoomSelectionOpacity']=(parseInt(opacity))/10;

	jQuery(window).scrollTo('#zoomAll', {
		duration: 300, 
		onAfter:function(){
			setTimeout(function(){
				demoShowSelect();
				setTimeout(function(){
					demoColorDisable();
				}, 2000);
			}, 500);
		}
	});
}

function demoOuterOpacity(opacity){
	jQuery.axZm['zoomOuterOpacity']=(parseInt(opacity))/10;

	jQuery(window).scrollTo('#zoomAll', {
		duration: 300, 
		onAfter:function(){
			setTimeout(function(){
				demoShowSelect();
				setTimeout(function(){
					demoColorDisable();
				}, 2000);
			}, 500);
		}
	});
}

// Demo Motions
function demoClickRatio(percent,play){
	jQuery.axZm['pZoom']=(parseInt(percent)); // Percent
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: jQuery.axZm['zoomSpeed'], motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoClickOutRatio(percent,play){
	jQuery.axZm['pZoomOut']=(parseInt(percent)); // Percent
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 20, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoomOut'], outSpeed: jQuery.axZm['zoomOutSpeed'], outMotion: jQuery.axZm['zoomEaseOut'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

// xxx
function demoClickZoomOut(speed,play){
	jQuery.axZm['zoomOutSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 20, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], outSpeed: jQuery.axZm['zoomOutSpeed'], outMotion: jQuery.axZm['zoomEaseOut'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoClickSpeed(speed,play){
	jQuery.axZm['zoomSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: jQuery.axZm['zoomSpeed'], motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMoveRatio(percent,play){
	jQuery.axZm['pMove']=(parseInt(percent)); // Percent
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomPan: true});
			jQuery.fn.axZm.loadingEnd();
		});		
	}
}

function demoMoveSpeed(speed,play){
	jQuery.axZm['moveSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomPan: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoTraverseSpeed(speed,play){
	jQuery.axZm['traverseSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			var fact = Math.min((jQuery.axZm['ow']/jQuery.axZm['iw']),(jQuery.axZm['oh']/jQuery.axZm['ih']));
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: Math.round(fact*100), move: 55, moveSpeed: jQuery.axZm['traverseSpeed'], moveMotion: jQuery.axZm['zoomEaseTraverse'], zoomPanDir: 1, zoomPan: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoCropSpeed(speed,play){
	jQuery.axZm['cropSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: jQuery.axZm['cropSpeed'], motion: jQuery.axZm['zoomEaseCrop'], factor: jQuery.axZm['pZoom'], zoomSelect: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoRestoreSpeed(speed,play){
	jQuery.axZm['restoreSpeed']=(parseInt(speed)); // ms
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

// Motion Functions

function demoMotionIn(mfunction,play){
	jQuery.axZm['zoomEaseIn']=mfunction;
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: jQuery.axZm['zoomSpeed'], motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMotionCrop(mfunction,play){
	jQuery.axZm['zoomEaseCrop']=mfunction;
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: jQuery.axZm['cropSpeed'], motion: jQuery.axZm['zoomEaseCrop'], factor: jQuery.axZm['pZoom'], zoomSelect: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMotionOut(mfunction,play){
	jQuery.axZm['zoomEaseOut']=mfunction;
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 20, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], outSpeed: jQuery.axZm['zoomOutSpeed'], outMotion: jQuery.axZm['zoomEaseOut'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMotionMove(mfunction,play){
	jQuery.axZm['zoomEaseMove']=mfunction;
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomPan: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMotionTraverse(mfunction,play){
	jQuery.axZm['zoomEaseTraverse']=mfunction;

	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			var fact = Math.min((jQuery.axZm['ow']/jQuery.axZm['iw']),(jQuery.axZm['oh']/jQuery.axZm['ih']));
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: Math.round(fact*100), move: 55, moveSpeed: jQuery.axZm['traverseSpeed'], moveMotion: jQuery.axZm['zoomEaseTraverse'], zoomPanDir: 1, zoomPan: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoMotionRestore(mfunction,play){
	jQuery.axZm['zoomEaseRestore']=mfunction;
	if (jQuery.demoAnm==true || play){
		jQuery.fn.axZm.zoomReset(false,function(){
			jQuery.demoAnimate({speed: 50, motion: jQuery.axZm['zoomEaseIn'], factor: jQuery.axZm['pZoom'], zoomIn: true});
			jQuery.fn.axZm.loadingEnd();
		});
	}
}

function demoLoaderPos(p){
	jQuery.axZm['zoomLoaderPos'] = p;
	demoLoaderTransp(jQuery.axZm['zoomLoaderTransp']*100);
}

function demoLoaderTransp(t){
	jQuery.axZm['zoomLoaderTransp']=(parseInt(t)/100);
	if (jQuery.demoAnm==true || play){
		jQuery(window).scrollTo('#zoomAll', {
			duration: 300, 
			onAfter:function(){
				setTimeout(function(){
					demoDisableForms();
					jQuery.fn.axZm.loadingStart(true);
					setTimeout(function(){
						jQuery.fn.axZm.loadingEnd();
						demoEnableForms()
					}, 2000);
				}, 500);
			}
		});
	}
}


function demoColorStage(color){
	jQuery('#zoomContainer').css('backgroundColor',color);
}

function demoBodyColor(color){
	jQuery('body').css({'backgroundColor': color, 'background-image': 'none'});
}

function demoBodyBack(backg){
	if (!backg){
		jQuery('body').css({'background-image': 'none'});
		return;
	}
	var imgPfad = jQuery.axZm.iconDir + 'bg_' + backg;
	jQuery('<img />').load(function(){
		jQuery('body').css({'background-image': 'url(' + imgPfad + ')'});
	}).attr('src', imgPfad);
	
}

function demoPhys(val){
	jQuery.axZm['zoomDragPhysics'] = val;
}

function demoIeInterp(val){
	jQuery.axZm['msInterp'] = val;
}

function subOpt(id,block){
	if (jQuery('#'+id+':checked').val() != undefined){
		jQuery('#'+block).css('display','block');
	}else{
		jQuery('#'+block).css('display','none');
	}
}

