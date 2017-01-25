// @charset "utf-8";
define(function() {

    // kline 价格图
    // 说明:
    // 数据分: 价格线, 均价线(大部分的个股市场有, 部分市场没有)
    // 样式分: 普通线, 闭合线
    var KlinePriceChart = function(ctx, d, x, configs, grid, gridBaseCtx){
        this.y = null;

        // 自动生成
        // this.yTickFormat = function([Aixs arguments..], {pre:pre, exampleStr:..}){   }
        this.yTickFormat = null;

        // 自动生成
        // this.yTickFormat = function([Aixs arguments..], {pre:pre, exampleStr:..}){   }
        this.xTickFormat = null;

        // 绘图中所有的位置信息
        // this.posInfo = null;

        this.render(ctx, d, x, configs, grid, gridBaseCtx);
    };


    KlinePriceChart.prototype.render = function (ctx, d, x, configs, grid, gridBaseCtx) {
        var data = d;

        // 新股未上市,  要可以绘制出框
        if (!data[0].s  && !data[0].o && !data[0].c && !data[0].i) {
            data = [{a: 0, c: 0, code: "hs_300033", h: 0, i: 0, ma5: null, ma10: null, ma30: null, n: 0, np: 0, o: 0, s: 0, t: ""
            }, {a: 1, c: 2, code: "hs_300033", h: 1, i: 1, ma5: null, ma10: null, ma30: null, n: 2, np: 1, o: 1, s: 1, t: ""}];

            // status = {type:'noKLine', msg:'没有k线数据'};
        }

    }
    

    return KlinePriceChart;
});
