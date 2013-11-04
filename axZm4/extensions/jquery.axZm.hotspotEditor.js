/**
* Plugin: jQuery AJAX-ZOOM, $.axZm.hotspotEditor.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/


;(function($){
	
	// Function to submit the JSON to a file which will save the information about hotspots e.g. to a javascript file
	var saveHotspotJS = function(){
		$("#saveHotspotJS").submit(function(event){
			// stop form from submitting normally
			event.preventDefault(); 
				
			// get some values from elements on the page
			var Form = $(this),
				jsCode = $('#allHotspotsCode').val(),
				fileName = $('#jsFileName').val(),
				password = $('#jsFilePass').val(),
				url = Form.attr('action');

			if (!fileName || !jsCode){
				$('#hotspotSaveToJSresults').empty().html('Please export your current settings to the formfield above and define a filename where this js should be saved!');
				return;
			}
			
			$('#hotspotSaveToJSresults').empty().html('Saving...');
			
			// Send the data using post and put the results in a div
			$.post(url, {jsCode: jsCode, fileName: fileName, password: password},
				function(data){
					$('#hotspotSaveToJSresults').empty().append(data);
				}
			).fail(function() { 
				$('#hotspotSaveToJSresults').empty().append(url + ' was not found on this server. Please adjust the path to saveHotspotJS.php in the action attribute of the form with id "saveHotspotJS"');
			});
		});	
	},
	
	// Returns the name of selected hotspot
	getHotspotSelector = function(id){
		id = id || 'hotspotSelector';
		return $('#'+id+' option:selected').val();
	},
	
	// Get type of a js var
	realTypeOf = function(v) {
		if (typeof(v) == "object") {
		if (v === null) return "null";
		if (v.constructor == (new Array).constructor) return "array";
		if (v.constructor == (new Date).constructor) return "date";
		if (v.constructor == (new RegExp).constructor) return "regex";
			return "object";
		}
		return typeof(v);
	},
	
	// Set current selected hotspot
	updateHotspotSelector = function(id){
		if ($.axZm && $.axZm.hotspots){
			id = id || 'hotspotSelector';
			var selected = getHotspotSelector(id);
			$('#'+id+' option').remove();
			$.each($.axZm.hotspots, function(hotspotName){
				var newOption = $('<option />');
				$('#'+id).append(newOption.val(hotspotName).html(hotspotName));
				if (hotspotName == selected){newOption.attr('selected', true);}
			});
			$.fn.axZm.hotspotsDraggable();
			colorSelectedHotspot();
			updateHotspotTooltip();
		}
	},
	
	// Safe hotspot tooltip changed directly into
	// Todo: expend tooltip to other available options
	saveHotspotTooltip = function(){
		var name = getHotspotSelector();
		if (name && $.axZm && $.axZm.hotspots){
			
			var toolTipHtml = $('#hotspot_toolTipHtml').val();
				toolTipHtml = toolTipHtml.replace(/(\r\n|\n|\r)/gm,'');
			
			$.axZm.hotspots[name]['altTitle'] = $('#hotspot_altTitle').val();
			$.axZm.hotspots[name]['toolTipTitle'] = $('#hotspot_toolTipTitle').val();
			$.axZm.hotspots[name]['toolTipHtml'] = toolTipHtml;
			$.axZm.hotspots[name]['toolTipGravity'] = $('#hotspot_toolTipGravity option:selected').val();
			
			$.axZm.hotspots[name]['toolTipWidth'] = $('#hotspot_toolTipWidth').val();
			$.axZm.hotspots[name]['toolTipHeight'] = $('#hotspot_toolTipHeight').val();
			
			var toolTipGravFixed = $('#hotspot_toolTipGravFixed').axZmGetPropType('checked'),
				toolTipAutoFlip = $('#hotspot_toolTipAutoFlip').axZmGetPropType('checked');
			
			$.axZm.hotspots[name]['toolTipGravFixed'] = (toolTipGravFixed && toolTipGravFixed != 'undefined') ? true : false;
			$.axZm.hotspots[name]['toolTipAutoFlip'] = (toolTipAutoFlip && toolTipAutoFlip != 'undefined') ? true : false;
			
			$.fn.axZm.initHotspots();
			setTimeout(function(){
				$.fn.axZm.hotspotsDraggable();
				colorSelectedHotspot();
			},100);
		}
	},
	
	// Set form fields values depending on loaded $.axZm.hotspots object
	updateHotspotTooltip = function(){
		var name = getHotspotSelector();
		if (name && $.axZm && $.axZm.hotspots && $.axZm.hotspots[name]){
			var altTitle = $.axZm.hotspots[name]['altTitle'],
				toolTipTitle = $.axZm.hotspots[name]['toolTipTitle'],
				toolTipHtml = $.axZm.hotspots[name]['toolTipHtml'],
				toolTipGravity = $.axZm.hotspots[name]['toolTipGravity'],
				toolTipWidth = $.axZm.hotspots[name]['toolTipWidth'],
				toolTipHeight = $.axZm.hotspots[name]['toolTipHeight'],
				toolTipGravFixed = $.axZm.hotspots[name]['toolTipGravFixed'],
				toolTipAutoFlip = $.axZm.hotspots[name]['toolTipAutoFlip'];
			
			$('#hotspot_altTitle').val(altTitle || '');
			$('#hotspot_toolTipTitle').val(toolTipTitle || '');
			$('#hotspot_toolTipHtml').val(toolTipHtml || '');
			$('#hotspot_toolTipGravity').val(toolTipGravity || 'left');
			
			$('#hotspot_toolTipWidth').val(toolTipWidth || 250);
			$('#hotspot_toolTipHeight').val(toolTipHeight || 120);
			$('#hotspot_toolTipGravFixed').attr('checked', toolTipGravFixed);
			$('#hotspot_toolTipAutoFlip').attr('checked', toolTipAutoFlip);
		}
	},
		
	// Format JSON object to display in textarea
	FormatJSON = function(oData, sIndent, placebo){
		if (placebo){
			sHTML = $.toJSON(oData);
			sHTML = sHTML.replace(/\"function/g, 'function');
			sHTML = sHTML.replace(/}\"/g, '}');
			return sHTML;
		}
		
		if (arguments.length < 2) {
			var sIndent = "";
		}
		
		var sIndentStyle = "	";
		var sDataType = realTypeOf(oData);
	
		// open object
		if (sDataType == "array") {
			if (oData.length == 0) {
				return "[]";
			}
			var sHTML = "[";
		} else {
			var iCount = 0;
			$.each(oData, function() {
				iCount++;
				return;
			});
			if (iCount == 0) { // object is empty
				return "{}";
			}
			var sHTML = "{";
		}
	
		// loop through items
		var iCount = 0;
		$.each(oData, function(sKey, vValue) {
			if (iCount > 0) {
				sHTML += ",";
			}
			if (sDataType == "array") {
				sHTML += ("\n" + sIndent + sIndentStyle);
			} else {
				sHTML += ("\n" + sIndent + sIndentStyle + "\"" + sKey + "\"" + ": ");
			}
	
			// display relevant data type
			switch (realTypeOf(vValue)) {
				case "array":
					break;
				case "object":
					sHTML += FormatJSON(vValue, (sIndent + sIndentStyle));
					break;
				case "boolean":
					sHTML += vValue.toString();
					break;
				case "number":
					sHTML += vValue.toString();
					break;
				case "null":
					sHTML += "null";
					break;
				case "string":
					sHTML += ("\"" + vValue.replace('"', '&#34;') + "\"");
					break;
				default:
					sHTML += ("TYPEOF: " + typeof(vValue));
			}
			iCount++;
		});
	
		if (sDataType == "array") {
			sHTML += ("\n" + sIndent + "]");
		} else {
			sHTML += ("\n" + sIndent + "}");
		}
	
		sHTML = sHTML.replace(/\"function/g, 'function');
		sHTML = sHTML.replace(/}\"/g, '}');
		sHTML = sHTML.replace(/;    /g, '; ');
	
		return sHTML;
	},
		
	// Change hotspots color after it is selected, 
	// spin to the first available frame with that hotspot and 
	// center it if zoomed
	colorSelectedHotspot = function(){
		if (!$.axZm || !$.axZm.icon || !$.axZm.hotspots){return;}
		setTimeout(function(){
			var selectedHotspot = getHotspotSelector(),
				defaultGreen = $.axZm.icon+'hotspot64_green.png',
				defaultRed = $.axZm.icon+'hotspot64_red.png',
				display = false;
	
			$.each($.axZm.hotspots, function(name, values){
				var currentImage = $('#axZmHotspotImg_'+name).attr('src');
				
				if (currentImage){
					// apply only on default hotspots
					if (currentImage.indexOf(defaultGreen) || currentImage.indexOf(defaultRed)){
						if (selectedHotspot == name){
							$('#axZmHotspotImg_'+name).attr('src', defaultRed);
							if ($('#axZmHotspot_'+name).css('display') != 'none'){
								display = true;
							}
						}else{
							$('#axZmHotspotImg_'+name).attr('src', defaultGreen);
						}
					}
				}
			});
			
			// Spin to a frame where hotspot is visible
			if ($.axZm.spinPreloaded && !display){
				if ($.axZm.hotspots &&  $.axZm.hotspots[selectedHotspot]){
					$.each($.axZm.hotspots[selectedHotspot]['position'], function(frame, values){
						$.fn.axZm.spinTo(parseInt(frame), false, false, function(){
							var hotspotPos = $.fn.axZm.getHotspotPosition(selectedHotspot);
							$.fn.axZm.panTo({x1: hotspotPos.left, y1: hotspotPos.top});
						});
						return false;
					});
				}
			}else{
				var hotspotPos = $.fn.axZm.getHotspotPosition(selectedHotspot);
				$.fn.axZm.panTo({x1: hotspotPos.left, y1: hotspotPos.top});			
			}
			
			updateHotspotTooltip();
			
		}, 150);
		
	},
	
	// Load a different set of 3D/360 or 2D
	changeAxZmContentPHP = function(){
		if (typeof ajaxZoom !== 'undefined'){
			if ($.axZm.spinPreloading){
				alert('Please wait...');
				return;
			}
			var pathToLoad = $('#pathToLoad').val(),
				hotspotFileToLoad = $('#hotspotFileToLoad').val();
	
			if (pathToLoad){
				$.fn.axZm.spinStop();
				
				var myCallBacks = {
					 
					onLoad: function(){ // onSpinPreloadEnd
						$.axZm.spinReverse = false;
						// Load hotspots over this function... or just define $.axZm.hotspots here and trigger $.fn.axZm.initHotspots(); after this.
	
						$.fn.axZm.loadHotspotsFromJsFile(hotspotFileToLoad, false, function(){
							// This is just for hotspot editor
							if (typeof updateHotspotSelector !== 'undefined' ){
								setTimeout(updateHotspotSelector, 200);
							}				
						});
					}
					 
				};
				
				// Load / Reload AJAX-ZOOM
				function ajaxZoomReload(){
					var url = ajaxZoom.path + 'zoomLoad.php';
					var qStringPar = '3dDir';
					
					// check path to load and change 3dDir= to zoomData=
					if (/\.(gif|png|jp(e|g|eg)|tif|tiff|psd|bmp)((#|\?).*)?$/i.test(pathToLoad)){
						qStringPar = 'zoomData';
					}
					
					
					var parameter = 'zoomLoadAjax=1&example=hotSpotEdit&'+qStringPar+'='+pathToLoad;
					 
					$.ajax({
						url: url,
						data: parameter, // important
						dataType: 'html',
						cache: false,
						success: function (data){
							if ($.isFunction($.fn.axZm) && data){
								$('#'+ajaxZoom.divID).html(data);
							}
						},
						complete: function () {
							if ($.isFunction($.fn.axZm)){
								$.fn.axZm(myCallBacks);
							}
						}
					});
				}
				ajaxZoomReload();
			}
		}
	};
	
	$.aZhSpotEd = {
		changeAxZmContentPHP: changeAxZmContentPHP,
		colorSelectedHotspot: colorSelectedHotspot,
		saveHotspotJS: saveHotspotJS,
		saveHotspotTooltip: saveHotspotTooltip,
		updateHotspotTooltip: updateHotspotTooltip,
		FormatJSON: function(a, b, c){return FormatJSON(a,b,c)},
		getHotspotSelector: function(a){return getHotspotSelector(a)},
		realTypeOf: function(a){return realTypeOf(a)},
		updateHotspotSelector: function(a){updateHotspotSelector(a)}
	};

	
	$.fn.axZmGetPropType = function(type){
		var oldJQuery = parseFloat($.fn.jquery) < 1.6;
		 
		if (oldJQuery){
			return $(this).attr(type);
		}else{
			return $(this).prop(type);
		}
	};
	
	$(document).bind("ready", function(){
		if ($.isFunction($.aZhSpotEd.saveHotspotJS)){
			$.aZhSpotEd.saveHotspotJS();
		}
	});

})(jQuery);