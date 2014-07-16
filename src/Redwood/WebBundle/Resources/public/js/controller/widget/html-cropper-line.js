define(function(require, exports, module) {
    var Widget = require('widget'),
        Templatable = require('templatable');

    var Line = Widget.extend({
        Implements: Templatable,

        attrs: {

            /*  
            * auto init by function _initContainer()
            */
            container: null, 
            

            /*do not change under value*/
      

        },
        events: {
            // 'mousemove #demo' : 'dragLine',
            // 'mousedown [data-crop-html^=wrap]' : 'dragLine',
        },


        setup: function(){
            this._initContainer(this.get('parentNode'));

        },
        _initContainer: function(container)
        {
            this.set('container', container);
        },

        dragLine: function(ev) {
            console.log("111");
            this.on('getHtml', function(a){
                console.log(a);
            });
            this.trigger('getTempLineHtml', '12121212');
           
            // var mousePos = this.getMouseOffset(this.get('container'), ev);
            // return mousePos;



        },
        // dragLineByMouse: function(mousePos){
        //     console.log(mousePos);
        //     var wrapHtml = this.$('[data-crop-html^=wrap]')[0];

        //     $(wrapHtml).css({'top':mousePos.y});
        //     // $(this).find('[data-crop-html^=wrap]').css({'border':'1px solid red'});

            
        // },

      


    });

    module.exports = Line;

});