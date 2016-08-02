define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $table = $('#jswidget-table');

        $table.on('click', '.delete-jswidget', function() {
            var $trigger = $(this);

            if (!confirm('真的要' + $trigger.attr('title') + '吗？')) {
                return ;
            }

            $.post($(this).data('url'), function() {
                Notify.success('删除成功！');
                window.location.reload();
            });
        });
    };

});