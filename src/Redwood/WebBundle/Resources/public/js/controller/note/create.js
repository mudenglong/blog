define(function(require, exports, module) {
    var KindEditorFactory = require('common/kindeditor-factory');
    
    exports.run = function() {
        console.log('123');
        var result = KindEditorFactory.create();
        console.log(result);
    };

});

