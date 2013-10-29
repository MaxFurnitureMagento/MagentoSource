Event.observe(window, 'load', function() {
  var zIndexNumber = 1000;
  $$('div.top-menu-popup').each(function(element) {
    element.setStyle({'zIndex': zIndexNumber});
    zIndexNumber -= 10;
  });
});