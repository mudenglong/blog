define(function(require, exports, module) {
    var Widget = require('widget'),
        MouseCoords = require("../widget/html-cropper-mousecoords");
    require("../../../css/html-crop.css");

    /*rectangle obj */
    function Box(DragResizer) {
        this.dragResizerObj = DragResizer;
        this.state = this.dragResizerObj.options;
        this.x = 0;
        this.y = 0;
        this.w = 50; 
        this.h = 30;
        this.fillColor = 'rgba(0,255,0,.7)';
    }

    Box.prototype.contains = function(mx, my) {
        // All we have to do is make sure the Mouse X,Y fall in the area between
        // the shape's X and (X + Height) and its Y and (Y + Height)
        return  (this.x <= mx) && (this.x + this.w >= mx) &&
                (this.y <= my) && (this.y + this.h >= my);
    };

    Box.prototype.drawBox = function(ctx) {
        ctx.fillStyle = this.fillColor;
        ctx.fillRect(this.x, this.y, this.w, this.h);

        if (this.state.selection === this) {

            ctx.strokeStyle = this.state.selectionColor;
            ctx.lineWidth = this.state.selectionWidth;
            ctx.strokeRect(this.x,this.y,this.w,this.h);

            // draw the boxes
            var half = this.state.selectionBoxSize / 2;

            // 0  1  2
            // 3     4
            // 5  6  7

            // top left, middle, right
            this.state.selectionHandles[0].x = this.x-half;
            this.state.selectionHandles[0].y = this.y-half;

            this.state.selectionHandles[1].x = this.x+this.w/2-half;
            this.state.selectionHandles[1].y = this.y-half;

            this.state.selectionHandles[2].x = this.x+this.w-half;
            this.state.selectionHandles[2].y = this.y-half;

            //middle left
            this.state.selectionHandles[3].x = this.x-half;
            this.state.selectionHandles[3].y = this.y+this.h/2-half;

            //middle right
            this.state.selectionHandles[4].x = this.x+this.w-half;
            this.state.selectionHandles[4].y = this.y+this.h/2-half;

            //bottom left, middle, right
            this.state.selectionHandles[6].x = this.x+this.w/2-half;
            this.state.selectionHandles[6].y = this.y+this.h-half;

            this.state.selectionHandles[5].x = this.x-half;
            this.state.selectionHandles[5].y = this.y+this.h-half;

            this.state.selectionHandles[7].x = this.x+this.w-half;
            this.state.selectionHandles[7].y = this.y+this.h-half;


            ctx.fillStyle = this.state.selectionBoxColor;
            for (var i = 0; i < 8; i += 1) {

                var cur = this.state.selectionHandles[i];
                ctx.fillRect(cur.x, cur.y, this.state.selectionBoxSize, this.state.selectionBoxSize);
            }
        }
    };

  

    var DragResizer = Widget.extend({
        events: {

            'mousedown .img-wrap' : 'mousedownFun',
            'mousemove .img-wrap' : 'myMouseMove',
            'mouseup .html-crop-con' : 'myMouseUp',
            'dblclick .img-wrap' : 'myDbclick'
        },
        attrs: {
            /*
            * required only id
            * if id='canvas' , you could set canvasID: canvas
            */
            canvasID: null,
            canvas:null,

            /**
             * 真实图片大小 
             * example trueSize:[width, height]
             */
            trueSize: null,
            originImgWidth: 0,
            originImgHeight: 0,
            xscale: 1,
            yscale: 1,

            boxes: [],

            _canvasHeight: null,
            _canvasWidth: null,
            ctx: null,
            ctxClone: null,


            dragOffsetX: null,
            dragOffsetY: null,
            beDragging: false,
            

            beResizeDragging: false,
            //return which selectionHandles when dragging selectionHandles
            expectResize: -1,

        },
        options:{
            //八个角上的拖动手柄
            selection: null,
            selectionHandles : [],
            selectionColor: '#CC0000',
            selectionBoxColor: 'darkred',
            selectionBoxSize: 6,
            selectionWidth: 2,

        },
        myDraw: null,
        valid: false,

        setup: function()
        {
            var i, 
                selectionHandles = this.options.selectionHandles;





            for (i = 0; i < 8; i += 1) {
                selectionHandles.push(new Box(this));
            }
            this.set('selectionHandles', selectionHandles);



            this.initCanvas(this.get('canvasID'));

            // add rectangle
            // this.addRectangle(this, 200, 200, 40, 40, 'rgba(0,255,0,.7)');
            // this.addRectangle(25, 90, 25, 25, '#2BB8FF');


           

            //@todo 把时间refresh的操作显示出来
            this.draw();
            // this.debugStopDraw();
        },

        //Initialize a new Box, add it, and invalidate the canvas
        addRectangle:function (that, x, y, w, h, fillColor) 
        {

            var rectangle = new Box(that);
            rectangle.x = x;
            rectangle.y = y;
            rectangle.w = w
            rectangle.h = h;
            rectangle.fillColor = fillColor;

            var boxes = this.get('boxes');
            boxes.push(rectangle);
            this.set('boxes', boxes);
            // invalidate();
        },



        initCanvas: function(canvasID)
        {
            var canvasHtml5 = document.getElementById(canvasID);
            this.set('canvas', canvasHtml5);
            this.set('_canvasHeight', canvasHtml5.height);
            this.set('_canvasWidth', canvasHtml5.width);
            this.set('ctx', canvasHtml5.getContext('2d'));

            var str = '#'+canvasID;
            $canvas = $(str);
            if ($canvas.is('canvas')) {
                this.set('originImgWidth', $canvas.width());
                this.set('originImgHeight', $canvas.height());
       
            } else{
                throw new Error('Load canvas Error');
            };

            /*if options contain trueSize*/
            var trueSize = this.get('trueSize');
            if (trueSize) {
                this.set('xscale', trueSize[0] / this.get('originImgWidth'));
                this.set('yscale', trueSize[1] / this.get('originImgHeight'));
            }

            this.initCloneCanvas();

        },
        initCloneCanvas: function()
        {
            var canvasClone = document.createElement('canvas');
            canvasClone.height = this.get('_canvasHeight');
            canvasClone.width = this.get('_canvasWidth');
            this.set('ctxClone', canvasClone.getContext('2d'));
        },
        clear: function(ctx) {
            var w = this.get('_canvasWidth'),
                h = this.get('_canvasHeight');

            ctx.clearRect(0, 0, w, h);
        },
        drawshape: function(ctx, box, fillColor)
        {
            ctx.fillStyle = fillColor;
            ctx.fillRect(box.x, box.y, box.w, box.h);
        },

        draw: function()
        {

            var obj = this;
            var boxes = obj.get('boxes');
            myDraw = setInterval(function(){
                if (obj.valid == false) {
                    obj.clear(obj.get('ctx'));
                    obj.clear(obj.get('ctxClone'));

                    var l = boxes.length;
                    for (var i = l-1; i >= 0; i--) {
                        //@todo ????why yao clone  ,and why can't see clone

                        boxes[i].drawBox(obj.get('ctx')); 
                        // obj.drawshape(obj.get('ctxClone'), boxes[i], 'black');
                        // obj.drawshape(obj.get('ctx'), boxes[i], boxes[i].fill);
                    }

                 
                    obj.valid = true;
                }

            }, 30);
        },


        debugStopDraw: function()
        {
            clearInterval(myDraw);
        },

        getMouse: function(e)
        {
            var mouse = MouseCoords.getMouseOffset('[data-crop-html="img-wrap"]', e);
            return mouse;
        },

        mousedownFun: function(e)
        {
            var selectedBox;
            var mouse = this.getMouse(e),
                mouseX = mouse.x,
                mouseY = mouse.y;

            if (this.get('expectResize')!== -1) {
                this.set('beResizeDragging', true);
                return;
            }

            // var ctxClone = this.get('ctxClone');
            // this.clear(ctxClone);
            var boxes = this.get('boxes');

            // run through all the boxes
            var l = boxes.length;

            for (var i = l-1; i >= 0; i--) {
                if (boxes[i].contains(mouseX, mouseY)) {
                    selectedBox = boxes[i];
                    var dragOffsetX = mouseX - selectedBox.x;
                    this.set('dragOffsetX', dragOffsetX); 
                    var dragOffsetY = mouseY - selectedBox.y;
                    this.set('dragOffsetY', dragOffsetY);

                    this.set('beDragging', true); 
                    this.options.selection = selectedBox;
                    // this.set('selection', selectedBox); 

                    this.valid = false;

                    return;
                  }

            }
            

            if (this.options.selection) {
                this.options.selection = null;
                // this.set('selection', null); 
                this.valid = false; // Need to clear the old selection border
            }
     

        },
        myMouseMove: function(e){

            var mouse = this.getMouse(e),
                mx = mouse.x,
                my = mouse.y;
            var currentSelection = this.options.selection;

            if (this.get('beDragging')) {
            
                
                // We don't want to drag the object by its top-left corner, we want to drag it
                // from where we clicked. Thats why we saved the offset and use it here
                currentSelection.x = mx - this.get('dragOffsetX');
                currentSelection.y = my - this.get('dragOffsetY');   
                // Something is dragging so we must redraw
                this.valid = false;

                // return;
            }else if (this.get('beResizeDragging')) {
              // time ro resize!
          
              var oldx = currentSelection.x,
              oldy = currentSelection.y;
 
              // 0  1  2
              // 3     4
              // 5  6  7
              switch (this.get('expectResize')) {
                case 0:
                  currentSelection.x = mx;
                  currentSelection.y = my;
                  currentSelection.w += oldx - mx;
                  currentSelection.h += oldy - my;
                  break;
                case 1:
                  currentSelection.y = my;
                  currentSelection.h += oldy - my;
                  break;
                case 2:
                  currentSelection.y = my;
                  currentSelection.w = mx - oldx;
                  currentSelection.h += oldy - my;
                  break;
                case 3:
                  currentSelection.x = mx;
                  currentSelection.w += oldx - mx;
                  break;
                case 4:
                  currentSelection.w = mx - oldx;
                  break;
                case 5:
                  currentSelection.x = mx;
                  currentSelection.w += oldx - mx;
                  currentSelection.h = my - oldy;
                  break;
                case 6:
                  currentSelection.h = my - oldy;
                  break;
                case 7:
                  currentSelection.w = mx - oldx;
                  currentSelection.h = my - oldy;
                  break;
              }
              this.valid = false;
            }

            var selectionHandles = this.options.selectionHandles;
            var selectionBoxSize = this.options.selectionBoxSize;
            var that = e.currentTarget;

            // if there's a selection see if we grabbed one of the selection handles
            if (currentSelection !== null && !this.get('beResizeDragging')) {
         
                for (var i = 0; i < 8; i += 1) {
                    // 0  1  2
                    // 3     4
                    // 5  6  7
                    cur = selectionHandles[i];
              


                    // we dont need to use the ghost context because
                    // 确定鼠标悬停的是哪个托拽框
                    if (mx >= cur.x && mx <= cur.x + selectionBoxSize &&
                    my >= cur.y && my <= cur.y + selectionBoxSize) {

                        // we found one!
                        this.set('expectResize', i);

                        this.valid = false;
              
                        switch (i) {
                          case 0:
                            that.style.cursor='nw-resize';
                            break;
                          case 1:
                            that.style.cursor='n-resize';
                            break;
                          case 2:
                            that.style.cursor='ne-resize';
                            break;
                          case 3:
                            that.style.cursor='w-resize';
                            break;
                          case 4:
                            that.style.cursor='e-resize';
                            break;
                          case 5:
                            that.style.cursor='sw-resize';
                            break;
                          case 6:
                            that.style.cursor='s-resize';
                            break;
                          case 7:
                            that.style.cursor='se-resize';
                            break;
                        }
                        return;
                    }

                }
                // not over a selection box, return to normal
                this.set('beResizeDragging', false);
                this.set('expectResize', -1);
                that.style.cursor = 'auto';
            }








        },

        myMouseUp: function(e)
        {
            this.set('beDragging', false);
            this.set('beResizeDragging', false);
            this.set('expectResize', -1);

            var currentSelection = this.options.selection;

            if (currentSelection !== null) {
              if (currentSelection.w < 0) {
                  currentSelection.w = -currentSelection.w;
                  currentSelection.x -= currentSelection.w;
              }
              if (currentSelection.h < 0) {
                  currentSelection.h = -currentSelection.h;
                  currentSelection.y -= currentSelection.h;
              }
            }

            var boxesPos = this.getBoxsPos(this.get('boxes'));
            this.trigger('getBoxs', boxesPos);

        },
        getBoxsPos: function(boxes)
        {
            var boxesPos = {},
                yscale = this.get('yscale'),
                l = boxes.length;
            for (var i =0; i < l; i++) {
                //@todo ????why yao clone  ,and why can't see clone

      // Math.round(top * yscale)
                boxesPos[i] = {
                    'x' : Math.round((boxes[i].x)*yscale),
                    'y' : Math.round((boxes[i].y)*yscale),
                    'w' : Math.round((boxes[i].w)*yscale),
                    'h' : Math.round((boxes[i].h)*yscale),

                };
            }

            return boxesPos;



        },

        myDbclick: function(e)
        {
            var mouse = this.getMouse(e);
            this.addRectangle(this, (mouse.x - 20), (mouse.y - 20), 80, 40, 'rgba(0,255,0,.7)');
            this.valid = false;
        },


    });

    module.exports = DragResizer;

});