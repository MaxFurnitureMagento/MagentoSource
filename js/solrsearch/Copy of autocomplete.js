/*
 *
 *  Ajax Autocomplete for Prototype, version 1.0.4
 *  (c) 2010 Tomas Kirda
 *
 *  Ajax Autocomplete for Prototype is freely distributable under the terms of an MIT-style license.
 *  For details, see the web site: http://www.devbridge.com/projects/autocomplete/
 *
 */

var Autocomplete = function(el, options){
  this.el = $(el);
  this.id = this.el.identify();
  this.el.setAttribute('autocomplete','off');
  this.suggestions = [];
  this.suggestionsPrice = [];
  this.updateSeachFaces = false;
  this.manager = options.manager;
  this.ajaxBaseUrl = null;
  this.queryFields = null;  
  this.incorrectkeywords = [];
  this.Autocompletemessage = null;
  this.didyoumeantext= '';
  
  this.categoriesFilter = [];
  this.data = [];
  this.badQueries = [];
  this.selectedIndex = -1;
  this.selectedProductId = null;
  this.currentValue = this.el.value;
  this.currentKeyword = null;
  this.intervalId = 0;
  this.cachedResponse = [];
  this.instanceId = null;
  this.onChangeInterval = null;
  this.ignoreValueChange = false;
  this.serviceUrl = options.serviceUrl;
  this.options = {
    autoSubmit:false,
    minChars:1,
    maxHeight:300,
    deferRequestBy:0,
    width:0,
    container:null,
    allowFilter:0,
    displayThumb:0,
  };
  if(options){ Object.extend(this.options, options); }
  if(Autocomplete.isDomLoaded){
    this.initialize();
  }else{
    Event.observe(document, 'dom:loaded', this.initialize.bind(this), false);
  }
};

Autocomplete.instances = [];
Autocomplete.isDomLoaded = false;

Autocomplete.getInstance = function(id){
  var instances = Autocomplete.instances;
  var i = instances.length;
  while(i--){ if(instances[i].id === id){ return instances[i]; }}
};

Autocomplete.highlight = function(value, re){
	value = value.toString();
	return value.replace(re, function(match){ return '<strong>' + match + '<\/strong>' });
};

