// $Id$

/**
 * @see http://wiki.apache.org/solr/SolJSON#JSON_specific_parameters
 * @class Manager
 * @augments AjaxSolr.AbstractManager
 */

AjaxSolr.Manager = AjaxSolr.AbstractManager.extend(
  {
  executeRequest: function (servlet) {
    var self = this;
    console.log(this.solrUrl + servlet + '?' + this.store.string() + '&wt=json');
   self.currentRequest = new Ajax.JSONRequest(this.solrUrl + servlet + '?' + this.store.string() + '&wt=json', {
      callbackParamName: "json.wrf",
      
      onCreate: function(response) {
        //created++;
        //console.log("jsonp request 1: created", response, response.responseJSON);
      },
      onSuccess: function(response) {
        //successful++;
        //console.log("jsonp request 1: successful", response, response.responseJSON);
      },
      onFailure: function(response) {
        //failed++;
        //console.log("jsonp request 1: failed", response, response.responseJSON);
      },
      onComplete: function(response) {
    	 
        self.handleResponse(response.responseJSON);
      }
    });
   
   
   //new Ajax.Request(this.solrUrl + servlet + '?' + this.store.string() + '&wt=json&json.wrf=?', {method: 'get',requestHeaders: {Accept: 'application/json'},onSuccess: function(transport,oJson) {self.handleResponse(transport.responseJSON);}});
  }
});