/**
* Plugin: jQuery AJAX-ZOOM, Magento JS helper file: magento_axZm.js
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
		
		if ($('#axZm-img').attr('src') == url){return false;}
		
		if ($.axZmLoading){
			 $.axZmBreak = true;
		}
		
		$.axZmLoading = true;
				
		$('#axZm-product-link').attr('id','axZm-product-link_old').css({zIndex: 1, position: 'absolute'});
		$('#axZm-img').attr('id','axZm-img_old');

		var loadImg = new Image();

		$('<DIV />').attr('id', 'axZmLoadingMsg').css({
			width: (parseInt($('#axZm-product-image').width())),
			height: 15,
			padding: 5,
			position: 'absolute',
			/*backgroundColor: '#FFFFFF',*/
			opacity: 0,
			zIndex: 99
		}).html('Loading...').prependTo('#axZm-product-image').fadeTo('fast', 0.75);

		
		$(loadImg).load(function(){
			$('#axZmLoadingMsg').fadeTo(fadeInSpeed, 0).remove();
			
			var link = $('<a />').attr('id', 'axZm-product-link').css({display: 'block', opacity: 0, position: 'absolute', zIndex: 2});
			$('<img>').attr('src',url).attr('id', 'axZm-img').appendTo(link);
			
			if (!$.axZmBreak){
				$(link).prependTo('#axZm-product-image').fadeTo(fadeInSpeed, 1, function(){
					$('#axZm-product-link_old').remove();
					$('#axZm-img_old').remove();
					$(this).css({cursor: 'pointer'});
					jQuery.fn.zoomImage(orgImage, linkObj, spin);
				});
			}else{
				$.axZmBreak = false;
			}
			
			$.axZmLoading = false;
			
		}).attr('src', url);
	};
	
	// Add AJAX-ZOOM in fancybox to the switched image
	jQuery.fn.zoomImage = function(orgImage, linkObj, spin){
		function isObject(x) {
			return (typeof x === 'object') && !(x instanceof Array) && (x !== null);
		}

		if (spin){
			// Query string 360 spin
			var alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&example=magento&3dDir='+orgImage;
			jQuery.spin360Loaded = true;
		}else{
			// Query string zoom
			var alink = axZm_BaseUrl + '/axZm/zoomLoad.php?zoomLoadAjax=1&example=magento&zoomFile='+orgImage;
			if (jQuery.zoomData){alink += '&zoomData='+jQuery.zoomData;}	
			jQuery.spin360Loaded = false;
		}

		$('#axZm-img').attr('alt','').attr('title','');

		$('#axZm-product-link').attr('href', alink).unbind().click(function(e){
			jQuery.fn.axZm.openFullScreen('/axZm/', alink);
			e.preventDefault();
			return false;
		});
		
		if ((typeof linkObj === 'object') && !(linkObj instanceof Array) && (linkObj !== null)){
			$(linkObj).unbind().click(function(){
				jQuery('#axZm-product-link').click();
			});
		}
	};
	
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
