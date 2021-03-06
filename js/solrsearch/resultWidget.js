
AjaxSolr.ResultWidget = AjaxSolr.AbstractWidget.extend(
{  
	/**
	   * How many links are shown around the current page.
	   *
	   * @field
	   * @public
	   * @type Number
	   * @default 4
	   */
	  innerWindow: 2,
	  updateSeachFaces: false,
	  productInfo: {},
	  pageLabel: 'Page',

	  /**
	   * How many links are around the first and the last page.
	   *
	   * @field
	   * @public
	   * @type Number
	   * @default 1
	   */
	  outerWindow: 0,

	  /**
	   * The previous page link label.
	   *
	   * @field
	   * @public
	   * @type String
	   * @default "&laquo; previous"
	   */
	  prevLabel: '&laquo; Prev',
	  prevLabelImage: null,

	  /**
	   * The next page link label.
	   *
	   * @field
	   * @public
	   * @type String
	   * @default "next &raquo;"
	   */
	  nextLabel: 'Next &raquo;',
	  nextLabelImage: null,

	  /**
	   * Separator between pagination links.
	   *
	   * @field
	   * @public
	   * @type String
	   * @default ""
	   */
	  separator: ' ',

	  /**
	   * The current page number.
	   *
	   * @field
	   * @private
	   * @type Number
	   */
	mediaPath:null,
	jsPath:null,
	basePath:null,
	viewType:'grid',
	currentUrl:null,
	currentPage:null,
	totalPages: null,
	gapMarker: function () {
	    return '';//'<span class="pager-gap">&hellip;</span>';
	},	
	/**
	   * @returns {Array} The links for the visible page numbers.
	   */
	  windowedLinks: function () {
	    var links = [];
	
	    var prev = null;
	
	    visible = this.visiblePageNumbers();
	    for (var i = 0, l = visible.length; i < l; i++) {
	      if (prev && visible[i] > prev + 1) links.push(this.gapMarker());
	      links.push(this.pageLinkOrSpan(visible[i], [ 'pager-current' ]));
	      prev = visible[i];
	    }
	
	    return links;
	  },
	  /**
	   * @returns {Array} The visible page numbers according to the window options.
	   */ 
	  visiblePageNumbers: function () {
	    var windowFrom = this.currentPage - this.innerWindow;
	    //alert(windowFrom);
	    var windowTo = this.currentPage + this.innerWindow;

	    // If the window is truncated on one side, make the other side longer
	    if (windowTo > this.totalPages) {
	      windowFrom = Math.max(0, windowFrom - (windowTo - this.totalPages));
	      windowTo = this.totalPages;
	    }
	    if (windowFrom < 1) {
	      windowTo = Math.min(this.totalPages, windowTo + (1 - windowFrom));
	      windowFrom = 1;
	    }

	    var visible = [];

	    // Always show the first page
	    //visible.push(1);
	    // Don't add inner window pages twice
	    for (var i = 1; i <= Math.min(1 + this.outerWindow, windowFrom - 1); i++) {
	      //visible.push(i);
	    }
	    // If the gap is just one page, close the gap
	    if (1 + this.outerWindow == windowFrom - 2) {
	      //visible.push(windowFrom - 1);
	    }
	    // Don't add the first or last page twice
	    for (var i = Math.max(1, windowFrom); i <= Math.min(windowTo, this.totalPages); i++) {
	      visible.push(i);
	    }
	    // If the gap is just one page, close the gap
	    if (this.totalPages - this.outerWindow == windowTo + 2) {
	      visible.push(windowTo + 1);
	    }
	    // Don't add inner window pages twice
	    for (var i = Math.max(this.totalPages - this.outerWindow, windowTo + 1); i < this.totalPages; i++) {
	      visible.push(i);
	    }
	    // Always show the last page, unless it's the first page
	    if (this.totalPages > 1) {
	      //visible.push(this.totalPages);
	    }

	    return visible;
	  },
	  /**
	   * @param {Number} page A page number.
	   * @param {String} classnames CSS classes to add to the page link.
	   * @param {String} text The inner HTML of the page link (optional).
	   * @returns The link or span for the given page.
	   */
	  pageLinkOrSpan: function (page, classnames, text) {
	    text = text || page;
		
	    if (page && page != this.currentPage) {
	    	var el = new Element('a',{href:'#',rel:(this.relValue(page))}).addClassName(classnames[1]);
	    	el.update(text);
	    	el.observe('click',this.clickHandler(page));
	    	return el;
	    }
	    else {
	      var el = new Element('span').addClassName('current');
	      el.update(text);
	      return el;
	    }
	  },

	  /**
	   * @param {Number} page A page number.
	   * @returns {Function} The click handler for the page link.
	   */
	  clickHandler: function (page) {
	    var self = this;
	    return function () {
	      self.manager.store.get('start').val((page - 1) * (self.manager.response.responseHeader.params && self.manager.response.responseHeader.params.rows || 10));
	      self.manager.doRequest();
	      return false;
	    }
	  },

	  /**
	   * @param {Number} page A page number.
	   * @returns {String} The <tt>rel</tt> attribute for the page link.
	   */
	  relValue: function (page) {
	    switch (page) {
	      case this.previousPage():
	        return 'prev' + (page == 1 ? 'start' : '');
	      case this.nextPage():
	        return 'next';
	      case 1:
	        return 'start';
	      default: 
	        return '';
	    }
	  },

	  /**
	   * @returns {Number} The page number of the previous page or null if no previous page.
	   */
	  previousPage: function () {
	    return this.currentPage > 1 ? (this.currentPage - 1) : null;
	  },

	  /**
	   * @returns {Number} The page number of the next page or null if no next page.
	   */
	  nextPage: function () {
	    return this.currentPage < this.totalPages ? (this.currentPage + 1) : null;
	  },
	pagination: function(){
		var perPage = parseInt(this.manager.response.responseHeader.params && this.manager.response.responseHeader.params.rows || 10);
	    var offset = parseInt(this.manager.response.responseHeader.params && this.manager.response.responseHeader.params.start || 0);
	    var total = parseInt(this.manager.response.response.numFound);
		
		$('solr_search_product_total').update(total);
		
		$('solr_search_product_offset').update(offset+1);
		if((offset+1+perPage) >= total){
			$('solr_search_product_rows').update(total);
		}else{
			$('solr_search_product_rows').update(offset+perPage);
		}
		
	    //alert(perPage+','+offset+','+total);
	    
	    offset = offset - offset % perPage;

	    this.currentPage = Math.ceil((offset + 1) / perPage);
	    this.totalPages = Math.ceil(total / perPage);
	    
	    this.renderLinks(this.windowedLinks());
	   // alert(this.currentPage);
	    //alert(this.totalPages);
	},
	/**
	   * Render the pagination links.
	   *
	   * @param {Array} links The links for the visible page numbers.
	   */
	  renderLinks: function (links) {
		
	    if (this.totalPages) {
			if(this.currentPage > 1){
				if(this.prevLabelImage){
					links.unshift(this.pageLinkOrSpan(this.previousPage(), [ 'pager-disabled', 'pager-prev' ], this.prevLabelImage));
				}else{
					links.unshift(this.pageLinkOrSpan(this.previousPage(), [ 'pager-disabled', 'pager-prev' ], this.prevLabel));
				}
			}
			if(this.totalPages > this.currentPage){
				if(this.nextLabelImage){
					links.push(this.pageLinkOrSpan(this.nextPage(), [ 'pager-disabled', 'pager-next' ], this.nextLabelImage));
				}else{
					links.push(this.pageLinkOrSpan(this.nextPage(), [ 'pager-disabled', 'pager-next' ], this.nextLabel));
				}
			}
	      //AjaxSolr.theme('list_items', this.target, links, this.separator);
	    }
	    var ul = new Element('ol');
	    for (var i = 0, l = links.length; i < l; i++) {
	    	ul.appendChild(new Element('li',{style:'margin-right:4px'}).update(links[i]));
	    }
		
	    if(links.length > 0){
 		$('navigation').update('<strong>'+this.pageLabel+'</strong>');
 	    }
	    $('navigation').appendChild(ul);
	  },
	viewGrid: function (idRange){
		var self = this;
		var ajaxUrl = self.basePath+'index.php/solrsearch/ajax/productinfo?q='+idRange+'&wt=json&currentUrl='+self.currentUrl;
		console.log(ajaxUrl);
		new Ajax.JSONRequest(self.basePath+'index.php/solrsearch/ajax/productinfo?q='+idRange+'&wt=json&currentUrl='+self.currentUrl, {
		     callbackParamName: "callback",
		      onCreate: function(response) {
		      },
		      onSuccess: function(response) {
		      },
		      onFailure: function(response) {
		      },
		      onComplete: function(response) {
		    	  self.productInfo = response.responseJSON;
		    	  var productInfo = response.responseJSON;
		    	  $('docs').update('');
		    	  
				  
				  
				  var content = new Element('div');
				  var index = 0;
				  for (var row = 0; row <= 6; row++){
					
					var ROW = new Element('ul').addClassName('products-grid');
					
					for (var x = 0; x <= 2; x++) {
						//alert(index);
						if(index > self.manager.response.response.docs.length-1){
							break;
						}
						//alert(index);
						var doc = self.manager.response.response.docs[index];
						var productId = doc.products_id;
						var productName = doc.name_varchar;
						var imageUrl = productInfo[productId].image_url;
						var fullImageUrl = productInfo[productId].full_image_url;
						var productUrl = productInfo[productId].product_url;
						var addToWishListUrl = productInfo[productId].add_to_wishlist_url;
						var addToCompareUrl = productInfo[productId].add_to_compare_url;
						var addToCartUrl = productInfo[productId].add_to_cart_url;
						var shortDescriptionText = productInfo[productId].short_description;
						var finalPrice = productInfo[productId].final_price;
						var finalPriceFormated = productInfo[productId].final_price_formated;
						var availability_class = productInfo[productId].availability_class;
						
						
						var COLUMN = new Element('li').addClassName('item');
						if(x < 1){
							COLUMN.addClassName('first');
						}
						if(x == 2){
							COLUMN.addClassName('last');
						}
						
						//PRODUCT IMAGE						
						var product_image = new Element('a',{title:(productName),href:(productUrl)}).addClassName('product-image').update('<img width="125px" height="125px" src="'+imageUrl+'" /><div class="product-label product-is-normal"></div>');						
						
						//PRODUCT NAME
						var product_name_div = new Element('div').addClassName('product-name');
						var product_name_link = new Element('div').addClassName('product-name-link');
						var product_name_a = new Element('a',{title:(productName),href:(productUrl)}).addClassName('product-name').update('<h2>'+productName+'</h2>');
						product_name_link.update(product_name_a);
						var product_name = product_name_div.update(product_name_link);
						
						//PRODUCT DESCRIPTION
						var product_desc = new Element('div').addClassName('desc').addClassName('std');
						product_desc.update(shortDescriptionText);
						
						//ACTIONS
						var product_actions = new Element('div').addClassName('actions');
						var product_fix_price_p = new Element('div').addClassName('product-pix-price');
						product_fix_price_p.update('Tarif Pixellium');
						var button = new Element('button',{type:"button",title:"Ajouter au panier",onclick:"setLocation('"+(addToCartUrl)+"')"}).addClassName('button btn-cart-on');
						button.update('<div class="price-box"><span id="product-price-'+productId+'" class="regular-price"><span class="price">'+finalPriceFormated+'</span></span></div>');
						
						var community = new Element('div').addClassName('product-community');
						community.update('<div class="product-community-availability"><span class="pix-availability '+availability_class+'"></span></div>');
						
						var add_to_links = new Element('ul').addClassName('add-to-links');
						add_to_links.update('<li><a class="link-wishlist" href="'+addToWishListUrl+'">Ajouter à la liste d\'envies</a></li><li><a class="link-compare" href="'+addToCompareUrl+'">Ajouter au comparateur</a></li>');
						
						product_actions.appendChild(product_fix_price_p);
						product_actions.appendChild(button);
						product_actions.appendChild(community);
						product_actions.appendChild(add_to_links);
						
						
						COLUMN.appendChild(product_image);
						COLUMN.appendChild(product_name);
						COLUMN.appendChild(product_desc);
						COLUMN.appendChild(product_actions);
						
						ROW.appendChild(COLUMN);
						index++;
					}
					
					content.appendChild(ROW);
				  }
				  
				  
		  	    $('docs').appendChild(content);
				self.pagination();
				
		      }
		    });
	},
	imageOverlay: function (image,event){
		//var Image = new Element('img',{src:(url)});
		Overlay.defaults({ modal : true, animate : false, click_hide : true, auto_hide : false });
		new Overlay().show(image, { modal : true, animate : false });
	},
	showListView: function (idRange){
		var self = this;
		var ajaxUrl = self.basePath+'index.php/solrsearch/ajax/productinfo?q='+idRange+'&wt=json&currentUrl='+self.currentUrl;
		console.log(ajaxUrl);
		new Ajax.JSONRequest(self.basePath+'index.php/solrsearch/ajax/productinfo?q='+idRange+'&wt=json&currentUrl='+self.currentUrl, {
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
		    	  self.productInfo = response.responseJSON;
		    	  var productInfo = response.responseJSON;
		    	  $('docs').update('');
		    	  var OL = new Element('ol',{id:'products-list'}).addClassName('products-list');
		    	  for (var i = 0, l = self.manager.response.response.docs.length; i < l; i++) {
			  	      var doc = self.manager.response.response.docs[i];
			  	      var productId = doc.products_id;
			  	      var productName = doc.name_varchar;
			  	      var imageUrl = productInfo[productId].image_url;
			  	      var fullImageUrl = productInfo[productId].full_image_url;
			  	      var productUrl = productInfo[productId].product_url;
			  	      var addToWishListUrl = productInfo[productId].add_to_wishlist_url;
			  	      var addToCompareUrl = productInfo[productId].add_to_compare_url;
			  	      var addToCartUrl = productInfo[productId].add_to_cart_url;
			  	      var shortDescriptionText = productInfo[productId].short_description;
			  	      var finalPrice = productInfo[productId].final_price;
			  	      var finalPriceFormated = productInfo[productId].final_price_formated;
					  var availability_class = productInfo[productId].availability_class;
			  	      
			  	      var li = new Element('li').addClassName('item');
			  	      //PRODUCT IMAGE
					  var product_image_div = new Element('div').addClassName('products-list-image');
			  	      var product_image_a = new Element('a',{title:(productName),href:(productUrl)}).addClassName('product-image').update('<img src="'+imageUrl+'" /><div class="product-label product-is-normal"></div>');
					  var product_image = product_image_div.update(product_image_a);
			  	     
			  	      li.appendChild(product_image);
			  	      
					  //PRODUCT LIST LEFT
					  var product_list_left = new Element('div').addClassName('products-list-left');
					  product_list_left.update('<div class="product-name"><div class="product-name-link"><a title="'+productName+'" href="'+productUrl+'"><h2>'+productName+'</h2></a></div></div><div class="desc std">'+shortDescriptionText+'</div>');
					  
					  li.appendChild(product_list_left);
					  
					  //PRODUCT LIST MIDDLE
					  var product_list_middle = new Element('div').addClassName('products-list-middle');
					  product_list_middle.update('<div class="product-community"><div class="product-community-availability"><span class="pix-availability '+availability_class+'"></span></div>	</div>');
					  li.appendChild(product_list_middle);
					  
					  //PRODUCT LIST RIGHT
					  var product_list_right = new Element('div').addClassName('products-list-right');
					  var product_list_right_inside = '<p class="product-pix-price">Tarif Pixellium</p>';
					  product_list_right_inside += '<p><button onclick="setLocation('+addToCartUrl+')" class="button btn-cart-on" title="Ajouter au panier" type="button">';
					  product_list_right_inside += '<div class="price-box"><span id="product-price-2087" class="regular-price"><span class="price">'+finalPriceFormated+'</span></span></div>';
					  product_list_right_inside += '</button></p><div class="product-ratings"></div><ul class="add-to-links">';
					  product_list_right_inside += '<li><a class="link-wishlist" href="'+addToWishListUrl+'">Ajouter à la liste d\'envies</a></li><li><a class="link-compare" href="'+addToCompareUrl+'">Ajouter au comparateur</a></li></ul></div>';
					  product_list_right.update(product_list_right_inside);
					  
					  li.appendChild(product_list_right);
					  
			  	      OL.appendChild(li);
		  	      }
		  	    $('docs').appendChild(OL);
		  	  self.pagination();
		      }
		    });
	},
	doSort: function(field,direction){
		this.manager.store.remove('sort');
		this.manager.store.addByValue('sort', field+' '+direction);
		  //this.updateSeachFaces = false;
		this.manager.doRequest();
	},
	doChangePerPage: function(value){
		this.manager.store.remove('rows');
		this.manager.store.addByValue('rows', value);
		this.manager.doRequest();
	},
	doChangeSortDirection: function(dir, field){
		var rel = document.getElementById('solr_search_sort_'+dir).rel;
				
		this.manager.store.remove('sort');
		//alert(obj.rel);
		if(rel == "desc"){
			direction = 'asc';
			this.manager.store.addByValue('sort', field+' '+direction);
			$('solr_search_sort_asc').show();
			$('solr_search_sort_desc').hide();
		}else{
			direction = 'desc';
			this.manager.store.addByValue('sort', field+' '+direction);
			$('solr_search_sort_asc').hide();
			$('solr_search_sort_desc').show();
		}
		
		  //this.updateSeachFaces = false;
		this.manager.doRequest();
	}
	,
	beforeRequest: function () {
		$('docs').update('<img src="'+this.jsPath+'solrsearch/loading.gif" />');
	},
	afterRequest: function () {
	    var self = this;
		var response = self.manager.response;
		//Incorrect keywords provided, and back request to Solr for misspelling suggestion
		if(typeof response.spellcheck !== 'undefined' && typeof response.response.numFound !== 'undefined' && response.response.numFound < 1){
			
		  if(typeof response.spellcheck.suggestions.collation !== 'undefined'){

			this.manager.store.addByValue('q', response.spellcheck.suggestions.collation);

			this.manager.store.addByValue('timestamp', new Date().getTime());
			//var arrs = response.spellcheck.suggestions.collation.split("autosuggest:");
			//self.Autocomplete.didyoumeantext = arrs[1];
			//self.Autocomplete.didyoumeantext = response.spellcheck.suggestions.collation;
			//self.Autocomplete.incorrectkeywords.push(self.Autocomplete.q);
			this.manager.doRequest();

		  }else{
			var productIdString = "";
			  for (var i = 0, l = this.manager.response.response.docs.length; i < l; i++) {
				  var doc = this.manager.response.response.docs[i];
				  
				  if((i+1) === this.manager.response.response.docs.length){
					  productIdString += doc.products_id;
				  }else{
					  productIdString += doc.products_id+",";
				  }
			  }
			  this.showListView(productIdString);
		  }
		  
		//If corrected keywords provided, and display suggestion
		}else{	
		  var productIdString = "";
		  for (var i = 0, l = this.manager.response.response.docs.length; i < l; i++) {
			  var doc = this.manager.response.response.docs[i];
			  
			  if((i+1) === this.manager.response.response.docs.length){
				  productIdString += doc.products_id;
			  }else{
				  productIdString += doc.products_id+",";
			  }
		  }
		  //this.showListView(productIdString);
		  if(this.viewType == 'grid'){
			this.viewGrid(productIdString);
		  }else{
			this.showListView(productIdString);
		  }
	  }
		Event.observe('solr_search_view_type_grid', 'click', function(event) {
			ResultPageManager.widgets.resultPage.viewType = 'grid';
			ResultPageManager.widgets.resultPage.manager.doRequest();
			this.addClassName('current');
			$('solr_search_view_type_list').removeClassName('current');
		});
		Event.observe('solr_search_view_type_list', 'click', function(event) {
			ResultPageManager.widgets.resultPage.viewType = 'list';
			ResultPageManager.widgets.resultPage.manager.doRequest();
			this.addClassName('current');
			$('solr_search_view_type_grid').removeClassName('current');
		});
		FacetedManager.store.addByValue('q', self.manager.store.get('q').value);
		FacetedManager.doRequest();
  }
})
