define(function(require, exports, module) {

    var MouseCoords = {

        /*return 
        * mouse coordinates on container
        * 鼠标偏离container的实时x,y
        */
        getMouseOffset: function(container, ev)
        {
            var mousePos = MouseCoords._mouseCoords(ev);
            var containerPos = MouseCoords._getContainerOffset(container);

            var res = {
                'x':Math.max((mousePos.x - containerPos.x),0),
                'y':Math.min((mousePos.y - containerPos.y), containerPos.height),
            };

            return res;

        },

        /*return mouse coordinates on windows*/
        _mouseCoords:function(event)
        {
            var e = event || window.event;
            var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
            var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
            var x = e.pageX || e.clientX + scrollX;
            var y = e.pageY || e.clientY + scrollY;
            return { 'x': x, 'y': y };
        },

        _getContainerOffset: function(container)
        {

            var offset = $(container).offset();
            var height = $(container).height();
            return {
                'x' : Math.round(offset.left),
                'y' : Math.round(offset.top),
                'height' : height,
            };
        },
    }

    module.exports = MouseCoords;

});