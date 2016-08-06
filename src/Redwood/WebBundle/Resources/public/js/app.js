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





	 // @charset "utf-8";
	 /**
	  * 股票代码自动补全
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

	     /*输入框内容变化时*/
	     this.$inputPane.keyup(function(){  
	         if(that.$inputPane.val() == ""){

	            window.location.href = '/';
	         }else{
	         	// $.ajax({
	         	// 	url: window.location.origin+'/jswidget/search',
	         	// 	dataType: 'json',
	         	// 	data: {q: that.$inputPane.val()},
	         	// })
	         	// .done(function() {
	         	// 	debugger;
	         	// 	console.log("success");
	         	// })
	         	// .fail(function() {
	         	// 	console.log("error");
	         	// })
	         	// .always(function() {
	         	// 	console.log("complete");
	         	// });
	         	
	            window.location.href = window.location.origin+'/jswidget/search?q='+that.$inputPane.val();
	         }
	     });

	    
	 };

	 StocknameGenerater.prototype.activeItems = function(selectedItem){
	     this.selectedItem = selectedItem;
	     if (selectedItem === null) {
	         this.$codeFillPane.hide();
	         return;
	     }
	     if (selectedItem < 0) {
	         this.selectedItem = this.$codeFillPane.find('li').length-1;
	     }
	     if (selectedItem >= this.$codeFillPane.find('li').length) {
	         this.selectedItem = 0;
	     }
	     this.$codeFillPane.find('li')
	         .removeClass('selected')
	         .eq(this.selectedItem).addClass('selected');

	     this.$codeFillPane.show();
	 }

	 StocknameGenerater.prototype.gotoStockpage = function(){
	     this.activeItems(null);
	     
	     this.$codeFillPane.find('.selected').children('a').attr('href');
	     var selectedItem = this.$codeFillPane.find('.selected');
	     if (selectedItem.length) {
	         window.location.href = selectedItem.children('a').attr('href');
	     }else{
	         window.location.href = "http://m.10jqka.com.cn/stockpage/hs_" + this.$inputPane.val() + '/#refCountId=R_554996b7_600';
	     }
	 }

	 // 输入 code
	 StocknameGenerater.prototype.inputCode = function(){
	     var that = this;
	     var resultArr = [];
	     var resultLiStr = '';
	     var tempObj = {};
	     var lastMarket = '';
	     
	     var marketNameMapGenral = {
	         '1':'沪深',
	         '6':'港股',
	         '9':'美股',
	     }

	     var marketMap = {
	         '1':'hs',
	         '6':'hk',
	         '9':'usa',
	     }
	     
	     this.$inputPane.keyup(function(event) {
	         if (that.enableFillPane && (event.keyCode > 40 || event.keyCode == 8 || event.keyCode == 32)) {
	             // $(that.get('fillPaneElement')).empty();

	             // Keys with codes 40 and below are special (enter, arrow keys, escape, etc.).
	             // Key code 8 is backspace.
	             var inputCodeLength = that.$inputPane.val().length;

	             if(inputCodeLength > 0 && inputCodeLength < 8){
	                 resultArr.length = 0; 
	                 $.getJSON("http://news.10jqka.com.cn/public/index_keyboard.php?search-text=" + that.$inputPane.val() + "&jsoncallback=?", function(json){
	                     lastMarket = '';
	                     resultLiStr = '';
	                     if (json.length > 0) {
	                         that.$codeFillPane.empty();
	                         $.each(json, function(index, item) {
	                             resultArr[index] = item.split("||");
	                             tempObj = {};
	                             if (/^[1|6|9]/.test(resultArr[index][0])) {
	                                 var arrSplit = resultArr[index][1].split(' ');
	                                 tempObj = {
	                                     marketName : marketNameMapGenral[resultArr[index][0]],
	                                     market : resultArr[index][0],
	                                     stockName: arrSplit[1],
	                                     url : 'http://m.10jqka.com.cn/stockpage/'+ marketMap[resultArr[index][0]] + '_' + arrSplit[0] + '/#refCountId=R_554996b7_600',
	                                     code : arrSplit[0]
	                                 }
	                                 // 需要市场名称的html
	                                 if (tempObj.marketName != lastMarket && that.get('marketTpl')) {
	                                     lastMarket = tempObj.marketName;
	                                     resultLiStr += that.get('marketTpl').replace(/<%([^%>]+)?%>/g, function(s0, s1){
	                                         return tempObj[s1];
	                                     });
	                                 };

	                                 if (tempObj.market === '6') {
	                                     tempObj.code = 'HK'+tempObj['code'].substr(1);
	                                     tempObj.url = 'http://m.10jqka.com.cn/stockpage/'+ marketMap[resultArr[index][0]] + '_' + tempObj.code + '/#refCountId=R_554996b7_600';
	                                 }
	                                 
	                                 resultLiStr += that.get('fillupTpl').replace(/<%([^%>]+)?%>/g, function(s0, s1){
	                                     return tempObj[s1];
	                                 });
	                                
	                             } 
	                         });
	                         
	                         that.showFillPaneHtml(resultLiStr);
	                         that.activeItems(0);
	                     }
	                 });
	             }else{
	                 that.activeItems(null);
	             }
	         }else if (event.keyCode == 38 && that.selectedItem !== null) {
	             // User pressed up arrow.
	             that.activeItems(that.selectedItem - 1);
	             event.preventDefault();
	         }
	         else if (event.keyCode == 40 && that.selectedItem !== null) {
	             // User pressed down arrow.
	             that.activeItems(that.selectedItem + 1);
	             event.preventDefault();
	         }
	         else if (event.keyCode == 27 && that.selectedItem !== null) {
	             // User pressed escape key.
	             that.$inputPane.val('');
	             that.activeItems(null);
	         }
	     }).keypress(function(event) {
	         if (event.keyCode == 13 && that.selectedItem !== null) {
	             // User pressed enter key.
	             that.gotoStockpage();
	             event.preventDefault();
	         }
	     })

	 //.blur(function(event) {
	 //     setTimeout(function() {
	 //         that.activeItems(null);
	 //     }
	 //     , 250);
	 // });

	 };


	 StocknameGenerater.prototype.showFillPaneHtml = function(liStr){
	     this.$codeFillPane.append('<ul class="sng-ul"></ul>');
	     $('.sng-ul').append(liStr);
	 }

	 StocknameGenerater.prototype.triggerInput = function(d){
	     var that = this;
	     $('.hexm-search-btn').click(function (e) {
	         $(".main-content").css('display', 'none');;
	         $(".hexm-top").css('display', 'none');;
	         $(that.get('element')).css('display', 'block');
	     });
	 }

	 StocknameGenerater.prototype.hideInput = function(){
	     $(".main-content").css('display', 'block');;
	     $(".hexm-top").css('display', 'block');;
	     $(this.get('element')).css('display', 'none');
	 }

	 StocknameGenerater.prototype.clickSearchBtn = function(d){
	     var that = this;
	     this.getSearchBtn().click(function(){
	         if($(this).val() == "取消" || (($(this).val() == "搜索")&&(that.$inputPane.val() == ""))){
	             that.hideInput();
	         }else{
	             /*点击搜索，跳转到相应股票的搜索内容页面*/
	             that.$codeFillPane.find('.selected').children('a').attr('href');
	             var selectedItem = that.$codeFillPane.find('.selected');
	             if (selectedItem.length) {
	                 window.location.href = selectedItem.children('a').attr('href');
	             }else{
	                 window.location.href = "http://m.10jqka.com.cn/stockpage/hs_" + that.$inputPane.val() + '/#refCountId=R_554996b7_600';
	             }
	             
	         }
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