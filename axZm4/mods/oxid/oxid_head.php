<?php 
/**
* Plugin: jQuery AJAX-ZOOM, Oxid PHP helper file: oxid_head.php
* Copyright: Copyright (c) 2010-2013 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 4.0.1
* Date: 2013-02-18
* URL: http://www.ajax-zoom.com
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

$axZm_BaseUrl = str_replace('\\','/',dirname($_SERVER["SCRIPT_NAME"]));
if ($axZm_BaseUrl == '/' || $axZm_BaseUrl == '\\'){$axZm_BaseUrl='';}
?>

<link rel="stylesheet" href="<?php echo $axZm_BaseUrl;?>/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.2.6.css" type="text/css">
<link rel="stylesheet" href="<?php echo $axZm_BaseUrl;?>/axZm/axZm.css" type="text/css">
<style type="text/css" media="screen">.zoomLogHolder{width: 70px;}</style>

<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/plugins/jquery-1.7.2.min.js"></script>
<script type="text/javascript">jQuery.noConflict();</script>

<script type="text/javascript">var axZm_BaseUrl = '<?php echo $axZm_BaseUrl;?>';</script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/jquery.axZm.js"></script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/mods/oxid/oxid_axZm.js"></script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.2.6.js"></script>

<?php

?>