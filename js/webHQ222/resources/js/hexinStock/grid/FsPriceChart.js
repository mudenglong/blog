// @charset "utf-8";
define(function() {

    // kline 价格图
    // 说明:
    // 数据分: 价格线, 均价线(大部分的个股市场有, 部分市场没有)
    // 样式分: 普通线, 闭合线

    var FsPriceChart = function(ctx, d, x, configs, grid){
        this.y = null;

        // 自动生成
        // this.yTickFormat = function([Aixs arguments..]){
        // 
        // }
        this.yTickFormat = null;

        this.render(ctx, d, x, configs, grid);
    };

    FsPriceChart.prototype.render = function (ctx, d, x, configs, grid) {
        var data = d;

        var drawLineType = configs.draw;

        // 生成　增加了昨收字段的　y轴自定义样式回调函数
        this.createYTickFormat(configs);

    }

    return FsPriceChart;
});
