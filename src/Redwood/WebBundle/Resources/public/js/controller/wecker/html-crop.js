define(function(require, exports, module) {
    var HtmlCropper = require("../widget/html-cropper");
    var DragResizer = require("../widget/drag-resizer");

    
    exports.run = function() {
    	var $picture = $('[data-role="html-img"]'),
            $cropBox = $('#demo');

    	var naturalWidth = $picture.data('naturalWidth'),
            naturalHeight = $picture.data('naturalHeight'),
            lines = Array();
   


        var demo = new HtmlCropper({ 
                element: '#demo', 
                img: '[data-role="html-img"]',
                trueSize: [naturalWidth, naturalHeight],
                setCutOffLine: true,
            }).render();

        //@todo 在初始化的时候一步实现
        var lines = demo.getLinePos();
        // 当 横线移动时
        demo.on('getLine', function(data){
            lines = data ? data : lines;
        });

        var boxs = Array();
        // @todo 当框框移动到边缘 返回的坐标有问题 没有边界
        var dragDiv = new DragResizer({ 
                element: '#demo', 
                canvasID: 'canvas',
                trueSize: [naturalWidth, naturalHeight],
              
            }).render();

        dragDiv.on('getBoxs', function(data){
            boxs = data ? data : '';
        });



        $('body').on('click', '[data-role=cropBtn]', function() {
            beforeGetResult();

            var data = {
                'lines': lines,
                'boxs' : boxs,
                'imageNaturalWidth' : naturalWidth,
                'imageNaturalHeight' : naturalHeight,
            };

            var url = window.location.href;

            $.post(url, {'postData':data}, function(results){
                if(results.status == 'success')
                {
                    $('[data-role="show-secret"]').val(results.secret);
                    $('#linkGetZip').data("secret", results.secret);
                    $('.cr-ok').css('display','block');

                    $('#demo').remove();
                    $('.cr-box').remove();
                }
                
            });

        });

        $('body').on('click', '#linkGetZip', function(){
            
            var url = $(this).data('url'),
            data = $(this).data('secret');
            if (data) {
                window.location.href = url + '?data=' +data; 
                
            }else{
                return false;                
            };

        });


    };

    function beforeGetResult () {
        $('#demo').css('display','none');
        $('.cr-tip').css('display','none');
        $('.cr-settings').css('display','none');
        $('.waiting-box').css('display','block');
    }

});

