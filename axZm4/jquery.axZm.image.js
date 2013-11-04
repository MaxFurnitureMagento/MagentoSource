/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.image.js
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

/**
// Example to embed AJAX-ZOOM into other domain with javascript
<div id='myImage123'></div>
<script type="text/javascript">
var zoomUrl = {
	placeholderID: 'myImage123',
	path: 'http://www.ajax-zoom.com/examples/example1.php', // change it to your website
	parameter: 'zoomDir=trasportation&zoomFile=mustang_1.jpg&example=16&iframe=1',
	width: 482,
	height: 370,
	containerCss: {margin: '0px 10px 10px 0px', float: 'left'},
	descrHeight: 20,
	descrCssClass: 'descr',
	descrText: 'Some text'
}
</script>
<script src="http://www.ajax-zoom.com/axZm/jquery.axZm.image.js" type="text/javascript"></script>
*/

var tempAxZmCont = jQuery('<div />').css({overflow: 'hidden', width: zoomUrl.width, height: zoomUrl.height+zoomUrl.descrHeight}).css(zoomUrl.containerCss);
var tempAxZmDescr = jQuery('<div />').addClass(zoomUrl.descrCssClass).html(zoomUrl.descrText);
var tempAxZmIframe = jQuery('<iframe />').attr('src', zoomUrl.path+'?'+zoomUrl.parameter).attr('width', zoomUrl.width).attr('height', zoomUrl.height).attr('scrolling', 'no').attr('frameBorder', '0');

tempAxZmDescr.appendTo(tempAxZmCont);
tempAxZmIframe.appendTo(tempAxZmCont);
jQuery('#'+zoomUrl.placeholderID).replaceWith(tempAxZmCont);
