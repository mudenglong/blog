define(function(require, exports, module) {
	window.$ = window.jQuery = require('jquery');
	require('bootstrap');
	require('common/bootstrap-modal-hack');

	exports.load = function(name) {
		require.async('./controller/' + name + '.js?' + window.app.version, function(controller){
			if ($.isFunction(controller.run)) {
				controller.run();
			}
		});
	};
	window.app.load = exports.load;

	if (app.controller) {
		exports.load(app.controller);
	}

	$( document ).ajaxSend(function(a, b, c) {
	    if (c.type == 'POST') {
	        b.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'));
	    }
	});

});