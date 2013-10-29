var $j=jQuery.noConflict();
$j(document).ready(function() {
	
	$j("#discount-coupon-form .coupons").live("click",function(){
		$j(".overlay").show();
		$j(".wraperOverlayContent").show();
	});
	$j(".closeLink span").click(function(){
		$j(".overlay").hide();
		$j(".wraperOverlayContent").hide();
	});
	
	$j(".overlay").click(function(){
		$j(this).hide();
		$j(".wraperOverlayContent").hide();
	});	
});