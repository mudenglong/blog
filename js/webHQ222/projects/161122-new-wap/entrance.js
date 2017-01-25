// @charset "utf-8";
require.config({
    baseUrl:'../../resources/js',
    paths: {

        <<content>>
    }
});

require(['KlineVolChart', 'KlinePriceChart', 'FsPriceChart', 'FsVolChart'], function(KlineVolChart, KlinePriceChart, FsPriceChart, FsVolChart) {
    
    window.hxc3 = {
        'KlineVolChart':KlineVolChart,
        'KlinePriceChart':KlinePriceChart,
        'FsPriceChart':FsPriceChart,
        'FsVolChart':FsVolChart
    }

});
