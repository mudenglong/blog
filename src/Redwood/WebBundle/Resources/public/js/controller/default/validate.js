define(function(require, exports, module) {

    require('jquery.validate')($);

    exports.run = function() {
    	$.validator.addMethod(
            "jswidgetUrl",
            function(value, element){
                /*var dotPos = value.indexOf('.');
                return value > 0 && dotPos < 0 && (dotPos > 0 && value.substring(dotPos + 1) <= 2);*/
                
                return value && /^\d*\.?\d{0,2}$/.test(value);
            },
            "组件根目录需位于'http://testm.10jqka.com.cn/mobile/info/widgets/zhouzy/1.html'"
        );

    }

});