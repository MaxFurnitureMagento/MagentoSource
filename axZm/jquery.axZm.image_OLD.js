/**
* Plugin: jQuery AJAX-ZOOM, jquery.axZm.image.js
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

/*
// Example to embed AJAX-ZOOM into other domain with javascript
<script type="text/javascript">
var zoomUrl = {
	path: 'http://www.ajax-zoom.com/examples/example1.php',
	parameter: 'zoomDir=trasportation&zoomFile=mustang_1.jpg&example=16&iframe=1',
	width: 482,
	height: 370,
	containerCss: 'margin: 0px 10px 10px 0px; float: left;',
	descrHeight: 20,
	descrCssClass: 'descr',
	descrText: 'Some text'
}
</script>
<script src="http://www.ajax-zoom.com/axZm/jquery.axZm.image.js" type="text/javascript"></script>
*/

document.write("<DIV style=\"overflow:hidden; width:"+zoomUrl.width+"px; height:"+(zoomUrl.height+zoomUrl.descrHeight)+"px; "+zoomUrl.containerCss+"\"");
document.write("<DIV class='"+zoomUrl.descrCssClass+"'>"+zoomUrl.descrText+"</DIV>");
document.write("<iframe src='"+zoomUrl.path+"?"+zoomUrl.parameter+"' width='"+zoomUrl.width+"' height='"+zoomUrl.height+"' scrolling='no' frameBorder='0'></iframe>");
document.write("</DIV");