define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    // require('jquery.validate')($);
    require('common/jquery-validate').inject($);



	exports.run = function() {

        var $form = $("#tag-form");
        var $modal = $form.parents('.modal');
        var $table = $('#tag-table');
            

        $form.validate({
            onsubmit:true,
            // onfocusout:false,// 是否在获取焦点时验证 
            onkeyup :false,// 是否在敲击键盘时验证 
            rules: {
                'name':{
                    required: true,
                    remotecheck:[function (a, b) {}]
                }
            },
            messages:{
                'name':{
                    required: "用户名不能为空",
                    remotecheck:"标签已存在"
                }
            }
        });

        $form.on('submit', function(event) {
            if ($form.valid()) {
                event.preventDefault();
                $('#tag-create-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(html){

                    console.log(html);
                    var $html = $(html);
                    if ($table.find( '#' +  $html.attr('id')).length > 0) {
                        $('#' + $html.attr('id')).replaceWith($html);
                        Notify.success('标签更新成功！');
                    } else {
                        $table.find('tbody').prepend(html);
                        Notify.success('标签添加成功!');
                    }
                    $modal.modal('hide');
                });
            }
        });

        $modal.find('.delete-tag').on('click', function() {
            if (!confirm('真的要删除该标签吗？')) {
                return ;
            }

            var trId = '#tag-tr-' + $(this).data('tagId');
            $.post($(this).data('url'), function(html) {
                $modal.modal('hide');
                $table.find(trId).remove();
            });

        });

	};

});