var AsyncIndex = {
    containerId: 'detailed_log',
    url: '',
    isFocused: false,

    init: function(url)
    {
        var self = this;

        self.url = url;

        self.updateLog();
        
        var interval = setInterval(function() {
            if ($(self.containerId).innerHTML != '') {
                self.scroll();
                clearInterval(interval);
            }
        }, 100);
    },


    updateLog: function()
    {
        var self = this;

        var request = new Ajax.Request(self.url, {
            method     : 'GET',
            parameters : {id: self.feedId},
            loaderArea : false,
            onSuccess : function(transport) {
                $(self.containerId).innerHTML = transport.responseText;
                
                setTimeout(function() {
                    self.updateLog();
                }, 100);
            }
        });
    },

    scroll: function ()
    {
        $(this.containerId).scrollTop = $(this.containerId).scrollHeight;
    }
};


// document.observe('dom:loaded', function() {
//     if ($('type')) {
//         FeedExportMapping.changeFormat($('type'));
//     }

//     window.editors = [];
//     $$('.codemirror').each(function(item, index) {
//         var editor = CodeMirror.fromTextArea(item, {
//             mode           : {name: 'xml', alignCDATA: true},
//             lineNumbers    : true,
//             matchTags      : true,
//             viewportMargin : Infinity
//         });

//         setInterval(function() {editor.refresh();editor.save()}, 100);
//         window.editors.push(editor);
//     });
// });