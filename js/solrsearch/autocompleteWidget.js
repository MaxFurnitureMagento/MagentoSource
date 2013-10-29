AjaxSolr.AutocompleteWidget = AjaxSolr.AbstractWidget.extend({
	/**
	* The init function which bind the search box into jquery autocomplete widget
	*/
	ajaxBaseUrl:null,
	init: function () {
		var self = this;
		self.Autocomplete = new Autocomplete('search', {
		    minChars:2, 
		    manager:self.manager,
		    ajaxBaseUrl:self.ajaxBaseUrl,
		    viewAllResultText:self.viewAllResultText,
		    displayResultOfText:self.displayResultOfText,
		    displayResultOfInsteadText:self.displayResultOfInsteadText,
		    categoryText:self.categoryText,
		    keywordsText:self.keywordsText,
		    displaykeywordsuggestion:self.displaykeywordsuggestion,
		    currencyPos:self.currencyPos,
		    allowFilter:self.allowFilter,
			currencySign:self.currencySign,
		    displayThumb:self.displayThumb,
		    searchTextPlaceHolder:self.searchTextPlaceHolder,
		    width:400,
		    boxWidth:462,
		    sideBarWidth:200,
		    deferRequestBy:100,
		    container: 'search_mini_form'
		  });
	},
	/**
	* Display the loadding image during the ajax request
	*/
	beforeRequest: function () {
		$('search').addClassName('ac_loading');
		//$('search').setStyle({background: 'url("js/solrsearch/ajax-loader.gif") no-repeat scroll center right #FFFFFF'});
	},
	/**
	* Process json result returned from Solr server
	*/
	afterRequest: function () {		
		var self = this;
		var response = self.manager.response;
		
		//self.Autocomplete.processResponse(response);
		$('search').removeClassName('ac_loading');
		if(typeof response !== 'undefined'){
			self.Autocomplete.processResponse(response);
			$('search').removeClassName('ac_loading');
			$('search').setStyle({background: ''});
		}		
	}
});