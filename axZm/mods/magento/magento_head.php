<?php 
/**
* Plugin: jQuery AJAX-ZOOM, Magento PHP helper file: magento_head.php
* Copyright: Copyright (c) 2010 Vadim Jacobi
* License Agreement: http://www.ajax-zoom.com/index.php?cid=download
* Version: 3.3.0
* Date: 2011-08-03
* URL: http://www.ajax-zoom.com
* Description: jQuery AJAX-ZOOM plugin - adds zoom & pan functionality to images and image galleries with javascript & PHP
* Description this file: include this file into head.phtml of your magento theme 'app/design/frontend/[your_interface]/[your_theme]/template/page/html/head.phtml'
* Documentation: http://www.ajax-zoom.com/index.php?cid=docs
*/

if (Mage::registry('product'))
{

$axZm_BaseUrl = str_replace('\\','/',dirname($_SERVER["SCRIPT_NAME"]));
if ($axZm_BaseUrl == '/' || $axZm_BaseUrl == '\\'){$axZm_BaseUrl='';}
?>

<link rel="stylesheet" href="<?php echo $axZm_BaseUrl;?>/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.2.6.css" type="text/css">
<link rel="stylesheet" href="<?php echo $axZm_BaseUrl;?>/axZm/axZm.css" type="text/css">
<style type="text/css" media="screen"> 
.zoomLogHolder{width: 70px;}
</style>

<script type="text/javascript">jQuery.noConflict();</script>

<script type="text/javascript">var axZm_BaseUrl = '<?php echo $axZm_BaseUrl;?>';</script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/jquery.axZm.js"></script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/mods/magento/magento_axZm.js"></script>
<script type="text/javascript" src="<?php echo $axZm_BaseUrl;?>/axZm/plugins/demo/jquery.fancybox/jquery.fancybox-1.2.6.js"></script>

<?php 
}
?>