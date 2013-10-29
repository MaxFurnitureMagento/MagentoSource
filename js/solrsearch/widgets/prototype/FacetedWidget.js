AjaxSolr.FacetedWidget = AjaxSolr.AbstractWidget.extend(
{  
	resultManager: null,
	selectedCategories: [],
	showCategory: true,
	labels:{},
	catInputClick: function (e){
		  
		  var fqString = "";
		  var index = 0;
		var args = $A(arguments);
//		alert(args[0]);
		  $$('#solr_search_category_faces input').each(function(item){
			  if(item.checked === true){
				  if(index === 0){
					  fqString += item.alt+':"'+item.value+'"';
				  }else{
					  fqString += " OR "+item.alt+':"'+item.value+'"';
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
		  		
		  		var facets = this.manager.response.facet_counts.facet_fields;
		  		$('solr_search_category_faces').update('');
		  		var dl = new Element('dl',{id:"narrow-by-list"});
		  		for (var face in facets) {
		  		  if (facets.hasOwnProperty(face)) {
		  		   // alert(key + " -> " + facets[key]);
		  			var faceLabel = face;
		  			if (this.labels.hasOwnProperty(face)) {
		  				faceLabel = this.labels[face];
		  			}
		  			
		  			  
		  			  
		  			var faceContainer = new Element('ol', { class: 'gan-list-checkbox'});
				    var index = 0;
				    var faceItems = facets[face];
				    for(term in faceItems) {	
						
				    	if(parseInt(faceItems[term]) < 1){
				    		continue;
				    	}   
				    	var catLi = new Element('li',{style:'cursor:pointer;/*border-bottom:1px solid #DDDDDD*/',onmouseover:';/*$(this).addClassName("selected")*/',onmouseout:';/*$(this).removeClassName("selected")*/'});
				    	
				    	if(this.selectedCategories.indexOf(term.toString()) < 0){
				    		var catInput = new Element('input',{type:'checkbox',alt:(face),value:(term)}).addClassName('category '+face);
				    	}else{
				    		var catInput = new Element('input',{type:'checkbox',alt:(face),checked:'checked',value:(term)}).addClassName('category '+face);
				    	}
				    	
				    	Event.observe(catInput, 'click', this.catInputClick.bind(this,face));
				    	
				    	catLi.appendChild(catInput);
				    	//catLi.appendChild(new Element('span').update(term+'('+faceItems[term]+')'));
						catLi.appendChild(new Element('a',{onclick:'setChecked(this);'}).update(term));
						catLi.appendChild(new Element('em').update(faceItems[term]));
				    	faceContainer.appendChild(catLi);
						//console.log(catLi);
				    	index++;
				    }
				    
				    if(index > 0){
				    	
					    dl.appendChild(new Element('dt',{style: 'font-weight:bold;text-align:left',id:"advancednavigation-filter-header-"+face, onclick:"navigationOpenFilter('"+face+"');"}).addClassName('opendt odd first').update('<span class="filter-name"><span class="Categories"></span><strong style="text-transform:capitalize">'+faceLabel+'</strong><em></em></span>'));
						
						
						var dd = new Element('DD');
						dd.appendChild(faceContainer);
						
						dl.appendChild(dd);
					    $('solr_search_category_faces').appendChild(dl);
				    }
				    
		  		  }
		  		}
		  		
		  	}		    
	  },
	afterRequest: function () {
	if(this.showCategory){
	    this.showCategoriesFaces();
	}
  }
})