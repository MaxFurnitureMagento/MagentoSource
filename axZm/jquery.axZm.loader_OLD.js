/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.loader.js
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

function ajaxZoomLoad(){
	var waitJquery;
	
	// Inject AJAX-ZOOM stylesheet - axZm.css
	var css = document.createElement('link');
	css.setAttribute('type', 'text/css');
	css.setAttribute('rel', 'stylesheet');
	css.setAttribute('href', ajaxZoom.path+'axZm.css');
	document.getElementsByTagName("head")[0].appendChild(css);
	
	// Inject any js file
	function loadJS(jsFile){
		var js = document.createElement('script');
		js.setAttribute("type","text/javascript")
		js.setAttribute('src', ajaxZoom.path+jsFile);
		document.getElementsByTagName("head")[0].appendChild(js);			
	}
	
	//  Check, if jquery is loaded
	if (typeof jQuery == 'undefined'){
		loadJS('plugins/jquery-1.6.1.min.js');
	}

	function wait(){
		if (waitJquery != 'undefined'){clearTimeout(waitJquery);}
		if (typeof jQuery != 'undefined'){
			var url = ajaxZoom.path + 'zoomLoad.php';
			var parameter = 'zoomLoadAjax=1&'+ajaxZoom.parameter;
			jQuery.getScript(ajaxZoom.path + 'jquery.axZm.js', function(){
				jQuery.ajax({
					url: url,
					data: parameter, // important
					dataType: 'html',
					cache: false,
					success: function (data){
						if (jQuery.isFunction(jQuery.fn.axZm) && data){
							jQuery('#'+ajaxZoom.divID).html(data);
						}
					},
					complete: function () {
						if (jQuery.isFunction(jQuery.fn.axZm)){
							jQuery.fn.axZm(ajaxZoom.opt);
						}
					}
				});						
			});
		} else{
			waitJquery = setTimeout(function(){
				wait();
			}, 250);					
		}
	}
	wait();
}

function ajaxZoomLoadEvent(obj, evType, fn){ 
	if (obj.addEventListener){ 
		obj.addEventListener(evType, fn, false); 
		return true; 
	} else if (obj.attachEvent){ 
		var r = obj.attachEvent("on"+evType, fn); 
		return r; 
	} else { 
		return false; 
	} 
}

ajaxZoomLoadEvent(window, 'load', ajaxZoomLoad);