// @charset "utf-8";
({
    baseUrl: "../../resources/js",
    name: "almond",
    include: "../../projects/1dd13dea2d3b86785ed9c31adfd64832/entrance.js",
    wrap: true,
    out: "release/wapa.min.js",
    optimize: 'uglify',

    paths: {
                            	   'KlineVolChart' : 'empty',
                    	   'FsVolChart' : 'hexinStock/grid/FsVolChart',
                    	   'KlinePriceChart' : 'empty',
                    	   'FsPriceChart' : 'hexinStock/grid/FsPriceChart',
                            'aaa' : 'aaa'
    }
})
