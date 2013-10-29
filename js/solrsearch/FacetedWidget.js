
AjaxSolr.FacetedWidget = AjaxSolr.AbstractWidget.extend(
{  
	resultManager: null,
	selectedCategories: [],
	showCategory: true,
	catInputClick: function (e){
		  
		  var fqString = "";
		  var index = 0;
		  
		  $$('#solr_search_category_faces input.category').each(function(item){
			  if(item.checked === true){
				  if(index === 0){
					  fqString += 'category:"'+item.value+'"';
				  }else{
					  fqString += " OR "+'category:"'+item.value+'"';
				  }
				  index++;
			  }
		  })
		 // if(fqString.length > 0){
			  //alert(this.options.container.identify());
			 // $(this.options.container.identify()).appendChild(new Element('input',{type:'hidden',name:'fq',value:(fqString)}));
			  //alert('appended');
		 // }
		 // $('search_filters').value = fqString;
		  ResultPageManager.store.remove('fq');
		  ResultPageManager.store.addByValue('fq', fqString);
		  //this.updateSeachFaces = false;
		  ResultPageManager.doRequest();
		this.showCategory = false;
	  },
	  showCategoriesFaces: function(){
		  
		  	if(typeof this.manager.response !== 'undefined'){
		  		var cats = this.manager.response.facet_counts.facet_fields.category;
			    var categoryFace = new Element('ul', { style: 'text-align:left;padding:5px'});
			    var index = 0;
				//console.log(cats);
			    for(key in cats) {	
					
			    	if(parseInt(cats[key]) < 1){
			    		continue;
			    	}   
			    	var catLi = new Element('li',{style:'cursor:pointer;/*border-bottom:1px solid #DDDDDD*/',onmouseover:';/*$(this).addClassName("selected")*/',onmouseout:';/*$(this).removeClassName("selected")*/'});
			    	
			    	if(this.selectedCategories.indexOf(key.toString()) < 0){
			    		var catInput = new Element('input',{type:'checkbox',value:(key)}).addClassName('category');
			    	}else{
			    		var catInput = new Element('input',{type:'checkbox',checked:'checked',value:(key)}).addClassName('category');
			    	}
			    	
			    	Event.observe(catInput, 'click', this.catInputClick.bind(this));
			    	
			    	catLi.appendChild(catInput);
			    	catLi.appendChild(new Element('span').update(key+'('+cats[key]+')'));
			    	categoryFace.appendChild(catLi);
					//console.log(catLi);
			    	index++;
			    }
				
			    $('solr_search_category_faces').update('');
			    $('solr_search_category_faces').appendChild(new Element('div',{style: 'font-weight:bold;text-align:left'}).update('Filtre par catégories'));
			    $('solr_search_category_faces').appendChild(categoryFace);
		  	}		    
	  },
	afterRequest: function () {
	if(this.showCategory){
	    this.showCategoriesFaces();
	}
  }
})
