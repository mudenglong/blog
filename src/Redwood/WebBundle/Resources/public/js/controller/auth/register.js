define(function(require, exports, module) {
    require('jquery.validate')($);

    exports.run = function() {

        $("#signForm").validate({
            rules: {
                'register[username]':{
                    required: true,
                    minlength: 3
                },
                'register[password]':{
                    required: true,
                    minlength: 6,
                    maxlength: 16
                },
                'register[repassword]':{
                    required: true,
                    equalTo: "#register_password"
                }
            },
            messages:{
                'register[username]':{
                    required: "用户名不能为空",
                    minlength: "用户名的最小长度为3"
                },
                'register[password]':{
                    required: "密码不能为空",
                    minlength: "密码长度不能少于6个字符",
                    maxlength: "密码长度不能超过16个字符"
                },
                'register[repassword]':{
                    required: "确认密码不能为空",
                    equalTo: "确认密码和密码不一致"
                }
            }
        });


    };

});