define(function(require, exports, module) {

    require('markdown');
    require('highlight');
    require('markdown-css');
    require('highlight-css');


    exports.run = function() {

        // markdown
        var simplemde = new SimpleMDE({
            element: document.getElementById("markdownContent"),
            renderingConfig: {
                codeSyntaxHighlighting: true
            },
            status: false,
            toolbar: false
        });
        simplemde.togglePreview();

    };

});

