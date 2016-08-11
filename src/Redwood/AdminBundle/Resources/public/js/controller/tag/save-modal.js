define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require('jquery.validate')($);


	exports.run = function() {
        var $form = $("#tag-form");
        var $modal = $form.parents('.modal');
        var $table = $('#tag-table');
            

        $form.validate({
            onsubmit:true,
            rules: {
                'name':{
                    required: true
                }
            },
            messages:{
                'name':{
                    required: "用户名不能为空"
                }
            }
        });

        $form.on('submit', function() {
            if ($form.valid()) {
debugger;
                $('#tag-create-btn').button('submiting').addClass('disabled');

                $.post($form.attr('action'), $form.serialize(), function(html){
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