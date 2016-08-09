define(function(require, exports, module) {
    require('jquery.validate')($);

    exports.run = function() {
        

        $("#jswidgetForm").validate({
            rules: {
                'jswidget[title]': {
                    required: true,
                    minlength: 2
                },
    
                'jswidget[url]': "required",
                'jswidget[description]': "required"
            },
            messages: {
                'jswidget[title]':{
                    required: "请输入标题",
                    minlength: "标题的最小长度为2"
                },
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

