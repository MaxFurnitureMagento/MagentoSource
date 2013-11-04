/*
 * FancyBox - jQuery Plugin
 * simple and fancy lightbox alternative
 *
 * Copyright (c) 2009 Janis Skarnelis
 * Examples and documentation at: http://fancybox.net
 * 
 * Version: 1.2.6 (16/11/2009)
 * Requires: jQuery v1.3+
 * 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
 
(function(a){a.fn.fixPNG=function(){return this.each(function(){var c=a(this).css("backgroundImage");if(c.match(/^url\(["']?(.*\.png)["']?\)$/i)){c=RegExp.$1;a(this).css({backgroundImage:"none",filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod="+(a(this).css("backgroundRepeat")=="no-repeat"?"crop":"scale")+", src='"+c+"')"}).each(function(){var g=a(this).css("position");g!="absolute"&&g!="relative"&&a(this).css("position","relative")})}})};var i,b,n=false,j=new Image,
s,t=1,u=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,v=null,l=a.browser.msie&&a.browser.version.substr(0,1)==6&&!window.XMLHttpRequest,x=l||a.browser.msie&&a.browser.version.substr(0,1)==7;a.fn.fancybox=function(c){function g(){a("#fancy_right, #fancy_left, #fancy_close, #fancy_title").hide();var d=b.itemArray[b.itemCurrent].href;if(d.match("iframe")||i.className.indexOf("iframe")>=0){a.fn.fancybox.showLoading();p('<iframe id="fancy_frame" onload="jQuery.fn.fancybox.showIframe()" name="fancy_iframe'+Math.round(Math.random()*
1E3)+'" frameborder="0" hspace="0" src="'+d+'"></iframe>',b.frameWidth,b.frameHeight)}else if(d.match(/#/)){var e=window.location.href.split("#")[0];e=d.replace(e,"");e=e.substr(e.indexOf("#"));p('<div id="fancy_div">'+a(e).html()+"</div>",b.frameWidth,b.frameHeight)}else if(d.match(u)){j=new Image;j.src=d;if(j.complete)m();else{a.fn.fancybox.showLoading();a(j).unbind().bind("load",function(){a("#fancy_loading").hide();m()})}}else{a.fn.fancybox.showLoading();a.get(d,function(f){a("#fancy_loading").hide();
tempData=f.replace(/<script{1}.*>*<\/script>/i,"");a("<DIV />").attr("id","fancyTemp").css({display:"none",minWidth:150,minHeight:150}).html(tempData).appendTo("body");var h=a("#fancyTemp").outerWidth(),k=a("#fancyTemp").outerHeight();a("#fancyTemp").remove();p('<div id="fancy_ajax">'+f+"</div>",h,k)})}}function m(){var d=j.width,e=j.height,f=b.padding*2+40,h=b.padding*2+60,k=a.fn.fancybox.getViewport();if(b.imageScale&&(d>k[0]-f||e>k[1]-h)){f=Math.min(Math.min(k[0]-f,d)/d,Math.min(k[1]-h,e)/e);d=
Math.round(f*d);e=Math.round(f*e)}p('<img alt="" id="fancy_img" src="'+j.src+'" />',d,e)}function o(){if(b.itemArray.length-1>b.itemCurrent){var d=b.itemArray[b.itemCurrent+1].href||false;if(d&&d.match(u)){objNext=new Image;objNext.src=d}}if(b.itemCurrent>0)if((d=b.itemArray[b.itemCurrent-1].href||false)&&d.match(u)){objNext=new Image;objNext.src=d}}function p(d,e,f){n=true;var h=b.padding;if(x||v){a("#fancy_content")[0].style.removeExpression("height");a("#fancy_content")[0].style.removeExpression("width")}if(h>
0){e+=h*2;f+=h*2;a("#fancy_content").css({top:h+"px",right:h+"px",bottom:h+"px",left:h+"px",width:"auto",height:"auto"});if(x||v){a("#fancy_content")[0].style.setExpression("height","(this.parentNode.clientHeight - "+h*2+")");a("#fancy_content")[0].style.setExpression("width","(this.parentNode.clientWidth - "+h*2+")")}}else a("#fancy_content").css({top:0,right:0,bottom:0,left:0,width:"100%",height:"100%"});if(a("#fancy_outer").is(":visible")&&e==a("#fancy_outer").width()&&f==a("#fancy_outer").height())a("#fancy_content").fadeOut("fast",
function(){a("#fancy_content").empty().append(a(d)).fadeIn("normal",function(){q()})});else{h=a.fn.fancybox.getViewport();var k=f+60>h[1]?h[3]:h[3]+Math.round((h[1]-f-60)*0.5),r={left:e+40>h[0]?h[2]:h[2]+Math.round((h[0]-e-40)*0.5),top:k,width:e+"px",height:f+"px"};if(a("#fancy_outer").is(":visible"))a("#fancy_content").fadeOut("normal",function(){a("#fancy_content").empty();a("#fancy_outer").animate(r,b.zoomSpeedChange,b.easingChange,function(){a("#fancy_content").append(a(d)).fadeIn("normal",function(){q()})})});
else if(b.zoomSpeedIn>0&&b.itemArray[b.itemCurrent].orig!==undefined){a("#fancy_content").empty().append(a(d));e=b.itemArray[b.itemCurrent].orig;f=a.fn.fancybox.getPosition(e);a("#fancy_outer").css({left:f.left-20-b.padding+"px",top:f.top-20-b.padding+"px",width:a(e).width()+b.padding*2,height:a(e).height()+b.padding*2});if(b.zoomOpacity)r.opacity="show";a("#fancy_outer").animate(r,b.zoomSpeedIn,b.easingIn,function(){q()})}else{a("#fancy_content").hide().empty().append(a(d)).show();a("#fancy_outer").css(r).fadeIn("normal",
function(){q()})}}}function y(){if(b.itemCurrent!==0){a("#fancy_left, #fancy_left_ico").unbind().bind("click",function(d){d.stopPropagation();b.itemCurrent--;g();return false});a("#fancy_left").show()}if(b.itemCurrent!=b.itemArray.length-1){a("#fancy_right, #fancy_right_ico").unbind().bind("click",function(d){d.stopPropagation();b.itemCurrent++;g();return false});a("#fancy_right").show()}}function q(){if(a.browser.msie){a("#fancy_content")[0].style.removeAttribute("filter");a("#fancy_outer")[0].style.removeAttribute("filter")}y();
o();a(document).bind("keydown.fb",function(e){if(e.keyCode==27&&b.enableEscapeButton)a.fn.fancybox.close();else if(e.keyCode==37&&b.itemCurrent!==0){a(document).unbind("keydown.fb");b.itemCurrent--;g()}else if(e.keyCode==39&&b.itemCurrent!=b.itemArray.length-1){a(document).unbind("keydown.fb");b.itemCurrent++;g()}});b.hideOnContentClick&&a("#fancy_content").click(a.fn.fancybox.close);b.overlayShow&&b.hideOnOverlayClick&&a("#fancy_overlay").bind("click",a.fn.fancybox.close);b.showCloseButton&&a("#fancy_close").bind("click",
a.fn.fancybox.close).show();if(typeof b.itemArray[b.itemCurrent].title!=="undefined"&&b.itemArray[b.itemCurrent].title.length>0){var d=a("#fancy_outer").position();a("#fancy_title div").text(b.itemArray[b.itemCurrent].title).html();a("#fancy_title").css({top:d.top+a("#fancy_outer").outerHeight()-32,left:d.left+(a("#fancy_outer").outerWidth()*0.5-a("#fancy_title").width()*0.5)}).show()}b.overlayShow&&l&&a("embed, object, select",a("#fancy_content")).css("visibility","visible");a.isFunction(b.callbackOnShow)&&
b.callbackOnShow(b.itemArray[b.itemCurrent]);if(a.browser.msie){a("#fancy_outer")[0].style.removeAttribute("filter");a("#fancy_content")[0].style.removeAttribute("filter")}n=false}var w=a.extend({},a.fn.fancybox.defaults,c),z=this;return this.unbind("click.fb").bind("click.fb",function(){i=this;b=a.extend({},w);if(!n){a.isFunction(b.callbackOnStart)&&b.callbackOnStart();b.itemArray=[];b.itemCurrent=0;if(w.itemArray.length>0)b.itemArray=w.itemArray;else{var d={};if(!i.rel||i.rel==""){d={href:i.href,
title:i.title};d.orig=a(i).children("img:first").length?a(i).children("img:first"):a(i);if(d.title==""||typeof d.title=="undefined")d.title=d.orig.attr("alt");b.itemArray.push(d)}else for(var e=a(z).filter("a[rel="+i.rel+"]"),f=0;f<e.length;f++){d={href:e[f].href,title:e[f].title};d.orig=a(e[f]).children("img:first").length?a(e[f]).children("img:first"):a(e[f]);if(d.title==""||typeof d.title=="undefined")d.title=d.orig.attr("alt");b.itemArray.push(d)}}for(;b.itemArray[b.itemCurrent].href!=i.href;)b.itemCurrent++;
if(b.overlayShow){if(l){a("embed, object, select").css("visibility","hidden");a("#fancy_overlay").css("height",a(document).height())}a("#fancy_overlay").css({"background-color":b.overlayColor,opacity:b.overlayOpacity}).show()}a(window).bind("resize.fb scroll.fb",a.fn.fancybox.scrollBox);g()}return false})};a.fn.fancybox.scrollBox=function(){var c=a.fn.fancybox.getViewport();if(b.centerOnScroll&&a("#fancy_outer").is(":visible")){var g=a("#fancy_outer").outerWidth(),m=a("#fancy_outer").outerHeight(),
o={top:m>c[1]?c[3]:c[3]+Math.round((c[1]-m)*0.5),left:g>c[0]?c[2]:c[2]+Math.round((c[0]-g)*0.5)};a("#fancy_outer").css(o);a("#fancy_title").css({top:o.top+m-32,left:o.left+(g*0.5-a("#fancy_title").width()*0.5)})}l&&a("#fancy_overlay").is(":visible")&&a("#fancy_overlay").css({height:a(document).height()});a("#fancy_loading").is(":visible")&&a("#fancy_loading").css({left:(c[0]-40)*0.5+c[2],top:(c[1]-40)*0.5+c[3]})};a.fn.fancybox.getNumeric=function(c,g){return parseInt(a.curCSS(c.jquery?c[0]:c,g,true))||
0};a.fn.fancybox.getPosition=function(c){var g=c.offset();g.top+=a.fn.fancybox.getNumeric(c,"paddingTop");g.top+=a.fn.fancybox.getNumeric(c,"borderTopWidth");g.left+=a.fn.fancybox.getNumeric(c,"paddingLeft");g.left+=a.fn.fancybox.getNumeric(c,"borderLeftWidth");return g};a.fn.fancybox.showIframe=function(){a("#fancy_loading").hide();a("#fancy_frame").show()};a.fn.fancybox.getViewport=function(){return[a(window).width(),a(window).height(),a(document).scrollLeft(),a(document).scrollTop()]};a.fn.fancybox.animateLoading=
function(){if(a("#fancy_loading").is(":visible")){a("#fancy_loading > div").css("top",t*-40+"px");t=(t+1)%12}else clearInterval(s)};a.fn.fancybox.showLoading=function(){clearInterval(s);var c=a.fn.fancybox.getViewport();a("#fancy_loading").css({left:(c[0]-40)*0.5+c[2],top:(c[1]-40)*0.5+c[3]}).show();a("#fancy_loading").bind("click",a.fn.fancybox.close);s=setInterval(a.fn.fancybox.animateLoading,66)};a.fn.fancybox.close=function(){n=true;a(j).unbind();a(document).unbind("keydown.fb");a(window).unbind("resize.fb scroll.fb");
a("#fancy_overlay, #fancy_content, #fancy_close").unbind();a("#fancy_close, #fancy_loading, #fancy_left, #fancy_right, #fancy_title").hide();__cleanup=function(){a("#fancy_overlay").is(":visible")&&a("#fancy_overlay").fadeOut("fast");a("#fancy_content").empty();b.centerOnScroll&&a(window).unbind("resize.fb scroll.fb");l&&a("embed, object, select").css("visibility","visible");a.isFunction(b.callbackOnClose)&&b.callbackOnClose();n=false};if(a("#fancy_outer").is(":visible")!==false)if(b.zoomSpeedOut>
0&&b.itemArray[b.itemCurrent].orig!==undefined){var c=b.itemArray[b.itemCurrent].orig,g=a.fn.fancybox.getPosition(c);c={left:g.left-20-b.padding+"px",top:g.top-20-b.padding+"px",width:a(c).width()+b.padding*2,height:a(c).height()+b.padding*2};if(b.zoomOpacity)c.opacity="hide";a("#fancy_outer").stop(false,true).animate(c,b.zoomSpeedOut,b.easingOut,__cleanup)}else a("#fancy_outer").stop(false,true).fadeOut("fast",__cleanup);else __cleanup();return false};a.fn.fancybox.build=function(){var c="";c+='<div id="fancy_overlay"></div>';
c+='<div id="fancy_loading"><div></div></div>';c+='<div id="fancy_outer">';c+='<div id="fancy_inner">';c+='<div id="fancy_close"></div>';c+='<div id="fancy_bg"><div class="fancy_bg" id="fancy_bg_n"></div><div class="fancy_bg" id="fancy_bg_ne"></div><div class="fancy_bg" id="fancy_bg_e"></div><div class="fancy_bg" id="fancy_bg_se"></div><div class="fancy_bg" id="fancy_bg_s"></div><div class="fancy_bg" id="fancy_bg_sw"></div><div class="fancy_bg" id="fancy_bg_w"></div><div class="fancy_bg" id="fancy_bg_nw"></div></div>';
c+='<a href="javascript:;" id="fancy_left"><span class="fancy_ico" id="fancy_left_ico"></span></a><a href="javascript:;" id="fancy_right"><span class="fancy_ico" id="fancy_right_ico"></span></a>';c+='<div id="fancy_content"></div>';c+="</div>";c+="</div>";c+='<div id="fancy_title"></div>';a(c).appendTo("body");a('<table cellspacing="0" cellpadding="0" border="0"><tr><td class="fancy_title" id="fancy_title_left"></td><td class="fancy_title" id="fancy_title_main"><div></div></td><td class="fancy_title" id="fancy_title_right"></td></tr></table>').appendTo("#fancy_title");
a.browser.msie&&a(".fancy_bg").fixPNG();if(l){a("div#fancy_overlay").css("position","absolute");a("#fancy_loading div, #fancy_close, .fancy_title, .fancy_ico").fixPNG();a("#fancy_inner").prepend('<iframe id="fancy_bigIframe" src="javascript:false;" scrolling="no" frameborder="0"></iframe>');c=a("#fancy_bigIframe")[0].contentWindow.document;c.open();c.close()}};a.fn.fancybox.defaults={padding:10,imageScale:true,zoomOpacity:true,zoomSpeedIn:0,zoomSpeedOut:0,zoomSpeedChange:300,easingIn:"swing",easingOut:"swing",
easingChange:"swing",frameWidth:560,frameHeight:340,overlayShow:true,overlayOpacity:0.3,overlayColor:"#666",enableEscapeButton:true,showCloseButton:true,hideOnOverlayClick:true,hideOnContentClick:true,centerOnScroll:true,itemArray:[],callbackOnStart:null,callbackOnShow:null,callbackOnClose:null};a(document).ready(function(){v=a.browser.msie&&!a.boxModel;a("#fancy_outer").length<1&&a.fn.fancybox.build()})})(jQuery);