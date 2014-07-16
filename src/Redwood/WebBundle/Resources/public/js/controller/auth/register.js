define(function(require, exports, module) {
    require('jquery.validate')($);

    exports.run = function() {

        $("#signForm").validate({
            rules: {
                'register[username]': "required",
            },
            messages: {
                'register[username]': "请输入姓名",

            }
        });


    };

});