define(function(require, exports, module) {

	var Notify = require('common/bootstrap-notify');

	exports.run = function() {

		var $table = $('#jswidget-table');

		// $table.on('click', '.lock-user, .unlock-user', function() {
		// 	var $trigger = $(this);

		// 	if (!confirm('真的要' + $trigger.attr('title') + '吗？')) {
		// 		return ;
		// 	}

  //           $.post($(this).data('url'), function(html){
  //               Notify.success($trigger.attr('title') + '成功！');
  //               var $tr = $(html);
  //               $('#' + $tr.attr('id')).replaceWith($tr);
  //           }).error(function(){
  //               Notify.danger($trigger.attr('title') + '失败');
  //           });
		// });

	};

});