define(function(require, exports, module) {
    require('jquery.validate')($);

    exports.run = function() {
        

        $("#jswidgetForm").validate({
            rules: {
                'jswidget[title]': "required",
                'jswidget[url]': "required",
                'jswidget[description]': "required"
            },
            messages: {
                'jswidget[title]': "请输入标题",
                'jswidget[url]': "请输入URL",
                'jswidget[description]': "请输入描述"
            }
        });

        // tag 转为 后台可识别的字符
        $('#jswidgetForm').submit(function(e) {
            return;        
        });

    };

});

