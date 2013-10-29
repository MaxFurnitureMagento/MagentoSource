//-----------------------------------------------------------------------------
jQuery.noConflict();
jQuery(document).ready(cofamedia_splash);
var cofamedia_splash_t, cofamedia_splash_current, cofamedia_splash_count, cofamedia_splash_width, cofamedia_splash_paused;
//-----------------------------------------------------------------------------
function cofamedia_splash()
{
	jQuery(".cofamedia-splash-container .first").addClass("active");
	
	var id = jQuery(".cofamedia-splash-container .active").attr('id').replace(/^splash-item-/, "");;
	cofamedia_splash_current = id;
	cofamedia_splash_count = jQuery("[id^=splash-item]").length;
	
	cofamedia_splash_width = jQuery(".cofamedia-splash-container .items").outerWidth();
	cofamedia_splash_height = jQuery(".cofamedia-splash-container .items").outerHeight();
	jQuery("[id^=splash-item-]").css('width', cofamedia_splash_width+"px");
	jQuery("[id^=splash-item-]").css('height', cofamedia_splash_height+"px");
	
	if(cofamedia_splash_count == 1)
		{
			jQuery(".cofamedia-splash-container .thumbnails").css("display", "none");
			return;
		}
	
	if((cofamedia_splash_trigger == "click") || (cofamedia_splash_trigger == "click_stop"))
		{
			jQuery("[id^=splash-thumbnail-]").click(cofamedia_splash_click);
		}
	else
		{
			jQuery("[id^=splash-thumbnail-]").mouseenter(cofamedia_splash_enter);
			jQuery("[id^=splash-thumbnail-]").mouseleave(cofamedia_splash_out);
		}
	
	if(cofamedia_splash_show_pause == '1')
		{
			cofamedia_splash_paused = false;
			jQuery("#cofamedia-splash-pause").click(cofamedia_splash_pause);
		}
	
	cofamedia_splash_timer_start();
}
//-----------------------------------------------------------------------------
function cofamedia_splash_timer_start()
{
	if(cofamedia_splash_show_progress == '1')
		{
			jQuery("#cofamedia-splash-progressbar .bar").css('width', '0px');
			jQuery("#cofamedia-splash-progressbar .bar").clearQueue();
			var bar_width = jQuery("#cofamedia-splash-progressbar").innerWidth();
			jQuery("#cofamedia-splash-progressbar .bar").animate( {
																															width:bar_width+"px"
																															},
																															cofamedia_splash_interval
																														);
		}
	cofamedia_splash_t = setTimeout(cofamedia_splash_tick, cofamedia_splash_interval);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_tick()
{
	var id = cofamedia_splash_current;
	if(++id > cofamedia_splash_count) id = 1;
	cofamedia_splash_rotate(id);
	cofamedia_splash_timer_start();
}
//-----------------------------------------------------------------------------
function cofamedia_splash_rotate(id)
{
	cofamedia_splash_reset_css();
	var animations = cofamedia_splash_animations.split(',');
			
	var random;
	do(random = Math.floor(Math.random()*10))
	while(!animations[random])
		animation = animations[random];
	
	switch(animation)
		{
			case "slide-left": cofamedia_splash_slide_left(id); break;
			case "slide-right": cofamedia_splash_slide_right(id); break;
			case "slide-top": cofamedia_splash_slide_top(id); break;
			case "slide-bottom": cofamedia_splash_slide_bottom(id); break;
			case "fade": cofamedia_splash_fade(id); break;
		}
	
	jQuery("[id^=splash-thumbnail-]").removeClass("active");
	jQuery("[id=splash-thumbnail-"+id+"]").addClass("active");
	
	cofamedia_splash_current = id;
}
//-----------------------------------------------------------------------------
function cofamedia_splash_fade(id)
{
	jQuery("[id^=splash-item-]").fadeOut(cofamedia_splash_animation_speed);
	jQuery("[id=splash-item-"+id+"]").css("left", "0px");
	jQuery("[id=splash-item-"+id+"]").css("top", "0px");
	jQuery("[id=splash-item-"+id+"]").fadeIn(cofamedia_splash_animation_speed);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_slide_left(id)
{
	jQuery("[id^=splash-item-"+id+"]").css("left", cofamedia_splash_width+"px");
	jQuery("[id^=splash-item-"+id+"]").css("top", "0px");
	jQuery("[id^=splash-item-"+id+"]").show();
	
	jQuery("[id^=splash-item-"+cofamedia_splash_current+"]").animate( {
																															left:"-="+cofamedia_splash_width+""
																															},
																															cofamedia_splash_animation_speed,
																															function() { $(this).hide() }
																														);
	
	jQuery("[id^=splash-item-"+id+"]").animate( {
																								left:"-="+cofamedia_splash_width+""
																							},
																							cofamedia_splash_animation_speed);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_slide_right(id)
{
	jQuery("[id^=splash-item-"+id+"]").css("right", cofamedia_splash_width+"px");
	jQuery("[id^=splash-item-"+id+"]").css("top", "0px");
	jQuery("[id^=splash-item-"+id+"]").show();
	
	jQuery("[id^=splash-item-"+cofamedia_splash_current+"]").animate( {
																															right:"-="+cofamedia_splash_width
																															},
																															cofamedia_splash_animation_speed,
																															function() { $(this).hide() }
																														);
	
	jQuery("[id^=splash-item-"+id+"]").animate( {
																								right:"-="+cofamedia_splash_width
																							},
																							cofamedia_splash_animation_speed);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_slide_top(id)
{
	jQuery("[id^=splash-item-"+id+"]").css("top", cofamedia_splash_height+"px");
	jQuery("[id^=splash-item-"+id+"]").css("left", "0px");
	jQuery("[id^=splash-item-"+id+"]").show();
	
	jQuery("[id^=splash-item-"+cofamedia_splash_current+"]").animate( {
																															top:"-="+cofamedia_splash_height+""
																															},
																															cofamedia_splash_animation_speed,
																															function() { $(this).hide() }
																														);
	
	jQuery("[id^=splash-item-"+id+"]").animate( {
																								top:"-="+cofamedia_splash_height+""
																							},
																							cofamedia_splash_animation_speed);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_slide_bottom(id)
{
	jQuery("[id^=splash-item-"+id+"]").css("bottom", cofamedia_splash_height+"px");
	jQuery("[id^=splash-item-"+id+"]").css("left", "0px");
	jQuery("[id^=splash-item-"+id+"]").show();
	
	jQuery("[id^=splash-item-"+cofamedia_splash_current+"]").animate( {
																															bottom:"-="+cofamedia_splash_height+""
																															},
																															cofamedia_splash_animation_speed,
																															function() { $(this).hide() }
																														);
	
	jQuery("[id^=splash-item-"+id+"]").animate( {
																								bottom:"-="+cofamedia_splash_height+""
																							},
																							cofamedia_splash_animation_speed);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_reset_css()
{
	jQuery("#splash-debug").html('reset');
	jQuery("[id^=splash-item-]").css("left", "auto");
	jQuery("[id^=splash-item-]").css("top", "auto");
	jQuery("[id^=splash-item-]").css("right", "auto");
	jQuery("[id^=splash-item-]").css("bottom", "auto");
}
//-----------------------------------------------------------------------------
function cofamedia_splash_enter(event)
{
	var obj = event.target;
	var id = obj.id.replace(/^splash-\w+-/, "");
	cofamedia_splash_paused = false;
	cofamedia_splash_pause();
	if(cofamedia_splash_current != id)
		cofamedia_splash_rotate(id);
}
//-----------------------------------------------------------------------------
function cofamedia_splash_out()
{
	cofamedia_splash_pause();
	// cofamedia_splash_timer_start();
}
//-----------------------------------------------------------------------------
function cofamedia_splash_click(event)
{
	var obj = event.target;
	var id = obj.id.replace(/^splash-\w+-/, "");
	cofamedia_splash_paused = false;
	cofamedia_splash_pause();
	if(id != cofamedia_splash_current) cofamedia_splash_rotate(id);
	if(cofamedia_splash_trigger != "click_stop") cofamedia_splash_pause();
}
//-----------------------------------------------------------------------------
function cofamedia_splash_pause()
{
	if(cofamedia_splash_paused)
		{
			cofamedia_splash_paused = false;
			jQuery("#cofamedia-splash-pause").removeClass('paused');
			cofamedia_splash_timer_start();
		}
	else
		{
			cofamedia_splash_paused = true;
			
			if(cofamedia_splash_show_progress == '1')
				{
					jQuery("#cofamedia-splash-progressbar .bar").clearQueue();
					jQuery("#cofamedia-splash-progressbar .bar").stop();
				}
			
			jQuery("#cofamedia-splash-pause").addClass('paused');
			clearTimeout(cofamedia_splash_t);
		}
}
//-----------------------------------------------------------------------------
