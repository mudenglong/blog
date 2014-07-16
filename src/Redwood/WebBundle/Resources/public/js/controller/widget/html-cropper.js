define(function(require, exports, module) {
    var Widget = require('widget'),
        Line = require("../widget/html-cropper-line"),
        MouseCoords = require("../widget/html-cropper-mousecoords");
    require("../../../css/html-crop.css");

    var HtmlCropper = Widget.extend({
        attrs: {
            /*required  
            * example  img: #demo-img
            */
            img: null, 

            /**
             * 真实图片大小 
             * example trueSize:[width, height]
             */
            trueSize: null,
            setCutOffLine: false,
            perImageHeight: 200,


            /*do not change under value*/
            originImgWidth: 0,
            originImgHeight: 0,
            xscale: 1,
            yscale: 1,
            /*
            * whether element is draging
            */
            beDraging: false,

            //@todo
            xlineTemplate: null,

            // Callbacks / Event Handlers
            getLinePos: function () { }


        },
        events: {
            'mousedown  [data-crop-html^=wrap]' : 'mousedownFun',
            'mouseup .html-crop-con' : 'mouseupFun',
        },

        setup: function()
        {
            if (this.get('img') == null) {
                throw new Error('Please set img like --> img:#demo-img');
            }
 
            this._initLoadImg();

            /*if options contain trueSize*/
            var trueSize = this.get('trueSize');
            if (trueSize) {
                this.set('xscale', trueSize[0] / this.get('originImgWidth'));
                this.set('yscale', trueSize[1] / this.get('originImgHeight'));
            }



            /*if options contain setCutoffLine*/
            if (this.get('setCutOffLine') != false) {
                this.setCutOffLine(this.get('originImgHeight'), this.get('perImageHeight'));
            }

        },

        /*private function*/
        _initLoadImg: function()
        {
            $img = $(this.get('img'));
            if ($img.is('img')) {
                this.set('originImgWidth', $img.width());
                this.set('originImgHeight', $img.height());
       
            } else{
                throw new Error('Load img Error');
            };
            

        },

        _convertNaturalSize: function(origin, scale)
        {
            if (scale == 'xscale') {
                return origin*this.get('xscale');
            } else if(scale == 'yscale'){
                return origin*this.get('yscale');
            }else{
                throw new Error('_convertNaturalSize function Error');
                return false;
            };
            
        },

        _convertOriginSize: function(natural, scale)
        {
            if (scale == 'xscale') {
                return natural/this.get('xscale');
            } else if(scale == 'yscale'){
                return natural/this.get('yscale');
            }else{
                throw new Error('_convertOriginSize function Error');
                return false;
            };
            
        },

        _calculateLineNum: function(imgHeight, perImageHeight)
        {
            return Math.floor(imgHeight / perImageHeight);
        },

        setCutOffLine: function(originImgHeight, perImageHeight)
        {
            var originPerImageHeight = Math.round(this._convertOriginSize(perImageHeight, 'yscale'));
            var lineNum = this._calculateLineNum(originImgHeight, originPerImageHeight);
            
            var lines = Array();
            var top = 0;

            for (var i = 0; i < lineNum; i++) 
            {
                top += originPerImageHeight;
                lines[i] = new Line({
                    template: '<div><div class="html-crop-xline-wrap" data-crop-html="wrap{{number}}" style="position: absolute; width:100%;top:{{top}}"><div class="html-crop-xline" data-crop-html="xline{{number}}" ></div></div></div>',
                    model: {
                        'number': i,
                        'top': top +'px',
                    },
                    parentNode: '[data-crop-html="img-wrap"]',
                }).render();
            };

        },

        mousedownFun:function(e)
        {
            this.set('beDraging', true);
            if (this.get('beDraging')) {
                this.$('.img-wrap').on('mousemove', { lineTarget: e.currentTarget }, this.dragLine);
            };       
        },

        mouseupFun:function()
        {
            this.set('beDraging', false);
            this.$('.img-wrap').off('mousemove',this.dragLine);

            var lines = this.getLinePos();
            this.trigger('getLine', lines);

        },

        dragLine: function(ev) {
            var mousePos = MouseCoords.getMouseOffset('[data-crop-html="img-wrap"]', ev);
            $(ev.data.lineTarget).css({'top':mousePos.y});

        },

        getLinePos: function()
        {
            var obj = this,
                lines = {},
                yscale = obj.get('yscale');

            $('[data-crop-html^=wrap]').each(function( index ) {
                var topArr = $(this).css('top').split("px"),
                    top = topArr[0],
                    index = index+1;

                lines[0] = {
                    'naturalTop' : 0,
                    'top' : 0,
                };
                
                // if(yscale != 1)
                // {
                lines[index] = {
                    'naturalTop' : Math.round(top * yscale),
                    'top' : parseInt(top),
                };
                // }
            });

            return lines;
        }

    });

    module.exports = HtmlCropper;

});