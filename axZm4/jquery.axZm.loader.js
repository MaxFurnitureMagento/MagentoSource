/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.loader.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.2
* Date: 2013-02-20
* URL: http://www.ajax-zoom.com
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

	// Inject js file
	function loadJS(jsFile){
		var js = document.createElement('script');
		js.setAttribute("type","text/javascript");
		js.setAttribute('src', ajaxZoom.path+jsFile);
		document.getElementsByTagName("head")[0].appendChild(js);			
	}
	
	//  Check, if jquery is loaded
	if (typeof jQuery == 'undefined'){
		loadJS('plugins/jquery-1.7.2.min.js');
	}
	
	if (typeof ajaxZoom == 'undefined'){
		alert('var ajaxZoom is not defined!');
		return;
	}

	function wait(){
		if (waitJquery != 'undefined'){clearTimeout(waitJquery);}
		
		if (typeof jQuery != 'undefined'){
			var url = ajaxZoom.path + 'zoomLoad.php';
			var parameter = 'zoomLoadAjax=1&'+ajaxZoom.parameter;
			
			var axZmFileLoadSuccess = function(){
				jQuery.ajax({
					url: url,
					data: parameter,
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
					},
					error: function(a){
						var status = a.status, 
							statusText = a.statusText, 
							returnStr = 'Error. Please contact AJAX-ZOOM support!';
						
						if (status == 403 || status == 500){
							returnStr = 'An error '+status+' ('+statusText+') was returned from the server! \
							This means that the file /axZm/zoomLoad.php encountered an error while processing. \
							Possible reasons are: \
							<ul>\
							';
							if (parameter.indexOf('./') != -1){
								returnStr += '<li>.htaccess rule does not allow to pass relative paths ('+parameter+') over query string, try with absolute path.</li>';
							}
							returnStr += '<li>Ioncube loader is not installed properly or is not running.</li>';
							returnStr += '<li>You have chmod /axZm directory and/or php files in it to some high value, so they are not executed because of server security settings.</li>';
							returnStr += '</ul>';
							returnStr += 'Found a different reason? Please report it to AJAX-ZOOM support. If nothing else helps please contact the support as well.';
						} else if (status == 404){
							returnStr = 'An error '+status+' ('+statusText+') was returned from the server! \
							Please make sure that ajaxZoom.path ('+ajaxZoom.path+') the path to "/axZm" directory is set properly! \
							';
						}
						
						jQuery('#'+ajaxZoom.divID).html('<div style="min-width: 300px; padding: 10px; font-size: 14px; background-color: #FFFFFF; color: #000000">'+returnStr+'</div>');
					}
				});
			};
			
			if (jQuery.isFunction(jQuery.fn.axZm)){
				axZmFileLoadSuccess();
			}else{
				jQuery.ajax({
					url: ajaxZoom.path + 'jquery.axZm.js',
					dataType: 'script',
					cache: true,
					success: function(){
						axZmFileLoadSuccess();
					}
				});				
			}

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

if (typeof ajaxZoom != 'undefined'){
	// Trigger immediately
	if (ajaxZoom.trigger){

		ajaxZoomLoad();
	} 
	
	// Do not do anything, trigger loading AJAX-ZOOM manually
	else if (ajaxZoom.readyToTrigger){
		 
	} else {
		ajaxZoomLoadEvent(window, 'load', ajaxZoomLoad);
	}
} else {
	// Some people inclide this file in head
	ajaxZoomLoadEvent(window, 'load', ajaxZoomLoad);
}