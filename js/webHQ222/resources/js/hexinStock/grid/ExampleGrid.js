// @charset "utf-8";
define(function() {

    // 1.先确认新建的类是组合模块，还是叶子模块?
    // 例如分时图来说　
    // 组合模块是 FsGridComposite.js 　
    // 叶子模块是 CommonGrid.js ==> 可实现　价格面板　成交量面板　资金流向面板
    // 
    // 组合模块本身也支持再组合:
    // 
    //        组合
    //         |  
    //     |-------|
    //    组合      |
    //     |       |
    //  ---|---    |
    //  |     |    |
    //  |     |    |
    // 叶子  叶子   叶子
    // 
    // 2. 任意图像 都是面板拼接，　每个面板主要支持下面几个通用方法
    // (叶子模块实现和组合模块实现有所不同，下面公用函数详细写明了差异)

    var ExampleGrid = function () {
        this.grids = [];
        // 必须要有，子面板向上冒泡传递的途径
        this.parent = null;
    }

    /**
     * 属于组合模块，确定是子模块的不需要实现
     * @param {[type]} grid [description]
     */
    ExampleGrid.prototype.add = function(grid){
        // 必须要有，子面板向上冒泡传递的途径
        grid.parent = this;

        this.grids.push(grid);
    }

    ExampleGrid.prototype.removeGrid = function(grid){
        if(!this.parent){
            return;
        }
        for (var grids = this.parent.grids, i = grids.length - 1; i >= 0; i--) {
            var grid = grids[i];
            if (grid == this) {
                grids.splice(i, 1);
            }
        }
    }

    // 绘制背景网格线
    ExampleGrid.prototype.drawGrid = function(ctx){

        // 若为叶子节点(即最小节点，例如CommonGrid.js)
        // do something ... 自定义

        // 若为组合节点(即父节点，例如FsGridComposite.js)
        for (var i = 0; i < this.grids.length; i++) {
            this.grids[i].drawGrid(ctx);
        }
    }


    return ExampleGrid;
});
