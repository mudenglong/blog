define(function(require, exports, module) {
	var TagCollector = require("../widget/tag-collector");
	exports.run = function() {
    	var demo = new TagCollector({ element: '#demo' }).render();
    	demo.on('beforeEnterValue', function(value){
    		console.log('aaaaa:',value);
    	});
	};

});