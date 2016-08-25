define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $table = $('#jswidget-table');

        $table.on('click', '.delete-jswidget', function() {
            if (!confirm('确认要删除组件吗？')) return false;
            var $btn = $(this);
            var jswidgetId = $btn.data('target');
            var token = $('meta[name=csrf-token]').attr('content');
 
            $.post($btn.data('url'), function(res) {
                if (res) {
                    $('#jswidget-table-tr-' + jswidgetId).remove();
                    Notify.success('删除成功!');
                } else {
                    Notify.danger($btn.attr('title') + '失败');
                }
            }, 'json');
           
        });

    };

});