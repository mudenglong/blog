// @charset "utf-8";


require.config({
    baseUrl:'../../resources/js',
    paths: {

                                   'FsVolChart' : 'hexinStock/grid/FsVolChart',
                           'FsPriceChart' : 'hexinStock/grid/FsPriceChart',
                            'aaa':'aaa'
    }
});


require(['FsVolChart','FsPriceChart', 'aaa'], function(FsVolChart,FsPriceChart, aaa) {
    window.hxc3 = {
                                    'FsVolChart' : FsVolChart,
                           'FsPriceChart' : FsPriceChart,
                            'aaa':aaa
    }

});