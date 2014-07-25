define(function(require, exports, module) {
    var KindEditorFactory = require('common/kindeditor-factory');
    var HtmlCropper = require("../widget/html-cropper");
    
    exports.run = function() {
        console.log(app.config.editor_upload_path);
        var editor = KindEditorFactory.create('#simpleEditor', 'simple', {extraFileUploadParams:{group:'note'}});
    };

});

