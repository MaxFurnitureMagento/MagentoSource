/**
* Plugin: jQuery AJAX-ZOOM, Magento JS helper file: magento_axZm.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


;(function($) {

	function getl(sep, str){
		return str.substring(str.lastIndexOf(sep)+1);
	}
	
	function getf(sep, str){
		var extLen = getl(sep, str).length;
		return str.substring(0, (str.length - extLen - 1));
	}
	
	// Optionally add stop/play button for gif animation
	jQuery.fn.addStopPlay = function(url){
		if (!jQuery.axZmStopPlay){return false;}
		if (!jQuery.axZmState360){jQuery.axZmState360 = 'gif';}
		jQuery('#axZmStopPlay').remove();
		var axZmStopPlay = jQuery('<DIV />').attr('id', 'axZmStopPlay').css({
			position: 'absolute',
			left: 0,
			top: 0,
			zIndex: 2,
			padding: 3,
			cursor: 'pointer'
		}).html('<DIV class="axZmStopPlayGif">'+(jQuery.axZmState360 == 'gif' ? 'Pause' : 'Play')+'</DIV>').appendTo('#axZm-product-link');
		
		axZmStopPlay.bind('click', function(event){
			event.stopPropagation();
			if (jQuery.axZmState360 == 'gif'){
				//var jpg = getf('/',url)+'/'+'axZmFirstFrame.jpg';
				jQuery('#axZm-img').attr('src', jQuery.preview360FirstFramePath);
				jQuery.axZmState360 = 'jpg';
				jQuery('#axZmStopPlay').html('<DIV class="axZmStopPlayGif1">Play</DIV>');
			}else{
				//jQuery('#axZm-img').attr('src', url);
				jQuery('#axZm-img').attr('src', jQuery.preview360imagePath);
				jQuery.axZmState360 = 'gif';
				jQuery('#axZmStopPlay').html('<DIV class="axZmStopPlayGif">Pause</DIV>');
			}
			
			return false;
		});		
	}
	
	// Switch images function
	jQuery.fn.rollImage = function (url, orgImage, fadeInSpeed, linkObj, spin){
		
		if ($('#axZm-img').attr('src') == url){return false;}
		var thisTitle = $('#axZm-img').attr('title');
		
		if ($.axZmLoading){
			 $.axZmBreak = true;
		}
		
		$.axZmLoading = true;
				
		$('#axZm-product-link').attr('id','axZm-product-link_old').css({zIndex: 1, position: 'absolute'});
		$('#axZm-img').attr('id','axZm-img_old');

		var loadImg = new Image();

		// Loading message
		$('<DIV />').attr('id', 'axZmLoadingMsg').css({
			width: (parseInt($('#axZm-product-image').width())),
			height: 15,
			padding: 5,
			position: 'absolute',
			/*backgroundColor: '#FFFFFF',*/
			opacity: 0,
			zIndex: 99
		}).html('Loading...').prependTo('#axZm-product-image').fadeTo('fast', 0.75);

		var urlToShow = url;
		if (spin && jQuery.axZmState360 == 'jpg'){
			urlToShow = getf('/',url)+'/'+'axZmFirstFrame.jpg';
		}
		
		$(loadImg).load(function(){
			$('#axZmLoadingMsg').fadeTo(fadeInSpeed, 0).remove();

			var link = $('<a />').attr('id', 'axZm-product-link').css({display: 'block', opacity: 0, position: 'absolute', zIndex: 2});
			$('<img>').attr('src', urlToShow).attr('id', 'axZm-img').attr({title: thisTitle, alt: thisTitle}).appendTo(link);
			
			if (!$.axZmBreak){
				$(link).prependTo('#axZm-product-image').fadeTo(fadeInSpeed, 1, function(){
					$('#axZm-product-link_old').remove();
					$('#axZm-img_old').remove();
					$(this).css({cursor: 'pointer'});
					jQuery.fn.zoomImage(orgImage, linkObj, spin);
					
					if (spin){
						jQuery.fn.addStopPlay(url);
					}
				});
			}else{
				$.axZmBreak = false;
			}
			
			$.axZmLoading = false;
			
		}).attr('src', urlToShow);
		
	};
	
	// Add AJAX-ZOOM to the switched image to open in fancybox or as fullscreen
	jQuery.fn.zoomImage = function(orgImage, linkObj, spin){
		function isObject(x) {
			return (typeof x === 'object') && !(x instanceof Array) && (x !== null);
		}

		if (spin){
			// Query string 360 spin
			var alink = 'example=magento&3dDir='+orgImage;
			jQuery.spin360Loaded = true;
		}else{
			// Query string zoom
			var alink = 'example=magento&zoomFile='+orgImage;
			if (jQuery.zoomData){alink += '&zoomData='+jQuery.zoomData;}	
			jQuery.spin360Loaded = false;
		}
		
		if (jQuery.zoomTouchStyle == 'yes'){
			alink += '&zoomTouchStyle=yes';
		}

		//jQuery('#axZm-img').attr('alt','').attr('title','');
		
		
		// Private function to hide the image
		function toggleSpinGifImage(act){
			if (jQuery.axZmState360 == 'gif'){
				jQuery('#axZm-img').css('display', (act == 'show') ? 'block' : 'none');
			}
			jQuery('#thumb360spin').css('display', (act == 'show') ? 'block' : 'none');
		}
		
		
		// Open as fullscreen, can be set in media.phtml
		if (jQuery.axZmOpenAsFullScreen){
			$('#axZm-product-link').unbind().bind('click', function(){
				// AJAX-ZOOM Api function
				jQuery.fn.axZm.openFullScreen(axZm_BaseUrl + '/axZm/', alink, {
					onFullScreenStart:  function(){toggleSpinGifImage('hide');},
					onFullScreenClose: function(){toggleSpinGifImage('show');},
					onLoad: function(){

					},
					onSpinPreloadEnd: function(){

					},
					onCropEnd: function(){

					}
				}, 'window', true);
				
			});
		}
		// Open in a lightbox (fancybox)
		// Can be replaced with any other lightbox, 
		// just do not forget to trigger jQuery.fn.axZm(); after ajax request is completed (must have a callback for it)
		else{
			alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&'+alink;
			if (jQuery.zoomTouchStyle == 'yes'){
				alink += '&zoomTouchStyle=yes';
			}
			jQuery('#axZm-product-link').attr('href', alink).unbind().fancybox({
				padding				: 14,
				overlayShow			: (jQuery.browser.msie ? false : true), // overlay can slow down performance in ie
				overlayOpacity		: 0.5,
				overlayColor		: '#000000',
				zoomSpeedIn			: 0,
				zoomSpeedOut		: 100,
				easingIn			: "swing",
				easingOut			: "swing",
				hideOnContentClick	: false, // Important
				centerOnScroll		: true,
				imageScale			: true,
				autoDimensions		: true,
				callbackOnShow: function(){ // Fancybox callback after loading
					jQuery.fn.axZm({
						onLoad: function(){// AJAX-ZOOM callback after loading   
							toggleSpinGifImage('hide');
						},
						onSpinPreloadEnd: function(){

						},
						onCropEnd: function(){

						}
					});
					 
				},
				callbackOnClose: function (){
					toggleSpinGifImage('show')
				},
				
				// New fancybox has different callback names //
				onComplete: function(){ // Fancybox callback after loading
					jQuery.fn.axZm({
						onLoad: function(){// AJAX-ZOOM callback after loading   
							toggleSpinGifImage('hide');
						},
						onSpinPreloadEnd: function(){

						},
						onCropEnd: function(){

						}
					});
				},
				onClosed: function (){
					toggleSpinGifImage('show')
				}
				
			});
		}
		
		if ((typeof linkObj === 'object') && !(linkObj instanceof Array) && (linkObj !== null)){
			$(linkObj).unbind().click(function(){
				jQuery('#axZm-product-link').click();
			});
		}
	};
	
	jQuery.fn.make360gif = function(prodID, w, h, thumbW, thumbH, sTurn){
		jQuery.ajax({
			url: axZm_BaseUrl + '/axZm/axZmSpinGif.php',
			data: 'prodID='+prodID+'&w='+w+'&h='+h+'&thumbW='+thumbW+'&thumbH='+thumbH+'&sTurn='+sTurn,
			dataType: 'script',
			cache: false,
			success: function (data){
				
			},
			complete: function () {

			}
		});		
	};
	
	jQuery.fn.axZmSwitchImage = function(mode, lnk, zData){
		if (jQuery.axZmCurrenZoomMod == mode){
			jQuery.fn.axZm.zoomSwitch(lnk);
		} else {
			jQuery.axZm = {};
			jQuery('#axZm-load-container').empty().html('Reloading, please wait...');
			ajaxZoom.path = axZm_BaseUrl+'/axZm/'; // Path to the axZm folder
			
			if (mode == 'image360'){
				ajaxZoom.parameter = '3dDir='+zData; // Needed parameter
			}else{
				ajaxZoom.parameter = 'zoomData='+jQuery.zoomData; // Needed parameter
				ajaxZoom.parameter += '&zoomFile='+zData;
			}
			
			ajaxZoom.parameter += '&example=magento&displayModus='+jQuery.axZmDisplayModus+'&embedWidth='+jQuery.axZmEmbedWidth+'&embedHeight='+jQuery.axZmEmbedHeight;

			if (jQuery.zoomTouchStyle == 'yes'){
				ajaxZoom.parameter += '&zoomTouchStyle=yes';
			}
			
			ajaxZoom.opt = {
				onLoad: function(){
				
				},
				onSpinPreloadEnd: function(){

				},
				onCropEnd: function(){

				}
			}

			ajaxZoom.divID = 'axZm-load-container'; // The id of the Div where ajax-zoom has to be inserted	
			ajaxZoomLoad();

		}
		
		if (mode == 'imageZoom'){
			jQuery.axZmCurrenZoomMod = 'imageZoom';
		}else{
			jQuery.axZmCurrenZoomMod = 'image360';
		}
	}
	
	jQuery.fn.axZmFull360 = function(path){
		window.scrollTo(0,1);
		var w = jQuery(window).width();
		var h = jQuery(window).height();
		
		var ifr = jQuery('<iframe />').
		attr('scrolling', 'no').
		attr('frameborder', 0).
		attr('width', w).
		//attr('height', h-30).
		attr('height', h).
		attr('id', 'axZmFullFrame360').
		attr('src', axZm_BaseUrl+'/axZm/zoomLoad360.php?example=magento&3dDir='+path);
		
		/*
		var closeDiv = jQuery('<div />').css({
			width:  w,
			height: 30,
			backgroundColor: 'red'
		}).bind('click', function(){
			jQuery('#axZmFullScr360').remove();
		});
		*/
		
		var np = navigator.platform;
		var mobs = false;
		if (((np.indexOf("iPhone") != -1) || (np.indexOf("iPod") != -1) ||(np.indexOf("iPad") != -1))){mobs = true;}

		var o = jQuery('<div />').attr('id', 'axZmFullScr360').css({
			position: mobs ? 'absolute' : 'fixed',
			top: (mobs ? parseInt(jQuery(window).scrollTop()) : 0),
			width: w,
			height:  h,
			left: 0,
			zIndex: 999999999
		});
		
		//closeDiv.appendTo(o);
		ifr.appendTo(o);
		o.appendTo('body');
		
		jQuery(window).bind('resize', jQuery.fn.axZmFullResize);
	}
	
	jQuery.fn.axZmFullResize = function(){
		var w = jQuery(window).width();
		var h = jQuery(window).height();

		jQuery('#axZmFullScr360').css({
			width: w,
			height: h
		});
		
		jQuery('#axZmFullFrame360').
		attr('width', w).
		attr('height', h);
	}
	
	// jQuery 1.4.2 bugfix ie unload Ticket #6452
	if (jQuery.browser.msie) {
		$(window).unload(function(){
			for ( var id in jQuery.cache ) {
				var item = jQuery.cache[ id ];
				if ( item.handle ) {
					if ( item.handle.elem === window ) {
						for ( var type in item.events ) {
							if ( type !== "unload" ) {
								// Try/Catch is to handle iframes being unloaded, see #4280
								try {
									jQuery.event.remove( item.handle.elem, type );
								} catch(e) {}
							}
						}
					} else {
						// Try/Catch is to handle iframes being unloaded, see #4280
						try {
							jQuery.event.remove( item.handle.elem );
						} catch(e) {}
					}
				}
			}  
		});
	}
})(jQuery);

var closeAxZmFull360 = function(){
	jQuery('#axZmFullScr360').remove();
	jQuery(window).unbind('resize', jQuery.fn.axZmFullResize);
}
