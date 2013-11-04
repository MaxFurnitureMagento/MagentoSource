/**
* Plugin: jQuery AJAX-ZOOM, Magento JS helper file: xtc_axZm.js
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

;(function($) {
	
	// Switch images function
	jQuery.fn.rollImage = function (url, orgImage, fadeInSpeed, linkObj, spin){
		if ($('#axZm-img').attr('src') == url || $('#axZm-product-link').css('opacity') != 1){return;}

		var pw = parseInt($('#axZm-product-link').parent().width());
		var ph = parseInt($('#axZm-product-link').parent().height());
		
		$('#axZm-img').attr('id','axZm-img_old');
		$('#axZm-product-link').attr('id','axZm-product-link_old').css({zIndex: 1, position: 'absolute'});
		
		var ow = parseInt($('#axZm-img_old').width());
		var oh = parseInt($('#axZm-img_old').height());


		// Loading
		var loading = $('<div />').css({
			position: 'absolute',
			padding: 3,
			zIndex: 4,
			width: pw
		}).html('Loading...').appendTo('#axZm-product-image');

		var loadImg = new Image();
		
		$(loadImg).load(function(){
			var w = this.width;
			var h = this.height;
			
			if (loading !== undefined){$(loading).remove();}

			var link = $('<a />').attr('id', 'axZm-product-link').css({width: pw, height: ph, display: 'block', opacity: 0, position: 'absolute', zIndex: 2});
			$('<img>').attr('src',url).attr('id', 'axZm-img').css({
				position: 'absolute',
				left: (Math.round((pw-w)/2)),
				top: (Math.round((ph-h)/2)),
				zIndex: 1
			}).appendTo(link);

			if (ow > w){
				var helpCss = {
					position: 'absolute',
					zIndex: 3,
					backgroundColor: '#FFFFFF',
					width: Math.round((pw-w)/2),
					height: ph,
					opacity: 0
				};
				var helpDivL = $('<div />').css(helpCss).appendTo('#axZm-product-image');
				var helpDivR = $('<div />').css(helpCss).css('left', (pw - helpCss.width)).appendTo('#axZm-product-image');
				$(helpDivL).fadeTo(fadeInSpeed,1);
				$(helpDivR).fadeTo(fadeInSpeed,1);
			}
			
			if (oh > h){
				var helpCss1 = {
					position: 'absolute',
					zIndex: 3,
					backgroundColor: '#FFFFFF',
					height: Math.round((ph-h)/2),
					width: pw,
					opacity: 0
				};				
				var helpDivT = $('<div />').css(helpCss1).appendTo('#axZm-product-image');
				var helpDivB = $('<div />').css(helpCss1).css('top', (ph - helpCss1.height)).appendTo('#axZm-product-image');
				$(helpDivT).fadeTo(fadeInSpeed,1);
				$(helpDivB).fadeTo(fadeInSpeed,1);				
			}

			$(link).appendTo('#axZm-product-image').fadeTo(fadeInSpeed, 1, function(){
				if (ow > w){
					setTimeout(function(){
						if (helpDivL !== undefined){$(helpDivL).remove();}
						if (helpDivR !== undefined){$(helpDivR).remove();}					
					},100);
				}

				if (oh > h){
					setTimeout(function(){
						if (helpDivT !== undefined){$(helpDivT).remove();}
						if (helpDivB !== undefined){$(helpDivB).remove();}					
					},100);
				}

				$('#axZm-product-link_old').remove();
				$(this).css({cursor: 'pointer'});
				jQuery.fn.zoomImage(orgImage, linkObj, spin);
			});
		}).attr('src', url);
	}
	
	// Add AJAX-ZOOM in fancybox to the switched image
	jQuery.fn.zoomImage = function(orgImage, linkObj, spin){
		function isObject(x) {
			return (typeof x === 'object') && !(x instanceof Array) && (x !== null);
		}
		
		if (spin){
			// Query string 360 spin
			var alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&example=xtc&3dDir='+orgImage;
			jQuery.spin360Loaded = true;
		}else{
			// Query string zoom
			var alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&example=xtc&zoomFile='+orgImage;
			if (jQuery.zoomData){alink += '&zoomData='+jQuery.zoomData;}	
			jQuery.spin360Loaded = false;
		}

		$('#axZm-img').attr('alt','').attr('title','');
		$('#axZm-product-link').attr('href', alink).unbind().fancybox({
			padding				: 10,
			overlayShow			: (jQuery.browser.msie ? false : true), // overlay slows down performance in ie
			overlayOpacity		: 0.5,
			overlayColor		: '#000000',
			zoomSpeedIn			: 0,
			zoomSpeedOut		: 100,
			easingIn			: "swing",
			easingOut			: "swing",
			hideOnContentClick	: false, // Important
			centerOnScroll		: false,
			imageScale			: true,
			autoDimensions		: true,
			callbackOnShow		: function(){
				jQuery.fn.axZm(); // Important callback after loading
			}
		});
		
		if ((typeof linkObj === 'object') && !(linkObj instanceof Array) && (linkObj !== null)){
			$(linkObj).unbind().click(function(){
				jQuery('#axZm-product-link').click();
			});
		}
	}
	
	jQuery.fn.make360gif = function(prodID, w, h, thumbW, thumbH, sTurn){
		$.ajax({
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
