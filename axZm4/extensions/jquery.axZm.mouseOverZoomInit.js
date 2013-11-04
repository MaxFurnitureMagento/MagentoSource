/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.mouseOverZoomInit.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

/**
This file is needed to init mouseOverZoom (jquery.axZm.mouseOverZoom.js)
*/

(function($) {
	
	$.fn.mouseOverZoomInit = function(options){  
		
		// Defaults
		var defaults = {
			axZmPath: 'auto', // Path to AJAX-ZOOM, e.g. /zoomTest/axZm/
			divID: '', // DIV for mouseover zoom
			galleryDivID: '', // DIV id of the gallery, set to false to disable gallery
			
			// Objecct containing absolte paths to the master images, optional with titles. Start with 1, not 0
			// Your master image can be as big as you want, it never loads into cache
			// You can also protect the directory with .htaccess or other http access restrictions.
			// Alternatively you can link to your static images with these keys: 
			// "zoom" - big mouseover image, "preview" - preview image and "thumb" - image for the gallery. 
			// "img" - your master image is needed anyway to open AJAX-ZOOM on click
			images: {},
			firstImageToLoad: 1, // image that should be loaded at first
			
			galleryCarousel: true, // use jCarousel on gallery thumbnails or not
			galleryCarouselParam: {}, // jCarousel parametrs if used, for more info see http://sorgalla.com/projects/jcarousel/

			preloadMouseOverImages: false, // preload all preview and mouse over images, possible values: false, true, 'oneByOne' 
			width: 'auto', // width of the mouse image or 'auto'
			height: 'auto', // height of the mouse image or 'auto'
			mouseOverZoomWidth: 1200, // max width of the image that will be shown in the zoom window
			mouseOverZoomHeight: 1200, // max height of the image that will be shown in the zoom window
			ajaxZoomOpenMode: 'fullscreen', // at time only fullscreen, will be extended
			data: {},
			fullScreenApi: true, // try to open AJAX-ZOOM at browsers fullscreen mode, possible on modern browsers except IE and mobile
			axZmCallBacks: {}, // AJAX-ZOOM has several callbacks, http://www.ajax-zoom.com/index.php?cid=docs#onBeforeStart
			thumbW: 50, // gallery thumb width
			thumbH: 80, // gallery thumb height
			quality: 90, // quality of the preview image
			qualityZoom: 80, // quality of the zoom image shown in the flyout window
			example: 'mouseOverExtension', // configuration set which is passed to ajax-zoom
			adjustContainer: false, // auto adjust parent container height
			
			mouseOverZoomParam: {
				position: 'right', // Position of the flyout zoom window, possible values: 'inside', 'top', 'right', 'bottom', 'left'
				posAutoInside: 150, // applies when width (left, right) or height (top, bottom) of zoom window are less than this px value (zoomWidth || zoomHeight are set to auto); if zoomWidth || zoomHeight are fixed, applies when zoom window is out of page border
				autoFlip: 200, //  flip right to left and bottom to top if less than int px or false to disable
				biggestSpace: false, // Overrides position option and instantly chooses the direction, disables autoFlip; playes nicely when zoomWidth and zoomHeight are set to 'auto'
				zoomFullSpace: false, // Uses full screen height (does not align to the map / disables adjustY) if position is right or left || uses full screen width (does not align to the map / disables adjustX) if position is top or bottom
				zoomWidth: 'auto', // width of the zoom window e.g. 540 or 'auto'
				zoomHeight: 'auto', // height of the zoom window e.g. 375, or 'auto'!
				autoMargin: 15, // if zoomWidth or zoomHeight are set to 'auto', the margin to the edge of the screen
				adjustX: 15, // horizontal margin of the zoom window
				adjustY: -1, // vertical margin of the zoom window
				lensOpacity: 0.30, // opacity of the selector lens
				zoomAreaBorderWidth: 1, // border thickness of the zoom window
				galleryFade: 300, // speed of inner fade or false
				shutterSpeed: 150, // speed of shutter fadein or false; applies only if image proportions are different from container
				showFade: 300, // speed of fade in for mouse over
				hideFade: 300, // speed of fade out for mouse over
				smoothMove: 2, // int bigger 1 indicates smoother movements; set 0 to disable
				tint: false, // color value around the lens or false
				tintOpacity: 0.5, // opacity of the area around the lens when tint is not false
				showTitle: true, // bool, enable / disable title on zoom window
				titleOpacity: 0.5, // opacity of the title container
				titlePosition: 'top', // position of the title, top or bottom
				cursorPositionX: 0.5, // cursor over lens  horizontal offset, 0.5 is middle
				cursorPositionY: 0.55, // cursor over lens vertical offset, 0.5 is middle
				loading: true, // display loading information, CSS .mouseOverLoading
				loadingMessage: 'Loading...', // Loading message, not needed, can be just the spinner - see below
				loadingWidth: 90, // width of loading container 
				loadingHeight: 20, // height of loading container 
				loadingOpacity: 1.0, // opacity of the loading container (the transparent background is set via png image on default, see css class)
				onLoad: function(){},
				onImageChange: function(){},
				onMouseOver: function(){},
				onMouseOut: function(){},
				spinner: true, // use ajax loading spinner without gif files etc.
				spinnerParam: { // spinner options, for more info see: http://fgnass.github.com/spin.js/
					lines: 11, // The number of lines to draw
					length: 3, // The length of each line
					width: 3, // The line thickness
					radius: 4, // The radius of the inner circle
					corners: 1, // Corner roundness (0..1)
					rotate: 0, // The rotation offset
					color: '#FFFFFF', // #rgb or #rrggbb
					speed: 1, // Rounds per second
					trail: 90, // Afterglow percentage
					shadow: false, // Whether to render a shadow
					hwaccel: false, // Whether to use hardware acceleration
					className: 'spinner', // The CSS class to assign to the spinner
					zIndex: 2e9, // The z-index (defaults to 2000000000)
					top: 0, // Top position relative to parent in px
					left: 1 // Left position relative to parent in px
				}
			}
		};
		
		// Override defaults by user setting
		var op = $.extend(true, {}, defaults, options);
		
		// In case fired in jquery plugin way - $('#mouseOverZoomContainer').mouseOverZoomInit(options);
		if ($(this).attr('id')){
			op.divID = $(this).attr('id');
		}

		// Try to get /axZm path instantly
		if (op.axZmPath == 'auto'){
			op.axZmPath = $.fn.axZm.installPath();
		}

		// Helper function
		function getl(sep, str){
			return str.substring(str.lastIndexOf(sep)+1);
		}
		
		// Helper function
		function getf(sep, str){
			var extLen = getl(sep, str).length;
			return str.substring(0, (str.length - extLen - 1));
		}
		
		// Get instance
		var divID = $('#'+op.divID);
		
		// Check existence
		if (divID.length <= 0){
			alert('Element with ID '+op.divID+' was not found.');
			return;
		}

		// Define width and height of the small image. 
		// Can be set explicitly or determined instantly
		var w = op.width == 'auto' ? divID.innerWidth() : op.width;
		var h = op.height == 'auto' ? divID.innerHeight() : op.height;
		
		// Adjust width/height of the intance div
		var prevW = divID.width();
		var prevH = divID.height();
		
		if (op.width != 'auto'){
			divID.css('width', w);
		}
		
		if (op.height != 'auto'){
			divID.css('height', h);
		}
		
		var uniqueZoomID = 'zoom_' + Math.floor(Math.random()*999999) + new Date().getTime();
		
		var showGallery = false;
		if (op.galleryDivID && $('#'+op.galleryDivID).length > 0){
			showGallery = true;
		}
		
		// Function to generate the paths to the images
		var imageSrc = function(num, kind){
			var imageServer = op.axZmPath+'zoomLoad.php?previewPic=';
			var path = '';
			if (op.images[num]){
				var v = op.images[num];
				if (v[kind]){
					path = v[kind];
				}else{
					if (kind == 'zoom'){
						path = imageServer+getl('/', v['img'])+'&previewDir='+getf('/', v['img'])+'&qual='+op.qualityZoom+'&width='+op.mouseOverZoomWidth+'&height='+op.mouseOverZoomHeight;
					} else if (kind == 'preview'){
						path = imageServer+getl('/', v['img'])+'&previewDir='+getf('/', v['img'])+'&qual='+op.quality+'&width='+w+'&height='+h;
					} else if (kind == 'thumb'){
						path = imageServer+getl('/', v['img'])+'&previewDir='+getf('/', v['img'])+'&qual='+op.quality+'&width='+op.thumbW+'&height='+op.thumbH;
					}
					path = path.split(' ').join('%20');
				}
			}
			return path;
		};

		// Preload images
		if (op.preloadMouseOverImages){
			op.mouseOverZoomParam.preloadGalleryImages = function(){
				if (op.preloadMouseOverImages == 'oneByOne'){
					preloadMouseOverImage = function(num){
						if (op.images[num]){
							
							$('<img>').attr('src', imageSrc(num, 'preview'));
							$('<img>').load(function(){
								if (op.images[num+1]){
									preloadMouseOverImage(num+1);
								}
							}).attr('src', imageSrc(num, 'zoom'));
						}
					}
					preloadMouseOverImage(1);
				}else{
					var nnn = 1;
					$.each(op.images, function(k, v){
						
						if (k != op.firstImageToLoad){
							nnn ++;
							setTimeout(function(){
								$('<img>').attr('src', imageSrc(num, 'zoom'));
								$('<img>').attr('src', imageSrc(num, 'preview'));
							}, nnn * 100);
						}
					});
				}
			};
		}
		
		var initMouseOverZoom = function(num){
			var a = $('<a />')
			.addClass('mouseOverImg')
			.data('zoomid', num)
			.attr({
				href: imageSrc(num, 'zoom'),
				id: uniqueZoomID
			});
			
			$('<img>').attr({
				'src': imageSrc(num, 'preview'),
				'title': op.images[num]['title'],
				'border': 0
			}).css('opacity', 0).appendTo(a);
			
			a.appendTo(divID);
		};
	
		// Show gallery
		if (showGallery){
			$('#'+op.galleryDivID).parent().css({
				display: 'block'
			});
		}

		// Cutom function to what happens when the user clicks on the lens
		jQuery.fn[uniqueZoomID] = function(event){
			var thisZoomID = $('#'+uniqueZoomID).data('zoomid');
	
			window.fullScreenStartSplash = {
				enable: true,
				className: false,
				opacity: 1
			};
			
			var callbacks = {
				onFullScreenClose: function(){
					// select the proper image if gallery image has been switched in AJAX-ZOOM
					$('#'+op.galleryDivID +' .mouseOverThumbA:eq('+($.axZm.zoomID-1)+')').trigger('click');
					if (op.galleryCarousel && $.isFunction($.fn.jcarousel)){
						$('#'+op.galleryDivID).jcarousel('scroll', $.axZm.zoomID-1);
					} 
				}
			};			
			
			if (op.ajaxZoomOpenMode == 'fullscreen'){
				$.extend(callbacks, op.axZmCallBacks);
				$.fn.axZm.openFullScreen(op.axZmPath, 'zoomID='+thisZoomID+'&zoomData='+allImages+'&example='+op.example, callbacks, 'window', op.fullScreenApi);
			} 
			
			else if ($.isFunction(op.ajaxZoomOpenMode)){
				if (op.data.axZmCallbacks){
					$.extend(op.data.axZmCallbacks, op.axZmCallBacks);
				}else{
					op.data.axZmCallbacks = $.extend(true, {}, op.axZmCallBacks);
				}
				$.extend(op.data.axZmCallbacks, callbacks);
				op.data.zoomID = thisZoomID;
				
				op.ajaxZoomOpenMode(op.data);
			}else{
				alert('Sorry, but at this point there\nare no other mods than (AJAX-ZOOM) fullscreen.');
			}
			
			$('.mouseOverTrap').trigger('mouseout');
		};

		
		// Put thumbnails (generated by AJAX-ZOOM) into jcarousel container
		// All thumbnails are created on the fly while loading first time
		var allImages = '';
		
		// Gallery
		if (showGallery){
			if (!op.galleryCarousel){
				var galParent = $('#'+op.galleryDivID).parent();
				$('#'+op.galleryDivID).remove();
			}
			$.each(op.images, function(k, v){
					allImages += v['img'] + '|';
					
					var li = $('<li />');
					
					var a = $('<a />')
					.addClass('mouseOverThumbA')
					.attr({
						id: uniqueZoomID+'_'+k,
						href: imageSrc(k, 'zoom'),
						title: op.images[k]['title']
					})
					.data('relZoom', uniqueZoomID)
					.data('zoomid', k)
					.data('smallImage', imageSrc(k, 'preview'))
					.bind('click', function(){
						$('#'+uniqueZoomID).data('previd', $('#'+uniqueZoomID).data('zoomid'));
						$('#'+uniqueZoomID).data('zoomid', k);
					});
	
					var div = jQuery('<div />').addClass('mouseOverThumbImage')
					.css('backgroundImage', 'url('+imageSrc(k, 'thumb')+')');
					$(div).appendTo(a);
					if (op.galleryCarousel){
						li.append(a).appendTo('#'+op.galleryDivID);
					}else{
						a.appendTo(galParent);
					}
			});
		
			allImages = allImages.substring(0, allImages.length-1);
			
		}
		
		$(document).ready(function () {
			initMouseOverZoom(op.firstImageToLoad);
			$('#'+uniqueZoomID).axZmMouseOverZoom(op.mouseOverZoomParam);
			
			$.each(op.images, function(k, v){
				$('#'+uniqueZoomID+'_'+k).axZmMouseOverZoom(op.mouseOverZoomParam);
			});

			// Init jcarousel -> http://sorgalla.com/projects/jcarousel/
			if (op.galleryCarousel && $.isFunction($.fn.jcarousel)){
				var defaultJcarousel = {start: op.firstImageToLoad};
				$.extend(defaultJcarousel, op.galleryCarouselParam);
				
				// Init jcarousel
				$('#'+op.galleryDivID).jcarousel(defaultJcarousel);
				
				/*
				start: 1, // The index of the item to start with.
				vertical: false, // Specifies wether the carousel appears in horizontal or vertical orientation. Changes the carousel from a left/right style to a up/down style carousel.
				rtl: false, // Specifies wether the carousel appears in RTL (Right-To-Left) mode.
				offset: 1, // The index of the first available item at initialisation.
				size: null, // The number of total items.
				scroll: 3, // The number of items to scroll by.
				visible: null, // If passed, the width/height of the items will be calculated and set depending on the width/height of the clipping, so that exactly that number of items will be visible.
				animation: 'normal', // The speed of the scroll animation as string in jQuery terms ("slow" or "fast") or milliseconds as integer (See jQuery Documentation). If set to 0, animation is turned off.
				easing: 'swing', // The name of the easing effect that you want to use
				auto: 0, // Specifies how many seconds to periodically autoscroll the content. If set to 0 (default) then autoscrolling is turned off. 
				wrap: null, // Specifies whether to wrap at the first/last item (or both) and jump back to the start/end. Options are "first", "last", "both" or "circular" as string. If set to null, wrapping is turned off (default).
				initCallback: null, // JavaScript function that is called right after initialisation of the carousel. Two parameters are passed: The instance of the requesting carousel and the state of the carousel initialisation (init, reset or reload).
				reloadCallback: null, 
				itemLoadCallback: null, 
				itemFirstInCallback: null, 
				itemFirstOutCallback: null, 
				itemLastInCallback: null, 
				itemLastOutCallback: null, 
				itemVisibleInCallback: null,
				itemVisibleOutCallback: null,
				buttonNextHTML: '<div></div>', // The HTML markup for the auto-generated next button. If set to null, no next-button is created.
				buttonPrevHTML: '<div></div>', // The HTML markup for the auto-generated prev button. If set to null, no prev-button is created.
				buttonNextEvent: 'click', // Specifies the event which triggers the next scroll.
				buttonPrevEvent: 'click', // Specifies the event which triggers the prev scroll.
				buttonNextCallback: null, // JavaScript function that is called when the state of the 'next' control is changing. The responsibility of this method is to enable or disable the 'next' control. Three parameters are passed: The instance of the requesting carousel, the control element and a flag indicating whether the button should be enabled or disabled.
				buttonPrevCallback: null, // JavaScript function that is called when the state of the 'previous' control is changing. The responsibility of this method is to enable or disable the 'previous' control. Three parameters are passed: The instance of the requesting carousel, the control element and a flag indicating whether the button should be enabled or disabled.
				itemFallbackDimension: null // If, for some reason, jCarousel can not detect the width of an item, you can set a fallback dimension (width or height, depending on the orientation) here to ensure correct calculations.
				*/
			}
			
			if (op.adjustContainer){
				var hhh = 10;
				setTimeout(function(){
					$.each($('#'+op.divID).parent().children(), function(){
						hhh += $(this).outerHeight();
					});
					$('#'+op.divID).parent().css('height', hhh);
				}, 100);
				
			}
			
		});
		
	};

})(jQuery);
