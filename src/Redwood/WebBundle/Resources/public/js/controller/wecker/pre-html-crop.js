define(function(require, exports, module) {
    
    exports.run = function() {

        $('body').on('click', '[data-role=getZip]', function() {
            beforeGetResult();

            var url = $('[data-role="getZip"]').data('url'),
            data = $('[data-role=getSecret]').val();

            window.location.href = url + '?data=' +data; 

        });


    };

    function beforeGetResult () {
        console.log('waiting for the moment!');
    }

});

