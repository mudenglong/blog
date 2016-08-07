define(function(require, exports, module) {
	window.$ = window.jQuery = require('jquery');
	require('bootstrap');
	// require('common/bootstrap-modal-hack');

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


	// 对Date的扩展，将 Date 转化为指定格式的String   
	// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，   
	// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)   
	// 例子：   
	// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423   
	// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18   
	Date.prototype.Format = function(fmt)   
	{ //author: meizz   
	  var o = {   
	    "M+" : this.getMonth()+1,                 //月份   
	    "d+" : this.getDate(),                    //日   
	    "h+" : this.getHours(),                   //小时   
	    "m+" : this.getMinutes(),                 //分   
	    "s+" : this.getSeconds(),                 //秒   
	    "q+" : Math.floor((this.getMonth()+3)/3), //季度   
	    "S"  : this.getMilliseconds()             //毫秒   
	  };   
	  if(/(y+)/.test(fmt))   
	    fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));   
	  for(var k in o)   
	    if(new RegExp("("+ k +")").test(fmt))   
	  fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));   
	  return fmt;   
	}  








	 // @charset "utf-8";
	 /**
	  * plugin for stockcode fill up
	  * @param {[type]} options [description]
	  * @author MDL@myhexin.com
	  */
	 var StocknameGenerater = function  (options) {
	     var defaultConfigs = {
	         
	         // 补全内容html拼接方式
	         // 允许的变量名:
	         // url, market, marketName, stockName, code
	         fillupTpl:'<li class="sng-stockli"><a href="<%url%>" data-market="<%market%>"><%code%> <span class="sng-s"><%stockName%></span></a></li>',

	         // 市场类别html显示方式(为空则不显示)
	         // eg: '<li class=""><%marketName%></li>'
	         // 请勿使用li 标签!
	         marketTpl:'<p class=""><%marketName%></p>',

	         // 补全内容展示方式 
	         // accordingInput : 追加到input框下面 @todo
	         // independentDiv : 独立的div
	         fillPaneShow: '',
	         fillPaneElement: '',

	         // 输入code的input，'.classname',  '#idName'
	         inputElement: '',
	         element:''
	     };
	     this.$inputPane = '';
	     this.$codeFillPane = '';
	     // qq 浏览器下，获取数据有bug 屏蔽
	     this.enableFillPane = true;
	     this.configs = $.extend({}, defaultConfigs, options);
	 }

	 StocknameGenerater.prototype.get = function(d){
	     return this.configs[d];
	 };

	 StocknameGenerater.prototype.getSearchBtn = function(d){
	     return $('.J_searchBtn');
	 };

	 // if (history.pushState) {
  //       if (href.match(/^\//) && this._loadUrl(href)) {
  //         return true;
  //       }
  //     } else {
  //       window.location = href;
  //     }

	 // Router.prototype._xhrStateChange = function(e) {
	 // 	return this.trigger('percentage', e.target.readyState * 25);
	 // };

	 // var animateLoadingBar = function(percentage) {
	 // 		this.$loading = $('.loading');
	 //       var complete, dimension;
	 //       dimension = 'height';
	 //       if (parseInt(window.innerWidth, 10) <= 600) {
	 //         dimension = 'width';
	 //       }
	 //       complete = null;
	 //       if (percentage >= 100) {
	 //         percentage = 100;
	 //         complete = (function(_this) {
	 //           return function() {
	 //             _this.$loading.css({
	 //               'transition': 'none',
	 //               '-moz-transition': 'none',
	 //               '-webkit-transition': 'none'
	 //             });
	 //             _this.$loading.removeData('css-transition');
	 //             return _this.$loading.css(dimension, '0');
	 //           };
	 //         })(this);
	 //         setTimeout(complete, 150);
	 //       }
	 //       if (!this.$loading.data('css-transition')) {
	 //         this.$loading.css({
	 //           'transition': dimension + ' .15s ease-in-out',
	 //           '-moz-transition': dimension + ' .15s ease-in-out',
	 //           '-webkit-transition': dimension + ' .15s ease-in-out'
	 //         });
	 //         this.$loading.data('css-transition', true);
	 //       }
	 //       return this.$loading.css(dimension, percentage + '%');
	 //     };



	 StocknameGenerater.prototype.init = function(){
	     var u = navigator.userAgent;
	     if(/MQQBrowser/.test(u)){
	         this.enableFillPane = false;
	     }
	     var that = this;
	     this.$inputPane = $(this.get('inputElement'));
	     this.$codeFillPane = $(this.get('fillPaneElement'));
	     this.getSearchBtn().val('取消');
	     this.$inputPane.val('');
	     $(this.get('element')).css('display', 'none');

	     var keyupFun = function  () {
	     	if(that.$inputPane.val() == ""){
	        	// var terms = '';
	         	// history.pushState({ search: terms }, terms, window.location.origin);
	         	window.location.href = location.origin;

	         }else{
	         	$.ajax({
	         		url: window.location.origin+'/jswidget/searchjson',
	         		dataType: 'json',
	         		data: {q: that.$inputPane.val()},
	         	})
	         	.done(function(d) {
	         		if (d.status === 'success') {
	         			var search = '/jswidget/search?q='+d.terms;
	         			var url = window.location.origin + search;
	         			history.pushState({ search: d.terms }, '', url);
	         			
	         			if (d.widgets.length) {
	         				var r = d.widgets;
	         				var str = '';
	         				for (var i = 0; i < r.length; i++) {
	         					str += that.fillHtml(r[i]);
	         				}
	         				$('#content').html('<div class="results"> <ul  id="jswidget-search-table" class="packages results">'+str+'</ul></div>');
	         			}else{
	         				$('#content').html('<div class="results"> <ul  id="jswidget-search-table" class="packages results"><div class="results"> <div class="no results"> 暂无该组件 </div> </div></ul></div>');
	         			}
	         		}
	         	})
				.fail(function() {

				})
				.always(function() {
				});

			}
	     		     
	    }

	    var debounce = function(idle, action){
	    	var last
	    	return function(){
	    		var ctx = this, args = arguments
	    		clearTimeout(last)
	    		last = setTimeout(function(){
	    			action.apply(ctx, args)
	    		}, idle)
	    	}
	    }

	     /*输入框内容变化时*/
	     this.$inputPane.keyup(debounce(400, keyupFun));

	    
	 };

	// 同 /src/Redwood/WebBundle/Resources/views/Jswidget/searchList-tr.html.twig 模板
	StocknameGenerater.prototype.fillHtml = function(d){
		var tpl = '<li id="jswidget-search-table-tr-{{ id }}"  class="package"> <h3> <a href="{{ jswidgetUrl }}"><b>{{ title }}</b></a> </h3> <span class="author"><em>by</em> <a href="/browse/authors/Sequoia%20Studios">{{ username }}</a> </span> <span class="meta"> <span class="installs" title="558 total unique installs">{{ view }} <em>views</em></span> <span class="installs" title="558 total unique installs">{{ admire }} <em>赞</em></span> </span> <div class="description"> {{ description }} </div> </li>';
		d['jswidgetUrl'] = window.location.origin+'/jswidget/'+ d.id;
        return tpl.replace(/{{ ([^}}]+)? }}/g, function(s0, s1){
			return d[s1];
		});

	 }

	 StocknameGenerater.prototype.render = function(d){
	     this.init();
	 };




	// 键盘精灵
	var sng = new StocknameGenerater({
	    element: '',
	    fillPaneShow: 'independentDiv',
	    fillPaneElement: '#jswidget-search-table',
	    inputElement: '#search'
	}).render(); 
	$('#search').trigger('focus');

	function getQueryVariable(variable) {
	    var url = window.location.href,
	        tempArr,
	        query = window.location.search.substring(1),
	        result = {};

	    var vars = query.split("&");
	    for (var i=0;i<vars.length;i++) {
	        var pair = vars[i].split("=");
	        result[pair[0]] = pair[1];
	    }
	    
	    return result[variable];
	}

	function setInputValue () {
		var search = getQueryVariable('q');
		if (search) {
			$('#search').val(search);
			$('#search').trigger('focus');
		};
		
	}
	setInputValue();


});