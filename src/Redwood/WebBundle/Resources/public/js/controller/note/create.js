define(function(require, exports, module) {
    var KindEditorFactory = require('common/kindeditor-factory');
    require('jquery.validate')($);

    exports.run = function() {
        var editor = KindEditorFactory.create('#note_content', 'simple', {extraFileUploadParams:{group:'note'}});

        $("#noteForm").validate({
            rules: {
                'note[title]': "required",
                'note[content]': "required"
            },
            messages: {
                'note[title]': "请输入姓名",
                'note[content]': "请输入title"
            }
        });
    };

});

