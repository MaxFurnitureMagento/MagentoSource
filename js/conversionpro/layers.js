(function() {
  var cb = function() { 
	$$('#narrow-by-list input[type="checkbox"]').each(function(d) {
	  d.on('click', function(e) {
		var href = d.next('a').href;
		window.location.href = href;
	  });
	  d.next('a').on('click', function(e) {
		d.click();
	  });
	});
  };
  if (document.loaded) {
    cb();
  } else {
    document.observe('dom:loaded', cb);
  }
})();