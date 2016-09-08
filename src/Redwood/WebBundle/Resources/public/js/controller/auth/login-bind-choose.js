define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require('common/jquery-validate').inject($);

    exports.run = function() {

        var $form = $('#set-bind-new-form');
        var $submitBtn = $('#set-bind-new-btn');

        $form.validate({
            onsubmit:false,
            // onfocusout:false,// 是否在获取焦点时验证 
            onkeyup :false,// 是否在敲击键盘时验证 
            rules: {
                'username':{
                    required: true,
                    minlength: 6,
                    maxlength: 18,
                    alphanumeric:true,
                    remotecheck:[function (a, b) {}]
                },
                'set_bind_emailOrMobile':{
                    required: true,
                    email:true,
                    email_remotecheck:[function (a, b) {}]
                }
            },
            messages:{
                'username':{
                    required: "用户名不能为空",
                    minlength: "最少长度为6个字符",
                    maxlength: "最长18个字符",
                    remotecheck:'用户名已存在,可选择"绑定已有账号"或重新输入'
                },
                'set_bind_emailOrMobile':{
                    required: "邮箱不能为空",
                    email:"邮箱格式有误",
                    email_remotecheck:'该邮箱已注册, 可选择"绑定已有账号"或重新输入'
                }
            }
        });

        $form.on('submit', function(event) {
            event.preventDefault();

            if ($form.valid()) {
                $form.find('[type=submit]').button('loading');
                $("#bind-new-form-error").hide();

                $.post($form.attr('action'), $form.serialize(), function(response) {
                    console.log(response);
                    debugger;
                    if (!response.success) {
                        $('#bind-new-form-error').html(response.message).show();
                        return ;
                    }
                    Notify.success('登录成功，正在跳转至首页！');
                    window.location.href = response._target_path;

                }, 'json').fail(function() {
                    Notify.danger('登录失败，请重新登录后再试！');
                }).always(function() {
                   $form.find('button[type=submit]').button('reset');
                });
            }
        });

        // checkbox 这里我影藏了
        $('#user_terms input[type=checkbox]').on('click', function() {
            if($(this).attr('checked')) {
                 $(this).attr('checked',false);
            } else {
                $(this).attr('checked',true);
            };
        });
       
    };

});