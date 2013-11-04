/**
* Plugin: jQuery AJAX-ZOOM, Oxid JS helper file: oxid_axZm.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

;(function($) {
	
	// Switch images function
	jQuery.fn.rollImage = function (url, orgImage, fadeInSpeed, linkObj){
		
		if ($('#axZm-img').attr('src') == url || $.axZmLoading){return false;}

		if ($.axZmLoading){
			 $.axZmBreak = true;
		}
		
		$.axZmLoading = true;

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
		}).html('Loading...').prependTo('#axZm-product-image');

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
			
			if (!$.axZmBreak){

				$('#axZm_HDL, #axZm_HDR, #axZm_HDT, #axZm_HDB').remove();
				
				if (ow > w){
					var helpCss = {
						position: 'absolute',
						zIndex: 3,
						backgroundColor: '#FFFFFF',
						width: Math.round((pw-w)/2),
						height: ph,
						opacity: 0
					};
					var helpDivL = $('<div />').css(helpCss).attr('id','axZm_HDL').prependTo('#axZm-product-image');
					var helpDivR = $('<div />').css(helpCss).attr('id','axZm_HDR').css('left', (pw - helpCss.width)).prependTo('#axZm-product-image');
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
					var helpDivT = $('<div />').css(helpCss1).attr('id','axZm_HDT').prependTo('#axZm-product-image');
					var helpDivB = $('<div />').css(helpCss1).attr('id','axZm_HDB').css('top', (ph - helpCss1.height)).prependTo('#axZm-product-image');
					$(helpDivT).fadeTo(fadeInSpeed,1);
					$(helpDivB).fadeTo(fadeInSpeed,1);				
				}

				$(link).prependTo('#axZm-product-image').fadeTo(fadeInSpeed, 1, function(){
					if (ow > w){
						setTimeout(function(){
							if (helpDivL !== undefined){$(helpDivL).remove();}
							if (helpDivR !== undefined){$(helpDivR).remove();}					
						},1);
					}
	
					if (oh > h){
						setTimeout(function(){
							if (helpDivT !== undefined){$(helpDivT).remove();}
							if (helpDivB !== undefined){$(helpDivB).remove();}					
						},1);
					}
	
					$('#axZm-product-link_old').remove();
					$(this).css({cursor: 'pointer'});
					jQuery.fn.zoomImage(orgImage, linkObj);
					
				});
				
			}else{
				$.axZmBreak = false;
			}
			
			$.axZmLoading = false;
			
		}).attr('src', url);
	}
	
	// Add AJAX-ZOOM in fancybox to the switched image
	jQuery.fn.zoomImage = function(orgImage, linkObj){
		function isObject(x) {
			return (typeof x === 'object') && !(x instanceof Array) && (x !== null);
		}
		
		// Query string
		var alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&example=oxid&zoomFile='+orgImage;
		if (jQuery.zoomData){alink += '&zoomData='+jQuery.zoomData;}

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
			},
			onComplete : function(){
				jQuery.fn.axZm(); // Important callback after loading
			}
		});
		
		if ((typeof linkObj === 'object') && !(linkObj instanceof Array) && (linkObj !== null)){
			$(linkObj).unbind().click(function(){
				jQuery('#axZm-product-link').click();
			});
		}
	}
	
	jQuery.fn.adjustOxidTemplate = function(adjTemplate, morepics){
		// Adjust some template things with javascript
		if (adjTemplate){
			var posThumbs = $(".axZmThumbs").position();	
			if (posThumbs && posThumbs.top > 0){
				$(".exturls").css('top', Math.round(posThumbs.top + $(".axZmThumbs").height()));
			}
			
			// Remove native [+] zoom link
			$('#test_zoom').remove();
			
			// Set minHeight of the parent container, since images are positioned absolutely
			var exturlsTop = parseInt($(".exturls").css('top'));
			var exturlsHeight = parseInt($(".exturls").css('height'));
			
			var minH = parseInt($('.axZmContainer').parent().css('minHeight'));
			if ((exturlsTop+exturlsHeight) > minH){
				$('.axZmContainer').parent().css('minHeight', exturlsTop+exturlsHeight);
			}
		}
		
		// Remove native thumbs of more images
		if (morepics){
			$('.morepics').remove();
		}
		
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
