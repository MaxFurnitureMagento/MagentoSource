//-----------------------------------------------------------------------------
var jq = jQuery.noConflict();
jq(document).ready(cm_onsale);
var onsale_current = 1;
//-----------------------------------------------------------------------------
function cm_onsale()
{
	if(onsale_page_count <= 1) return;

	jq('#onsale-left').click(cm_onsale_left);
	jq('#onsale-right').click(cm_onsale_right);
	jq('#onsale-controls .dots').click(cm_onsale_dot);
	jq('#onsale-dot-0').addClass('active');
}
//-----------------------------------------------------------------------------
function cm_onsale_dot()
{
	var page = parseInt(jq(this).attr('id').replace(/^onsale-dot-/, '')) + 1;
	onsale_current = page;
	cm_onsale_goto(page);
}
//-----------------------------------------------------------------------------
function cm_onsale_left()
{
	if(onsale_current == 1) return;
	
	onsale_current--;
	cm_onsale_goto(onsale_current);
}
//-----------------------------------------------------------------------------
function cm_onsale_right()
{
	if(onsale_current == onsale_page_count) return;
	
	onsale_current++;
	cm_onsale_goto(onsale_current);
}
//-----------------------------------------------------------------------------
function cm_onsale_goto(page)
{
	var real_page = page - 1;
	var item_width = jq('#onsale-product-grid td.item').outerWidth();
	var width = real_page * (onsale_per_page * item_width);
	jq('#onsale-product-grid').animate( {
																				left:"-"+width+"px"
																			},
																			cm_onsale_animation_speed
																		);
	jq('#onsale-controls .dots').removeClass('active');
	jq('#onsale-dot-'+real_page).addClass('active');
}
//-----------------------------------------------------------------------------