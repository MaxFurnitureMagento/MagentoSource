(function ($) {

AjaxSolr.CurrentSearchWidget = AjaxSolr.AbstractWidget.extend({
  init: function(){
  	this.selectedIndex = -1;
  },
  beforeRequest: function (){
  	$('.search_left').html('<img src="'+dir_template+'javascript/ajax-solr/loading.gif" alt="loading" />');
  },
  afterRequest: function () {

    var fq = this.manager.store.values('fq');
    var q = this.manager.store.params.q.value;
    q = q.replace("autosuggest:","");
    if(typeof FacetSearch.response.response === 'undefined'){
    	var nq = '';
    	for(i=0; i<fq.length; i++){
    		if(nq.length == 0)
    			nq = fq[i];
    		else nq += ' OR ' + fq[i];
    	}
    	FacetSearch.store.addByValue('q', 'autosuggest:'+q);
    	FacetSearch.store.addByValue('json.nl', 'map');
    	FacetSearch.store.addByValue('rows', '50');
    	FacetSearch.store.addByValue('fl', 'autosuggest,name,products_image,products_id');
    	FacetSearch.store.addByValue('fq', nq);
    	FacetSearch.store.addByValue('timestamp', new Date());
    	FacetSearch.doRequest();
    }

    if(FacetSearch.response.response.docs.length >= 1){
    	$('.search_left').empty();
    	//reset height of search_left and search_right div
    	$('.search_left').css({height: ""});
	    $('.search_right').css({height: ""});
    	var docs = [];
    	var pimages = [];
    	var pids = [];
    	var i = 0;
    	$.each(FacetSearch.response.response.docs, function(key, value){
    		if($.inArray(value.name,docs) < 0 && i < 15){
    			docs.push(value.name);
    			pimages.push(value.products_image);
    			var val = 'name_exact:"'+value.name+'"';
    			div = $('<div class="search_line" title="'+value.name+'" onmouseover="$(this).addClass(\'selected\');" onmouseout="$(this).removeClass(\'selected\');" >'+fnFormatResult(value.name, q)+'</div>');
	    		div.bind('click', function(){
	    			$('#searchterm').val(val);
	    			$('#box_search').submit();
	    		});
	    		$('.search_left').append(div);
	    		i ++;
    		}
    	});
    	//synchronize height of search_left and search_right div
    	synheight($('.search_left'), $('.search_right'));
    	//set search bottom
    	var bot = $('.search_bottom');
    	var div = '';
    	for(i=0;i<Math.min(5,pimages.length);i++){
    		div += '<div class="search_product_saleoff" onclick="redirect(\''+product_link+'?products_id='+pids[i]+'\');"><div class="picture"><img src="'+dir_thumb_images+pimages[i]+'" onerror="this.src=\''+dir_thumb_images+'noimage.gif\'" alt="'+docs[i]+'" width="80px" height="80px" /></div></div>';
    	}
    	bot.empty();
    	bot.append(div);
    	$('.search_div').append(bot);
    }else{
    	$('.search_left').html('There is no product');
    }
  }
});

})(jQuery);
