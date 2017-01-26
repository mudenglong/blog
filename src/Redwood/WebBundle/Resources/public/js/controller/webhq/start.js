define(function(require, exports, module) {

    exports.run = function() {

        var chartType = {
            'fs':'fs',
            'kline':'kline'
        };
        
        $('#startSetting').click(function(event) {
            var typeArr = '';
            $(".JChartType").each(function(){
                if($(this).is(':checked')){
                    typeArr.push($(this).data('charttype'));
                    debugger;    
                }
            });
            // $("input[type='checkbox']").is(':checked')
        });

    }

});