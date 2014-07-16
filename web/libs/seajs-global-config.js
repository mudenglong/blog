seajs.config({
	alias: {
		'jquery': 'jquery/1.10.1/jquery',
		'$': 'jquery/1.10.1/jquery',
		'$-debug': 'jquery/1.10.1/jquery-debug',
		'bootstrap': 'bootstrap/3.0.3/js/bootstrap',
		'jquery.validate' : 'jquery.validate/1.11.1/jquery.validate',
		"jquery.jcrop": "jquery-plugin/jcrop/0.9.12/jcrop",
		"jquery.jcrop-css": "jquery-plugin/jcrop/0.9.12/jcrop.css",
		'widget': 'arale/widget/1.1.1/widget',
		"templatable": "arale/templatable/0.9.1/templatable",
	},

	// 预加载项
	preload: [
	this.JSON ? '' : 'json'],

	// 路径配置
	// paths: {
	// 	'common': 'common/'
	// },

	// 变量配置
	vars: {
		'locale': 'zh-cn'
	},

	charset: 'utf-8',

	debug: app.debug
})