Autocomplete.prototype = {

  killerFn: null,

  initialize: function() {
    var me = this;
    this.killerFn = function(e) {
      if (!$(Event.element(e)).up('.autocomplete')) {
        me.killSuggestions();
        me.disableKillerFn();
      }
    } .bindAsEventListener(this);

    if (!this.options.width) { this.options.width = this.el.getWidth(); }
    
    //Create a div element
    var div = new Element('div', { style: 'position:absolute;' });
    //Put some children div into parent div
    div.update('<div class="autocomplete-w1" id="solr_search_autocomplete_box" style="display:none"><div id="didyoumean"  style="float:left;display:none" class="txt_suggestion didyoumean" >No results found for <span id="didyoumean_text" style="font-weight:bold"></span> - showing results for <span id="didyoumean_text_2" style="font-weight:bold"></span> instead.</div><div style="clear:both"></div><div><div class="autocomplete" id="solr_'+this.id+'_autocomplete_right' + '" style="display:none;padding:5px; width: 98%"></div><div class="left-side-bar" style="display:none" id="solr_search_autocomplete_left">&nbsp;</div></div><div id="solr_search_closed_button" class="closed-button" style="position:absolute;display:none">&nbsp;</div><div class="solr_search_autocomplete_box_bottom"><span id="solr_search_view_all_link"></span></div></div>');
    
    //Append all div to body tag
    this.options.container = $(this.options.container);
    if (this.options.container) {
      //this.options.container.appendChild(div);
      //this.fixPosition = function() { };
    } else {
      document.body.appendChild(div);
    }
    document.body.appendChild(div);
    //$('didyoumean').hide();
    //Get the div ID
    this.mainContainerId = div.identify();
    this.container = $('solr_'+this.id+'_autocomplete_right');
    this.box = $('solr_search_autocomplete_box');
    this.closebutton = $('solr_search_closed_button');
    this.leftContainer = $('solr_search_autocomplete_left');
    
    //if (this.options.sideBarWidth) { this.leftContainer.setStyle({width:(this.options.sideBarWidth)+'px'}); }
    if (this.options.sideBarWidth) { this.leftContainer.setStyle({width:'100%'}); }
    if (this.options.boxWidth) { this.box.setStyle({width:(this.options.boxWidth)+'px'}); }
    this.box.setStyle({padding:'5px'});
    
    if(this.options.allowFilter == 1){
    	this.leftContainer.show();
    }else{
    	this.leftContainer.remove();
    	this.box.setStyle({width:(this.options.boxWidth - this.options.sideBarWidth)+'px'});
    }
    
    //This function called to set some css attributes to the parent div
    this.fixPosition();
    
    Event.observe(this.el, window.opera ? 'keypress':'keydown', this.onKeyPress.bind(this));
    Event.observe(this.el, 'keyup', this.onKeyUp.bind(this));
    //Event.observe(this.el, 'blur', this.enableKillerFn.bind(this));
    Event.observe(this.el, 'focus', this.fixPosition.bind(this));
    Event.observe(this.closebutton, 'click', this.closeAll.bind(this));
    this.instanceId = Autocomplete.instances.push(this) - 1;
  },
  closeAll: function(){
	this.box.hide();
	this.closebutton.hide();
  	this.container.hide();
  	this.leftContainer.hide();
  },
  hideAll: function(){
	  this.closebutton.hide();
	  this.container.hide();
	  this.leftContainer.hide();
	  this.box.hide();
  },
  fixPosition: function() {
    var offset = this.el.cumulativeOffset();
    var top = offset.top + this.el.getHeight();
    var left = offset.left - this.options.boxWidth + this.el.getWidth();
    
    if(this.options.allowFilter != 1){
    	left += this.options.sideBarWidth - 10;
    }
    
	//var left = offset.left;
    //$(this.mainContainerId).setStyle({ top: (top) + 3 + 'px', left: (left - 32) + 'px' });
	$(this.mainContainerId).setStyle({ top: (top - 10) + 'px', left: (left - 20) + 'px' });
    this.closebutton.setStyle({ top: '-22px', left: (this.options.boxWidth - 17)+ 'px' });
    if(this.options.allowFilter != 1){
    	this.closebutton.setStyle({top: '-10px', left: (this.options.boxWidth - this.options.sideBarWidth - 12)+ 'px'});
    }
  },

  enableKillerFn: function() {
    //Event.observe(document.body, 'click', this.killerFn);
  },

  disableKillerFn: function() {
    //Event.stopObserving(document.body, 'click', this.killerFn);
  },

  killSuggestions: function() {
    this.stopKillSuggestions();
    this.intervalId = window.setInterval(function() { this.hide(); this.stopKillSuggestions(); } .bind(this), 300);
  },

  stopKillSuggestions: function() {
    window.clearInterval(this.intervalId);
  },
  catInputClick: function (e){
	  
	  var fqString = "";
	  var index = 0;
	  
	  $$('#'+this.leftContainer.identify()+' input.category').each(function(item){
		  if(item.checked === true){
			  if(index === 0){
				  fqString += 'category:"'+item.value+'"';
			  }else{
				  fqString += " OR "+'category:"'+item.value+'"';
			  }
			  index++;
		  }
	  })
	  if(fqString.length > 0){
		  //alert(this.options.container.identify());
		  $(this.options.container.identify()).appendChild(new Element('input',{type:'hidden',name:'fq',value:(fqString)}));
		  //alert('appended');
	  }
	 // $('search_filters').value = fqString;
	  this.manager.store.remove('fq');
	  this.manager.store.addByValue('fq', fqString);
	  this.updateSeachFaces = false;
	  this.getSuggestions();
  },
  brandInputClick: function (e){
	  var fqString = "";
	  var index = 0;
	  
	  $$('#'+this.leftContainer.identify()+' input.brand').each(function(item){
		  if(item.checked === true){
			  if(index === 0){
				  fqString += 'oemname_facets:"'+item.value+'"';
			  }else{
				  fqString += " OR "+'oemname_facets:"'+item.value+'"';
			  }
			  index++;
		  }
	  })
	  if(fqString.length > 0){
		  //alert(this.options.container.identify());
		  $(this.options.container.identify()).appendChild(new Element('input',{type:'hidden',name:'fq',value:(fqString)}));
		  //alert('appended');
	  }
	 // $('search_filters').value = fqString;
	  this.manager.store.remove('fq');
	  this.manager.store.addByValue('fq', fqString);
	  this.updateSeachFaces = false;
	  this.getSuggestions();
  }
  ,
  onKeyPress: function(e) {
    if (!this.enabled) { return; }
    // return will exit the function
    // and event will not fire
    switch (e.keyCode) {
      case Event.KEY_ESC:
        this.el.value = this.currentValue;
        this.hide();
        break;
      case Event.KEY_TAB:
      case Event.KEY_RETURN:
    	  if (this.selectedIndex === -1) {
          this.hide();
          return;
        }
        this.select(this.selectedProductId);
        if (e.keyCode === Event.KEY_TAB) { return; }
        break;
      case Event.KEY_UP:
        this.moveUp();
        break;
      case Event.KEY_DOWN:
        this.moveDown();
        break;
      default:
        return;
    }
    Event.stop(e);
  },

  onKeyUp: function(e) {
    switch (e.keyCode) {
      case Event.KEY_UP:
      case Event.KEY_DOWN:
        return;
    }
    clearInterval(this.onChangeInterval);
    if (this.currentValue !== this.el.value) {
      if (this.options.deferRequestBy > 0) {
        // Defer lookup in case when value changes very quickly:
        this.onChangeInterval = setInterval((function() {
          this.onValueChange();
        }).bind(this), this.options.deferRequestBy);
      } else {
        this.onValueChange();
      }
    }
  },

  onValueChange: function() {
    clearInterval(this.onChangeInterval);
    this.currentValue = this.el.value;
    this.selectedIndex = -1;
    if (this.ignoreValueChange) {
      this.ignoreValueChange = false;
      return;
    }
    if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
    	this.hide();
    } else {
    	this.updateSeachFaces = true;
    	this.manager.store.remove('fq');
    	this.getSuggestions();
    }
  },

  getSuggestions: function() {
    var cr = this.cachedResponse[this.currentValue];
    if (cr && Object.isArray(cr.suggestions)) {
      this.suggestions = cr.suggestions;
      this.data = cr.data;
      this.suggest();
    } else if (!this.isBadQuery(this.currentValue)) {
    	
	/*this.manager.store.addByValue('q', '"'+this.currentValue+'"');*/
	this.manager.store.addByValue('q', this.currentValue);
    	this.manager.store.addByValue('json.nl', 'map');
    	this.manager.store.addByValue('rows', '5');
    	this.manager.store.addByValue('fl', 'name_varchar,products_id,price_decimal');
    	if(this.options.queryFields != ""){
    		this.manager.store.addByValue('qf', this.options.queryFields);
    	}else{
    		this.manager.store.addByValue('qf', 'name_varchar');
    	}
    	
    	this.manager.store.addByValue('spellcheck', 'true');
    	this.manager.store.addByValue('autocompletez', 'true');
    	this.manager.store.addByValue('spellcheck.collate', 'true');

    	this.manager.store.addByValue('facet', 'true');
    	this.manager.store.addByValue('facet.field', 'category_path,manufacturer_facets');
    	//this.manager.store.addByValue('facet.field', 'manufacturers_name');

    	this.manager.store.addByValue('facet.limit', '5');
    	
    	//this.manager.store.addByValue('timestamp', new Date().getTime());
    	
    	this.manager.store.addByValue('defType', 'dismax');

    	this.manager.doRequest();
    }
  },

  isBadQuery: function(q) {
    var i = this.badQueries.length;
    while (i--) {
      if (q.indexOf(this.badQueries[i]) === 0) { return true; }
    }
    return false;
  },

  hide: function() {
    this.enabled = false;
    this.selectedIndex = -1;
    this.hideAll();
    this.box.hide();
  },
  showCategoriesFaces: function(){
	    var cats = this.manager.response.facet_counts.facet_fields.category_path;
	    var categoryFace = new Element('ul', { style: 'text-align:left;padding:5px'});
	    var index = 0;
	    var catIdRange = "";
	    var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
	    for(key in cats) {
	    	if(cats[key] < 1){
	    		continue;
	    	}  
	    	var catPath = key.substring(key.indexOf("/") + 1,key.lastIndexOf("/"));
	    	var currentCat = catPath.substring(catPath.lastIndexOf("/") + 1,catPath.length);
	    	
	    	var catLink = this.options.ajaxBaseUrl+'/index.php/search/?fq=category:"'+currentCat+'"&q='+this.currentKeyword;
	    	var catLi = new Element('li',{style:'cursor:pointer;',onclick:'window.location=\''+catLink+'\'',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('solr_search_suggest_item_row');
	    	//var catInput = new Element('input',{type:'checkbox',value:(key)}).addClassName('category');
	    	//Event.observe(catInput, 'click', this.catInputClick.bind(this));
	    	//catIdRange += key+',';
	    	//catLi.appendChild(catInput);
	    	
	    	//catPath = catPath.replace('/', '&nbsp;>&nbsp;');	
	    	catPath = catPath.split('/').join('&nbsp;>&nbsp;');
	    	catLi.appendChild(new Element('span').addClassName('solr_search_suggest_item_title').update(Autocomplete.highlight(currentCat, re)));
	    	catLi.appendChild(new Element('br'));
	    	catLi.appendChild(new Element('span').addClassName('solr_search_suggest_item_subtitle').update(cats[key]+' products - '+Autocomplete.highlight(catPath, re)));
	    	categoryFace.appendChild(catLi);
	    	if(index >= 5){
	    		break;
	    	}
	    	index++;
	    }
	    this.leftContainer.appendChild(new Element('div',{style: 'font-weight:bold;text-align:left;font-size: 18px;'}).update('Categories'));
  	  	this.leftContainer.appendChild(categoryFace);
  },
  showBrandsFaces: function(){
	  var brands = this.manager.response.facet_counts.facet_fields.manufacturer_facets;
	    var brandsFace = new Element('ul', { style: 'text-align:left;padding:5px' });
	    var index = 0;
	    for(key in brands) {
	    	
	    	if(brands[key] < 1){
	    		continue;
	    	}    
	    	var brandLink = this.options.ajaxBaseUrl+'/index.php/search/?fq=manufacturer_facets:"'+key+'"&q='+this.currentKeyword;
	    	var brandLi = new Element('li',{style:'cursor:pointer;',onclick:'window.location=\''+brandLink+'\'',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('solr_search_suggest_item_row');
	    	//var brandInput = new Element('input',{type:'checkbox',name:'brand',value:(key)}).addClassName('brand');
	    	//Event.observe(brandInput, 'click', this.brandInputClick.bind(this));
	    	//brandLi.appendChild(brandInput);
	    	
	    	//alert(brandLink);
	    	brandLi.appendChild(new Element('span').addClassName('solr_search_suggest_item_title').update('<a href="'+encodeURI(brandLink)+'">'+key+'</a>'));
	    	brandLi.appendChild(new Element('br'));
	    	brandLi.appendChild(new Element('span').addClassName('solr_search_suggest_item_subtitle').update(brands[key]+' products'));
	    	brandsFace.appendChild(brandLi);
	    	if(index >= 5){
	    		break;
	    	}
	    	index++;
	    }
	    this.leftContainer.appendChild(new Element('div',{style: 'font-weight:bold;margin-top:10px;text-align:left;font-size: 18px;'}).update('Brands'));
	    this.leftContainer.appendChild(brandsFace);
  },
  suggest: function() {
    if (this.suggestions.length === 0 && this.currentValue.length == 0) {
      this.hide();
      this.box.hide();
      return;
    }
    var content = [];
    //var re = new RegExp('\\b' + this.currentValue.match(/\w+/g).join('|\\b'), 'gi');
    var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
   /*
    this.suggestions.each(function(value, i) {
      content.push((this.selectedIndex === i ? '<div class="selected suggested-item"' : '<div class="suggested-item"'), ' title="', value, '" onclick="Autocomplete.instances[', this.instanceId, '].select(', i, ');" onmouseover="Autocomplete.instances[', this.instanceId, '].activate(', i, ');">', Autocomplete.highlight(value, re), '</div>');
    } .bind(this));
    */
    var i = 1;
    for(key in this.suggestions)
	{
    	if(!isNaN(key)){
    		value = this.suggestions[key];
    		price = parseFloat(this.suggestionsPrice[key]);
    		price = price.toFixed(2);
        	if(this.options.displayThumb == 1){
        		content.push((this.selectedIndex === i ? '<div id="'+key+'" style="padding:2px;" class="selected suggested-item"' : '<div id="'+key+'" class="suggested-item"'), ' style="padding:2px;" title="', value, '" onclick="Autocomplete.instances[', this.instanceId, '].select(', key, ');" onmouseout="$(this).addClassName(\'suggested-item\')" onmouseover="Autocomplete.instances[', this.instanceId, '].activate(', i, ');"><div class="solr_search_suggest_thumb"><img src="'+this.options.ajaxBaseUrl+'/index.php/search/ajax/thumb/?product_id='+key+'" style="" /></div>', '<span class="solr_search_suggest_item_title">'+Autocomplete.highlight(value, re)+'</span><br/><span class="solr_search_suggest_item_subtitle">$'+price+'</span>', '</div>');
        	}else{
        		content.push((this.selectedIndex === i ? '<div id="'+key+'" style="padding:2px;" class="selected suggested-item"' : '<div id="'+key+'" class="suggested-item"'), ' style="padding:2px;" title="', value, '" onclick="Autocomplete.instances[', this.instanceId, '].select(', key, ');" onmouseout="$(this).addClassName(\'suggested-item\')" onmouseover="Autocomplete.instances[', this.instanceId, '].activate(', i, ');">', '<span class="solr_search_suggest_item_title">'+Autocomplete.highlight(value, re)+'</span><br/><span class="solr_search_suggest_item_subtitle">$'+price+'</span>', '</div>');
        	}
    		
        	i++;
        	if(i>= 20){
        		break;
        	}
    	}    	
	}
    
    //Display or hide the "Did you mean" text
    /*
	if(this.incorrectkeywords.indexOf(this.q) > -1){
		$('didyoumean').setStyle('display:block');
		$('didyoumean_text').update(this.didyoumeantext);
		$('didyoumean_text_2').update(this.el.value);
		this.incorrectkeywords = [];
		this.fixPosition();
	}else{
		$('didyoumean').hide();
		this.didyoumeantext = '';
	}
    */
    this.enabled = true;
    this.box.setStyle('display:block');
    if(this.suggestions.length > 0){
    	this.container.update('<div class="result_items">'+this.Autocompletemessage+'</div>'+content.join('')).show();
    }else{
    	this.hideAll();
		return;
    }
    
    
    this.el.setStyle({style:"border-bottom:1px solid #D0D3D8;background:#D0D3D8"});
    this.closebutton.show();
  },

  processResponse: function(response) {
	  this.suggestions = [];
	  if(typeof response === 'undefined' && this.currentValue.length == 0){
		  this.hideAll();
		  return;
	  }
	  if(response && (response.response.docs.length < 1 || this.currentValue.length == 0)){6
		  this.hideAll();
		  return;
	  }
	  /**
	   * Loop to push doc name into suggestions array
	   */
	  var i = 0;
	  if(response && response.responseHeader.params.q){		  
		  for (var index = 0; index < response.response.docs.length; ++index) {
			/*If we would like to remove duplication product name
			if(this.suggestions.indexOf(response.response.docs[index].autosuggest) < 0 ){
				this.suggestions[i] = response.response.docs[index].autosuggest;
				i++;
			}	
			if(this.suggestions.size() >= 20){
				break;
			}	
			*/ 
			
			product_id = response.response.docs[index].products_id;
			//alert(product_id);
			this.suggestions[product_id] = response.response.docs[index].name_varchar;
			this.suggestionsPrice[product_id] = response.response.docs[index].price_decimal;
			i++;
			if(i >= 20){
				break;
			}
		  }
		  this.Autocompletemessage = 'Display result of <b>'+response.responseHeader.params.q+'</b>';
		  if(response.responseHeader.params.q != this.currentValue){
			  this.Autocompletemessage = 'Display result of <b>'+response.responseHeader.params.q+'</b> instead';
		  }
		  this.currentKeyword = response.responseHeader.params.q;
		  var viewAllLink = this.options.ajaxBaseUrl+'/index.php/search/?q='+this.currentKeyword;
	    	//alert(brandLink);		  
		  $('solr_search_view_all_link').update('<a href="'+encodeURI(viewAllLink)+'">View All Search Results for <b>'+this.currentKeyword+'</b></a>');
	  }
	  
	  this.suggest();
	  if(this.updateSeachFaces === true){
		  this.leftContainer.update('');		  
		  this.showBrandsFaces();
		  this.showCategoriesFaces();
		  this.leftContainer.show();
		  
		  
	  }
  },
  ajaxGetProductUrl: function(id){
	  new Ajax.JSONRequest(this.options.ajaxBaseUrl+'/index.php/search/ajax/producturl?q='+id+'&wt=json', {
		     callbackParamName: "callback",
		      
		      onCreate: function(response) {
		        //do something
		      },
		      onSuccess: function(response) {
		        //Do something
		      },
		      onFailure: function(response) {
		        //do some thing
		      },
		      onComplete: function(response) {
		    	  productInfo = response.responseJSON;	
		    	  //alert(productInfo['product_url']);
		    	  window.location=productInfo['product_url'];
		    	 // var redirect = new Redirect(productInfo['product_url']);
		    	  //redirect.go();
		      }
	  });  
  },
  activate: function(index) {
    var divs = this.container.childNodes;
    var activeItem;
    // Clear previous selection:
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
      divs[this.selectedIndex].className = 'suggested-item';
      this.selectedProductId = divs[this.selectedIndex].id;
    }
    
    this.selectedIndex = index;
    //alert(product_id);
    
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
      activeItem = divs[this.selectedIndex]
      activeItem.className = 'selected';
    }
    return activeItem;
  },

  deactivate: function(div, index) {
    //div.removeClassName('selected');
    //div.addClassName('suggested-item');
    if (this.selectedIndex === index) { this.selectedIndex = -1; }
  },

  select: function(i) {
	this.ajaxGetProductUrl(i);
	return true;
    var selectedValue = this.suggestions[i];
    if (selectedValue) {
    	
      this.el.value = selectedValue;
      if (this.options.autoSubmit && this.el.form) {
        this.el.form.submit();
      }
      this.ignoreValueChange = true;
      this.hide();
      this.onSelect(i);
    }
  },

  moveUp: function() {
    if (this.selectedIndex === -1) { return; }
    if (this.selectedIndex === 0) {
      this.container.childNodes[0].className = '';
      this.selectedIndex = -1;
      //this.el.value = this.currentValue;
      return;
    }
    this.adjustScroll(this.selectedIndex - 1,this.selectedProductId);
  },

  moveDown: function() {
    if (this.selectedIndex === (this.suggestions.length - 1)) { return; }
    this.adjustScroll(this.selectedIndex + 1,this.selectedProductId);
  },

  adjustScroll: function(i,product_id) {
    var container = this.container;
    var activeItem = this.activate(i,product_id);
    var offsetTop = activeItem.offsetTop;
    var upperBound = container.scrollTop;
    var lowerBound = upperBound + this.options.maxHeight - 25;
    if (offsetTop < upperBound) {
      container.scrollTop = offsetTop;
    } else if (offsetTop > lowerBound) {
      container.scrollTop = offsetTop - this.options.maxHeight + 25;
    }
    //this.el.value = this.suggestions[i];
  },

  onSelect: function(i) {
    (this.options.onSelect || Prototype.emptyFunction)(this.suggestions[i], this.data[i]);
  }

};

Event.observe(document, 'dom:loaded', function(){ Autocomplete.isDomLoaded = true; }, false);