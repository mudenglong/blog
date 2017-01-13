define(function(require, exports, module) {
    require("jquery.jqColorPicker");

    exports.run = function() {
        function obj2string(o){ 
               var r=[]; 
               if(typeof o=="string"){ 
                   return "\""+o.replace(/([\'\"\\])/g,"\\$1").replace(/(\n)/g,"\\n").replace(/(\r)/g,"\\r").replace(/(\t)/g,"\\t")+"\""; 
               } 
               if(typeof o=="object"){ 
                   if(!o.sort){ 
                      for(var i in o){ 
                          r.push(i+":"+obj2string(o[i])); 
                      } 
                      if(!!document.all&&!/^\n?function\s*toString\(\)\s*\{\n?\s*\[native code\]\n?\s*\}\n?\s*$/.test(o.toString)){ 
                          r.push("toString:"+o.toString.toString()); 
                      } 
                      r="{"+r.join()+"}"; 
                  }else{ 
                      for(var i=0;i<o.length;i++){ 
                          r.push(obj2string(o[i])) 
                      } 
                      r="["+r.join()+"]"; 
                  } 
                  return r; 
              } 
              return o.toString(); 
          }


        var scale = function(){
            var range, domain;
            var p = function(x){
                if (typeof (x) == 'object' && !x) {return null;}
                if (isNaN(domain[0]) || isNaN(domain[1])) {
                    return 0;
                }
                var a = (range[1] - range[0]) / (domain[1] - domain[0]);
                if (!isFinite(a)) {
                    a = 0;
                }
                var b = range[0] - a * domain[0];
                return a * x + b;
            }
            p.domain = function(x) {
                if (arguments.length == 0 && domain) {
                    return domain;
                };
                if ((isNaN(x[0]) || !isFinite(x[0]) || x[0] == 0) && (isNaN(x[1]) || !isFinite(x[0]) || x[1] == 0)) {
                    domain = [0, 1];
                } else {
                    domain = x;
                }
                return p;
            }
            p.range = function(x) {
                if (arguments.length == 0 && range) {
                    return range;
                }
                range = x;
                return p;
            }
            return p;
        }

        var initKlineColorBox = function(){
            var d = main.testklinedata;

            var canvasW = $('#klineBox').width() || 876;
            var canvasH = $('#klineBox').height() || 400;
            var chartStartX = 60;
            var klinePriceH = 270;
            var klinewidth = 20;
            var ss = 22;
            var klineVolH = canvasH - klinePriceH - ss;
            var MMy = [66, 82];
            var MMvolY = [1354875, 25835082];
            var yarr = [];
            yarr[0] = 0;
            yarr[1] = klinePriceH/2+0.5;
            yarr[2] = klinePriceH+0.5;
            yarr[3] = klinePriceH+ss+0.5;
            yarr[4] = canvasH-klineVolH/2+0.5;
            yarr[5] = canvasH-0.5;
            var spotLineArr = [];


            var str = '';
            var x = scale()
                        .domain([0, d.length-1])
                        .range([chartStartX+6, canvasW-klinewidth]);
            var y = scale()
                        .domain(MMy)
                        .range([klinePriceH-10, 0]);
            var vy = scale()
                        .domain(MMvolY)
                        .range([canvasH, klinePriceH+ss+3]);
            var tempd, left, top, w, h, tt, c, lineX, lineYTop, lineYBottom;
            var voly, volh;
            var ma5str = '', ma10str = '', ma30str = '';

            var svgStr = '<svg style="position: absolute;" width="100%" height="100%" version="1.1"xmlns="http://www.w3.org/2000/svg">';
            var svgStr2 = '<svg width="100%" height="100%" version="1.1"xmlns="http://www.w3.org/2000/svg">';

            for (var i = 0; i < d.length; i++) {
                tempd = d[i];

                left = x(i);
                tt = tempd.o - tempd.c;
                top =  tt > 0? y(tempd.o) : y(tempd.c);
                w = klinewidth;
                h = Math.abs(y(tempd.o)- y(tempd.c));
                c = tt > 0 ? 'down': (tt<0?'up':'eq'); 
                lineX = left+w/2;
                lineYTop = y(tempd.a);
                lineYBottom = y(tempd.i);
                voly = vy(tempd.n);
                volh = klineVolH;
                if (i === 4 || i===13 || i===24 || i === 20) {
                    spotLineArr.push(lineX);
                }

                ma5str += x(i)+w/2+','+y(tempd['ma5'])+' ';
                ma10str += x(i)+w/2+','+y(tempd['ma10'])+' ';
                ma30str += x(i)+w/2+','+y(tempd['ma30'])+' ';

                str += '<div class="hqKl'+c+'BorderColorbd" style="position:absolute;width:1px;height:'+ (lineYBottom - lineYTop ) +'px;top:'+lineYTop+'px;left:'+lineX+'px;"></div><div class="kline-bar hqKl'+c+'FillColor hqKl'+c+'BorderColorbd" style="top:'+top+'px;height:'+h+'px;width:'+w+'px;position:absolute;left:'+left+'px;"></div> <div class="kline-bar hqKl'+c+'FillColor hqKl'+c+'BorderColorbd" style="top:'+voly+'px;height:'+volh+'px;width:'+w+'px;position:absolute;left:'+left+'px;"></div>';
            }
            var spotLine1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[0]+0.5)+'" y1="0.5" x2="'+(spotLineArr[0]+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqKldashedLineColorsvg spotCC"/>';
            var spotLine2 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[1]+0.5)+'" y1="0.5" x2="'+(spotLineArr[1]+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqKldashedLineColorsvg spotCC"/>';
            var spotLine3 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[3]+0.5)+'" y1="0.5" x2="'+(spotLineArr[3]+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqKldashedLineColorsvg spotCC"/>';
            var crossStr = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[2]+0.5)+'" y1="0.5" x2="'+(spotLineArr[2]+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqKlcrossColorsvg spotCC"/><line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="100" x2="'+(canvasW+0.5)+'" y2="100"  class="hqKlcrossColorsvg spotCC"/>';
            var verticalDraw1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="0.5" x2="'+(chartStartX+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqKlgridColorsvg horizontalCC"/>';
            var horizontalDraw1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[1]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[1]+'"  class="hqKlgridColorsvg horizontalCC"/>';
            var horizontalDraw2 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[2]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[2]+'"  class="hqKlgridColorsvg horizontalCC"/>';
            var horizontalDraw3 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[3]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[3]+'"  class="hqKlgridColorsvg horizontalCC"/>';
            var horizontalDraw4 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[4]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[4]+'"  class="hqKlgridColorsvg horizontalCC"/>';
            var ma5Domstr = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+ma5str+'"  class="hqKlma5svg maCC"/>';
            var ma10Domstr = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+ma10str+'"  class="hqKlma10svg maCC"/>';
            var ma30Domstr = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+ma30str+'"  class="hqKlma30svg maCC"/>';
            var yAxisstr = '<div class="hx3-axis-klineprice hqKlaxisColorcc"><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[2]-24)+'px;left:13.5px;"><span>64.50</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[1]-12)+'px;left:13.5px;"><span>74.29</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[0])+'px;left:13.5px;"><span>84.08</span></div></div>    <div class="hx3-axis-x-hhr-bottom" style=""><div style="position:absolute;text-align:center;width:80px;top:271.5px;left:'+(x(4)-30)+'px">2016-11-02</div><div class="hx3-axis-x-hhr-bottom" style=""><div style="position:absolute;text-align:center;width:80px;top:271.5px;left:'+(x(13)-30)+'px">2016-12-02</div><div class="hx3-axis-x-hhr-bottom" style=""><div style="position:absolute;text-align:center;width:80px;top:271.5px;left:'+(x(24)-30)+'px">2017-01-17</div></div></div>            <div class="hxc3-klineprice-max hqKlmaxminColorcc" style="position: absolute; text-decoration: underline; margin-top: -20px; height: 20px; left: '+(x(5))+'px;  top: 19.5px; margin-left: -33px;">82.06</div>       <div class="hxc3-klineprice-min hqKlmaxminColorcc" style="position: absolute; text-decoration: underline; margin-top: -20px; height: 20px; left: '+(x(23))+'px;  top: 262.5px; margin-left: -33px;">62.06</div>';
            var volYAxisstr = '<div class="hx3-axis-klinevol hqKlaxisColorcc"><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[5]-24)+'px;left:13.5px;"><span>13233</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[4]-12)+'px;left:13.5px;"><span>34242</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[3])+'px;left:13.5px;"><span>65462</span></div></div></div>';

            
            svgStr2 = svgStr2  + spotLine1 + spotLine2 + spotLine3 +'</svg>';
            svgStr = svgStr + crossStr + verticalDraw1 +horizontalDraw1 + horizontalDraw2 + horizontalDraw3 + horizontalDraw4 + ma5Domstr + ma10Domstr + ma30Domstr +'</svg>';
            $(this.klineBoxDom).append(str);
            $(this.klineBoxDom).append(svgStr);
            $(this.klineBoxDom).append(svgStr2);
            $(this.klineBoxDom).append(yAxisstr);
            $(this.klineBoxDom).append(volYAxisstr);
            $(this.klineBoxDom).find('.mousepng').css('left', (x(20)+14)+'px');

        }

        function initFsColorBox (){

            var d = main.testfsdata;

            var canvasW = $('#fsBox').width() || 876;
            var canvasH = $('#fsBox').height() || 400;
            var chartStartX = 60;
            var fsPriceH = 270;
            var ss = 22;
            var fsVolH = canvasH - fsPriceH - ss;
            var MMy = [10.94, 11.12];
            var MMvolY = [0, 702800];
            var preprice = 11;

            var yarr = [];
            yarr[0] = 0;
            yarr[1] = fsPriceH/2+0.5;
            yarr[2] = fsPriceH+0.5;
            yarr[3] = canvasH-fsVolH/2-ss+0.5;
            yarr[4] = canvasH-ss+0.5;
            yarr[5] = canvasH-0.5;
            var spotLineArr = [];

            var str = '';
            var x = scale()
                        .domain([0, d.length-1])
                        .range([chartStartX, canvasW-1]);
            var y = scale()
                        .domain(MMy)
                        .range([fsPriceH-10, 0]);
            var vy = scale()
                        .domain(MMvolY)
                        .range([canvasH-ss, fsPriceH]);

            var left, nowpriceStr = '', avpriceStr = '', nowpriceStr2 = '', 
                volline = '';
            var svgStr = '<svg style="position: absolute;" width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg">';

            var bbottom = canvasH - ss - 0.5;
            for (var i = 0; i < d.length; i++) {
                tempd = d[i];
                preprice = d[i-1]? d[i-1].nowp:preprice;
                left = (x(i)>>0)+0.5;

                nowpriceStr += left +','+y(tempd['nowp'])+' ';
                avpriceStr += left +','+y(tempd['av'])+' ';
                nowpriceStr2 = nowpriceStr;

                if (tempd['nowp'] > preprice) {
                    volline += '<line x1="'+left+'" y1="'+((vy(tempd['n'])>>0)+0.5)+'" x2="'+left+'" y2="'+bbottom+'" class="hqFsupColorsvg"/>';
                }else if(tempd['nowp'] < preprice){
                    volline += '<line x1="'+left+'" y1="'+((vy(tempd['n'])>>0)+0.5)+'" x2="'+left+'" y2="'+bbottom+'" class="hqFsdownColorsvg"/>';
                }else{
                    volline += '<line x1="'+left+'" y1="'+((vy(tempd['n'])>>0)+0.5)+'" x2="'+left+'" y2="'+bbottom+'" class="hqFseqColorsvg"/>';
                }

                if (i === 60 || i===120 || i===170 || i === 180) {
                    spotLineArr.push(left);
                }

            }

            nowpriceStr = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+nowpriceStr+'"  class="hqFsnowpsvg maCC"/>';
            avpriceStr = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+avpriceStr+'"  class="hqFsavpsvg maCC"/>';
            var nowpriceStr2 = '<polyline xmlns="http://www.w3.org/2000/svg" points="'+nowpriceStr2+' '+ canvasW+','+fsPriceH+' '+chartStartX+','+fsPriceH+'"  class="hqFsnowpClosesvgfill maCCstrock"/>';
            
            var spotLine1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[0])+'" y1="0.5" x2="'+(spotLineArr[0])+'" y2="'+bbottom+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var spotLine2 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[1])+'" y1="0.5" x2="'+(spotLineArr[1])+'" y2="'+bbottom+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var spotLine3 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[3])+'" y1="0.5" x2="'+(spotLineArr[3])+'" y2="'+bbottom+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var crossStr = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(spotLineArr[2])+'" y1="0.5" x2="'+(spotLineArr[2])+'" y2="'+canvasH+'"  class="hqFscrossColorsvg spotCC"/><line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="100" x2="'+(canvasW+0.5)+'" y2="100"  class="hqFscrossColorsvg spotCC"/>';



            var verticalDraw1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="0.5" x2="'+(chartStartX+0.5)+'" y2="'+(canvasH-0.5)+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var horizontalDraw1 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[1]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[1]+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var horizontalDraw2 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[2]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[2]+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var horizontalDraw3 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[3]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[3]+'"  class="hqFsgridColorsvg horizontalCC"/>';
            var horizontalDraw4 = '<line xmlns="http://www.w3.org/2000/svg" x1="'+(chartStartX+0.5)+'" y1="'+yarr[4]+'" x2="'+(canvasW+0.5)+'" y2="'+yarr[4]+'"  class="hqFsgridColorsvg horizontalCC"/>';

            var yAxisstr = '<div class="hx3-axis-klineprice"><div class="hx3-axis-y-hhr-left hqFsdownAxisColorcc" style=""><div style="position:absolute;text-align:left;top:'+(yarr[2]-24)+'px;left:13.5px;"><span>64.50</span></div></div><div class="hx3-axis-y-hhr-left hqFseqAxisColorcc" style=""><div style="position:absolute;text-align:left;top:'+(yarr[1]-12)+'px;left:13.5px;"><span>74.29</span></div></div><div class="hx3-axis-y-hhr-left hqFsupAxisColorcc"><div style="position:absolute;text-align:left;top:'+(yarr[0])+'px;left:13.5px;"><span>84.08</span></div></div>    <div class="hx3-axis-x-hhr-bottom hqFsxAxisColorcc" style=""><div style="position:absolute;text-align:center;width:80px;top:380.5px;left:'+(x(0)-15)+'px">09:30</div><div class="hx3-axis-x-hhr-bottom hqFsxAxisColorcc" style=""><div style="position:absolute;text-align:center;width:80px;top:380.5px;left:'+(x(120)-30)+'px">11:30/13:00</div><div class="hx3-axis-x-hhr-bottom hqFsxAxisColorcc" style=""><div style="position:absolute;text-align:center;width:80px;top:380.5px;left:'+(x(241)-60)+'px">15:00</div></div></div>';


            var volYAxisstr = '<div class="hx3-axis-klinevol hqFseqAxisColorcc"><div class="hx3-axis-y-hhr-left" class=""><div style="position:absolute;text-align:left;top:'+(yarr[4]-24)+'px;left:13.5px;"><span>13233</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[3]-12)+'px;left:13.5px;"><span>34242</span></div></div><div class="hx3-axis-y-hhr-left" style=""><div style="position:absolute;text-align:left;top:'+(yarr[2])+'px;left:13.5px;"><span>65462</span></div></div></div>';


            svgStr = svgStr + nowpriceStr + avpriceStr + nowpriceStr2 + volline + verticalDraw1 + horizontalDraw1 + horizontalDraw2 + horizontalDraw3 + horizontalDraw4 + spotLine1 + spotLine2 + spotLine3 + crossStr +'</svg>';

            $(main.fsBoxDom).append(svgStr);
            $(main.fsBoxDom).append(volYAxisstr);
            $(main.fsBoxDom).append(yAxisstr);
            $(main.fsBoxDom).find('.mousepng').css('left', (x(170)+8)+'px');


        }

        var outputKlineColor = function(){

            $(main.outputKlineDom).on('click', function(event) {
                event.preventDefault();
                var idstr = $(this).attr('id');
                var type = '';
                idstr.replace(/^output([^C]+)/, function(s0, s1){
                    type = s1;
                });
                var $ele = $('.configsetting'+type);
                var cc = main.totalCongfigs[type];
                var tempcc;
                
                function arrangeColorSettings (o, name, obj){
                    if(typeof o == "string"){ 
                        var idname = 'idhq'+type+name;
                        var $idname = $ele.find('input[id^="'+idname+'"]');
                        if ($idname.length>0) {
                            tempcc = $idname.val();
                            obj[name] = tempcc;
                        }
                    } 
                    if(typeof o=="object"){ 
                        for(var i in o){
                            arrangeColorSettings(o[i], i, o); 
                        }
                    } 
                }

                arrangeColorSettings(cc);
                $('.result-'+type).html(obj2string(cc));
            });
        }

        var importKlineColor = function(){
            $(main.importKlineDom).on('click', function(event) {
                event.preventDefault();

                var idstr = $(this).attr('id');
                var type = '';
                idstr.replace(/^import([^C]+)/, function(s0, s1){
                    type = s1;
                });

                $('.import'+type+'ColorTextarea').css('display', 'block');
            });

            $(main.importColorTextarea).on('click', '.cancle', function(event) {
                event.preventDefault();
                $(this).parent().parent().css('display', 'none');
            });

            $(main.importColorTextarea).on('click', '.sayyes', function(event) {
                event.preventDefault();
                var ccstr = $(this).parent().siblings('textarea').val();
                var cc = eval('(' + ccstr + ')');

                var type = $(this).parent().parent().data('chart');
                var $ele = $('.configsetting'+type);

                function arrangeColorSettings (o, name, obj){
                    if(typeof o == "string"){ 
                        var idname = 'idhq'+type+name;
                        var iddom = $ele.find('input[id^="'+idname+'"]');
                        if (iddom.length>0) {
                            iddom.val(o);
                            iddom.colorPicker(main.colorPickerConfigs);

                            var b = iddom.data('influence');

                            if(/cc$/.test(b)){
                                $('.'+b).css('color', o);
                            }else if(/svg$/.test(b)){
                                $('.'+b).css('stroke', o);
                            }else if(/svgfill$/.test(b)){
                                $('.'+b).css('fill', o);
                            }else if(/bd$/.test(b)){
                                $('.'+b).css('border', '1px solid '+ o);
                            }else{
                                $('.'+b).css('background', o);
                            }
                        }
                    } 
                    if(typeof o=="object"){ 
                        for(var i in o){
                            arrangeColorSettings(o[i], i, o); 
                        }
                    } 
                }
                arrangeColorSettings(cc);

                $('.import'+type+'ColorTextarea').css('display', 'none');
            });
        }

        var colorPickerInit = function(){
            $('.jscolor').colorPicker(main.colorPickerConfigs);
        }


        var main = {
            klineBoxDom:'#klineBox',
            fsBoxDom:'#fsBox',
            importColorTextarea:'.importKlColorTextarea, .importFsColorTextarea',
            outputKlineDom:'#outputKlColor, #outputFsColor',
            importKlineDom:'#importKlColor, #importFsColor',
            outputKlineColor: outputKlineColor,
            importKlineColor: importKlineColor,
            colorPickerConfigs:{
                buildCallback: function($elm) {
                    this.$colorAlpha = $elm.find('.cp-alpha');
                    // this.$colorPatch = $elm.prepend('<div class="cp-disp">').find('.cp-disp');
                },
                cssAddon:
                        '.cp-disp {padding:10px; margin-bottom:6px; font-size:19px; height:20px; line-height:20px}' +
                        '.cp-xy-slider {width:200px; height:200px;}' +
                        '.cp-xy-cursor {width:16px; height:16px; border-width:2px; margin:-8px}' +
                        '.cp-z-slider {height:200px; width:40px;}' +
                        '.cp-z-cursor {border-width:8px; margin-top:-8px;}' +
                        '.cp-alpha {height:40px;}' +
                        '.cp-alpha-cursor {border-width:8px; margin-left:-8px;}',
                renderCallback: function($elm, toggled) {
                    var colors = this.color.colors;
                    if($elm.hasClass('jqshow-alpha')){
                        this.$colorAlpha.css({'display':'block'});
                    }else{
                        this.$colorAlpha.css({'display':'none'});
                    }
                    var c = this.color.toString($elm._colorMode);
                    var a = $elm.attr('data-influence');
                    if(/cc$/.test(a)){
                        $('.'+a).css('color', c);
                    }else if(/svg$/.test(a)){
                        $('.'+a).css('stroke', c);
                    }else if(/svgfill$/.test(a)){
                        $('.'+a).css('fill', c);
                    }else if(/bd$/.test(a)){
                        $('.'+a).css('border', '1px solid '+c);
                    }else{
                        $('.'+a).css('background', c);
                    }
                    // this.$colorPatch.css({
                    //     backgroundColor: '#' + colors.HEX,
                    //     color: colors.RGBLuminance > 0.22 ? '#222' : '#ddd'
                    // }).text(this.color.toString($elm._colorMode)); // $elm.val();
                }
            },
            colorPickerInit:colorPickerInit,
            initKlineColorBox: initKlineColorBox,
            initFsColorBox: initFsColorBox,
            totalCongfigs:{
                'Fs':{
                    fsprice:{
                        nowp:'',
                        avp:'',
                        nowpClose:''
                    },
                    crossColor: '',
                    fsvol:{
                        upColor:'', 
                        downColor:'', 
                        eqColor:''
                    },
                    gridColor: '',

                    // css 可控制 @todo
                    axisColor:{
                        upAxisColor:'',
                        downAxisColor:'',
                        eqAxisColor:'',
                    },
                    hqoutlineColor: '',
                    hqbgColor: ''
                },
                'Kl':{
                    // 以下是canvas中的颜色配置
                    klineColor:{
                        // k线填充色
                        upFillColor: '',
                        eqFillColor: '',
                        downFillColor: '',
                        // k线 边框线色
                        upBorderColor: '',
                        eqBorderColor: '',
                        downBorderColor:''
                    },
                    maColor:{
                        ma5 : '',
                        ma10: '',
                        ma30: ''
                    },
                    gridColor: '',
                    dashedLineColor:'',
                    crossColor:'',
                    // 以下是 可通过css 配置的颜色
                    hqoutlineColor: '',
                    hqbgColor: '',
                    axisColor: '',
                    maxminColor: ''
                }
            },
            testklinedata: [{t:"20161123",n:"6485995",i:"72.2",o:"73.09",a:"73.98",c:"72.59",s:"be",ma5:"71.928",ma10:"72.35",ma30:"70.2573"},{t:"20161124",n:"4329638",i:"71.6",o:"72.14999999999999",a:"72.94999999999999",c:"71.85",s:"be",ma5:"72.01",ma10:"72.374",ma30:"70.4547"},{t:"20161125",n:"3734351",i:"70.35",o:"71.35",a:"72.14999999999999",c:"71.97999999999999",s:"ab",ma5:"72.166",ma10:"72.275",ma30:"70.6843"},{t:"20161128",n:"10874380",i:"72.5",o:"74.05",a:"75.58",c:"72.98",s:"be",ma5:"72.52",ma10:"72.268",ma30:"70.94"},{t:"20161129",n:"7447597",i:"71.99",o:"72.08",a:"74.5",c:"73.69999999999999",s:"ab",ma5:"72.62",ma10:"72.32",ma30:"71.1473"},{t:"20161130",n:"25835082",i:"72.98",o:"73.4",a:"81.06",c:"78.67",s:"ab",ma5:"73.836",ma10:"72.882",ma30:"71.522"},{t:"20161201",n:"12840961",i:"76.8",o:"77.23",a:"78.67999999999999",c:"78.67",s:"ab",ma5:"75.2",ma10:"73.605",ma30:"71.909"},{t:"20161202",n:"11980196",i:"77.49",o:"78.14999999999999",a:"80.94999999999999",c:"78",s:"be",ma5:"76.404",ma10:"74.285",ma30:"72.2873"},{t:"20161205",n:"8725017",i:"76.25",o:"76.88",a:"80.87",c:"78.29",s:"ab",ma5:"77.466",ma10:"74.993",ma30:"72.5973"},{t:"20161206",n:"8380084",i:"75",o:"78.29",a:"78.87",c:"75.18",s:"be",ma5:"77.762",ma10:"75.191",ma30:"72.821"},{t:"20161207",n:"6738418",i:"73.7",o:"75.31",a:"75.8",c:"74.98",s:"be",ma5:"77.024",ma10:"75.43",ma30:"73.034"},{t:"20161208",n:"4831985",i:"73.8",o:"74.61",a:"75.28999999999999",c:"74.02",s:"be",ma5:"76.094",ma10:"75.647",ma30:"73.172"},{t:"20161209",n:"6330411",i:"73.5",o:"73.98",a:"74.89",c:"73.58",s:"be",ma5:"75.21",ma10:"75.807",ma30:"73.227"},{t:"20161212",n:"11926301",i:"68.1",o:"73.11",a:"73.35",c:"68.35",s:"be",ma5:"73.222",ma10:"75.344",ma30:"73.1273"},{t:"20161213",n:"5694407",i:"66.49",o:"68.24",a:"69.64999999999999",c:"68.8",s:"ab",ma5:"71.946",ma10:"74.854",ma30:"72.9777"},{t:"20161214",n:"3727004",i:"68.1",o:"68.6",a:"69.94999999999999",c:"68.80999999999999",s:"ab",ma5:"70.712",ma10:"73.868",ma30:"72.847"},{t:"20161215",n:"4457573",i:"68.18",o:"68.57000000000001",a:"70.09",c:"69.47000000000001",s:"ab",ma5:"69.802",ma10:"72.948",ma30:"72.7263"},{t:"20161216",n:"6241148",i:"69.04",o:"69.32000000000001",a:"73.19000000000001",c:"71.46000000000001",s:"ab",ma5:"69.378",ma10:"72.294",ma30:"72.745"},{t:"20161219",n:"3818624",i:"69.31",o:"70.88",a:"70.9",c:"69.94",s:"be",ma5:"69.696",ma10:"71.459",ma30:"72.743"},{t:"20161220",n:"3475555",i:"68.91",o:"69.72999999999999",a:"70.95",c:"69.39",s:"be",ma5:"69.814",ma10:"70.88",ma30:"72.704"},{t:"20161221",n:"2912580",i:"69.3",o:"70.09",a:"70.88",c:"69.81",s:"be",ma5:"70.014",ma10:"70.363",ma30:"72.7143"},{t:"20161222",n:"2457449",i:"68.66",o:"69.72999999999999",a:"69.97999999999999",c:"69.3",s:"be",ma5:"69.98",ma10:"69.891",ma30:"72.6373"},{t:"20161223",n:"4662413",i:"66.66",o:"69.17999999999999",a:"69.47999999999999",c:"67",s:"be",ma5:"69.088",ma10:"69.233",ma30:"72.4383"},{t:"20161226",n:"3845151",i:"66.01",o:"67.04",a:"68.55000000000001",c:"68.46000000000001",s:"ab",ma5:"68.792",ma10:"69.244",ma30:"72.2853"},{t:"20161227",n:"2520366",i:"67.78",o:"68.4",a:"69.23",c:"68.5",s:"ab",ma5:"68.614",ma10:"69.214",ma30:"72.1293"},{t:"20161228",n:"2421373",i:"68.07",o:"68.1",a:"68.78999999999999",c:"68.52999999999999",s:"ab",ma5:"68.358",ma10:"69.186",ma30:"71.9787"},{t:"20161229",n:"4874738",i:"68.35",o:"69.08999999999999",a:"70.36999999999999",c:"69.74",s:"ab",ma5:"68.446",ma10:"69.213",ma30:"71.922"},{t:"20161230",n:"3094281",i:"68.5",o:"69.73",a:"70.33",c:"68.78",s:"be",ma5:"68.802",ma10:"68.945",ma30:"71.8413"},{s:"ab",t:"20170104",o:"70.02",a:"73.1",i:"70",c:"72.55",n:"7206841",np:"518713980",h:"2.734",ma5:"69.62",ma10:"69.206",ma30:"71.886"}],

            testfsdata:[{t:"0930",nowp:11.03, av:11.021,n:299300},{t:"0931",nowp:11.01, av:11.021,n:72980},{t:"0932",nowp:10.97, av:11.009,n:262120},{t:"0933",nowp:10.99, av:11.005,n:89500},{t:"0934",nowp:11.01, av:11.004,n:114760},{t:"0935",nowp:11.01, av:11.004,n:94620},{t:"0936",nowp:10.98, av:11.002,n:161220},{t:"0937",nowp:11, av:11.001,n:65080},{t:"0938",nowp:11, av:11,n:140300},{t:"0939",nowp:11, av:11,n:65800},{t:"0940",nowp:10.98, av:10.999,n:163200},{t:"0941",nowp:10.99, av:10.999,n:104700},{t:"0942",nowp:10.99, av:10.998,n:117700},{t:"0943",nowp:10.98, av:10.998,n:103200},{t:"0944",nowp:10.99, av:10.997,n:46100},{t:"0945",nowp:11, av:10.998,n:156132},{t:"0946",nowp:11.01, av:10.998,n:105100},{t:"0947",nowp:11.02, av:10.998,n:90000},{t:"0948",nowp:11.02, av:10.999,n:84280},{t:"0949",nowp:11.03, av:11.001,n:99000},{t:"0950",nowp:11.04, av:11.001,n:78636},{t:"0951",nowp:11.03, av:11.002,n:57600},{t:"0952",nowp:11.01, av:11.004,n:270088},{t:"0953",nowp:11.02, av:11.004,n:34400},{t:"0954",nowp:11.02, av:11.004,n:46800},{t:"0955",nowp:11.02, av:11.004,n:35100},{t:"0956",nowp:11.02, av:11.006,n:325605},{t:"0957",nowp:11.01, av:11.006,n:31495},{t:"0958",nowp:11.02, av:11.006,n:65200},{t:"0959",nowp:11.02, av:11.006,n:55500},{t:"1000",nowp:11.02, av:11.006,n:50900},{t:"1001",nowp:11.02, av:11.006,n:34500},{t:"1002",nowp:11.02, av:11.007,n:66400},{t:"1003",nowp:11.01, av:11.007,n:61900},{t:"1004",nowp:11, av:11.007,n:105402},{t:"1005",nowp:11.02, av:11.007,n:34300},{t:"1006",nowp:11.01, av:11.007,n:70400},{t:"1007",nowp:11.01, av:11.007,n:42600},{t:"1008",nowp:11.01, av:11.007,n:104100},{t:"1009",nowp:10.99, av:11.007,n:50200},{t:"1010",nowp:11.01, av:11.007,n:47000},{t:"1011",nowp:10.98, av:11.006,n:170600},{t:"1012",nowp:11.01, av:11.003,n:540200},{t:"1013",nowp:11, av:11.003,n:110200},{t:"1014",nowp:11.01, av:11.003,n:38260},{t:"1015",nowp:11, av:11.003,n:24100},{t:"1016",nowp:11, av:11.003,n:56240},{t:"1017",nowp:11, av:11.003,n:48500},{t:"1018",nowp:11, av:11.003,n:65640},{t:"1019",nowp:11, av:11.003,n:34700},{t:"1020",nowp:10.99, av:11.003,n:66900},{t:"1021",nowp:10.99, av:11.003,n:23300},{t:"1022",nowp:10.99, av:11.003,n:33800},{t:"1023",nowp:10.99, av:11.003,n:26200},{t:"1024",nowp:10.98, av:11.003,n:37200},{t:"1025",nowp:10.99, av:11.003,n:34360},{t:"1026",nowp:10.99, av:11.003,n:35440},{t:"1027",nowp:10.95, av:10.998,n:702800},{t:"1028",nowp:10.95, av:10.998,n:60400},{t:"1029",nowp:10.94, av:10.994,n:356880},{t:"1030",nowp:10.95, av:10.992,n:258500},{t:"1031",nowp:10.95, av:10.992,n:49900},{t:"1032",nowp:10.95, av:10.991,n:47100},{t:"1033",nowp:10.94, av:10.991,n:118140},{t:"1034",nowp:10.94, av:10.99,n:45900},{t:"1035",nowp:10.95, av:10.99,n:82200},{t:"1036",nowp:10.95, av:10.99,n:24400},{t:"1037",nowp:10.95, av:10.989,n:59800},{t:"1038",nowp:10.95, av:10.989,n:47000},{t:"1039",nowp:10.95, av:10.989,n:27200},{t:"1040",nowp:10.95, av:10.989,n:35400},{t:"1041",nowp:10.95, av:10.988,n:65500},{t:"1042",nowp:10.96, av:10.988,n:164300},{t:"1043",nowp:10.96, av:10.987,n:93300},{t:"1044",nowp:10.96, av:10.986,n:272500},{t:"1045",nowp:10.98, av:10.986,n:77900},{t:"1046",nowp:10.98, av:10.986,n:92600},{t:"1047",nowp:10.97, av:10.986,n:154900},{t:"1048",nowp:10.97, av:10.986,n:15500},{t:"1049",nowp:10.96, av:10.986,n:84765},{t:"1050",nowp:10.97, av:10.985,n:32300},{t:"1051",nowp:10.96, av:10.985,n:25300},{t:"1052",nowp:10.97, av:10.985,n:15080},{t:"1053",nowp:10.96, av:10.985,n:88200},{t:"1054",nowp:10.97, av:10.985,n:12900},{t:"1055",nowp:10.96, av:10.985,n:37800},{t:"1056",nowp:10.97, av:10.985,n:19380},{t:"1057",nowp:10.97, av:10.985,n:26900},{t:"1058",nowp:10.97, av:10.985,n:18000},{t:"1059",nowp:10.97, av:10.985,n:34200},{t:"1100",nowp:10.97, av:10.985,n:50400},{t:"1101",nowp:10.97, av:10.985,n:25000},{t:"1102",nowp:10.98, av:10.985,n:42700},{t:"1103",nowp:10.98, av:10.985,n:177460},{t:"1104",nowp:10.99, av:10.985,n:254160},{t:"1105",nowp:10.99, av:10.985,n:62600},{t:"1106",nowp:10.99, av:10.985,n:99800},{t:"1107",nowp:10.99, av:10.985,n:40500},{t:"1108",nowp:10.99, av:10.985,n:34400},{t:"1109",nowp:11, av:10.985,n:28359},{t:"1110",nowp:11, av:10.985,n:116300},{t:"1111",nowp:11.01, av:10.985,n:103600},{t:"1112",nowp:11.03, av:10.986,n:160000},{t:"1113",nowp:11.03, av:10.986,n:128800},{t:"1114",nowp:11.03, av:10.986,n:23400},{t:"1115",nowp:11.01, av:10.987,n:143800},{t:"1116",nowp:11.01, av:10.987,n:33800},{t:"1117",nowp:11.03, av:10.987,n:32300},{t:"1118",nowp:11.02, av:10.987,n:33800},{t:"1119",nowp:11.03, av:10.987,n:26800},{t:"1120",nowp:11.03, av:10.987,n:57525},{t:"1121",nowp:11.04, av:10.988,n:39100},{t:"1122",nowp:11.03, av:10.988,n:52900},{t:"1123",nowp:11.04, av:10.988,n:78400},{t:"1124",nowp:11.03, av:10.988,n:21220},{t:"1125",nowp:11.06, av:10.989,n:162200},{t:"1126",nowp:11.07, av:10.99,n:59700},{t:"1127",nowp:11.07, av:10.99,n:36100},{t:"1128",nowp:11.07, av:10.99,n:29300},{t:"1129",nowp:11.08, av:10.991,n:79000},{t:"1130",nowp:11.08, av:10.991,n:0},{t:"1300",nowp:11.09, av:10.992,n:163400},{t:"1301",nowp:11.1, av:10.994,n:170179},{t:"1302",nowp:11.1, av:10.994,n:57221},{t:"1303",nowp:11.09, av:10.995,n:142400},{t:"1304",nowp:11.1, av:10.996,n:90800},{t:"1305",nowp:11.1, av:10.997,n:114979},{t:"1306",nowp:11.1, av:10.998,n:84900},{t:"1307",nowp:11.1, av:10.998,n:32000},{t:"1308",nowp:11.1, av:10.999,n:39600},{t:"1309",nowp:11.1, av:10.999,n:32300},{t:"1310",nowp:11.09, av:10.999,n:33900},{t:"1311",nowp:11.11, av:11.001,n:229580},{t:"1312",nowp:11.11, av:11.001,n:28200},{t:"1313",nowp:11.1, av:11.002,n:43000},{t:"1314",nowp:11.1, av:11.002,n:37385},{t:"1315",nowp:11.1, av:11.002,n:21000},{t:"1316",nowp:11.09, av:11.002,n:36700},{t:"1317",nowp:11.11, av:11.003,n:17500},{t:"1318",nowp:11.12, av:11.004,n:101200},{t:"1319",nowp:11.11, av:11.004,n:73200},{t:"1320",nowp:11.11, av:11.004,n:34300},{t:"1321",nowp:11.11, av:11.005,n:27500},{t:"1322",nowp:11.11, av:11.006,n:119000},{t:"1323",nowp:11.09, av:11.007,n:201700},{t:"1324",nowp:11.08, av:11.007,n:55500},{t:"1325",nowp:11.08, av:11.008,n:28900},{t:"1326",nowp:11.08, av:11.008,n:47900},{t:"1327",nowp:11.09, av:11.008,n:25800},{t:"1328",nowp:11.09, av:11.008,n:19600},{t:"1329",nowp:11.09, av:11.008,n:33900},{t:"1330",nowp:11.09, av:11.008,n:19300},{t:"1331",nowp:11.1, av:11.009,n:41600},{t:"1332",nowp:11.09, av:11.009,n:48600},{t:"1333",nowp:11.1, av:11.009,n:11200},{t:"1334",nowp:11.1, av:11.009,n:13100},{t:"1335",nowp:11.09, av:11.009,n:53400},{t:"1336",nowp:11.07, av:11.01,n:60800},{t:"1337",nowp:11.07, av:11.01,n:104100},{t:"1338",nowp:11.08, av:11.011,n:88600},{t:"1339",nowp:11.07, av:11.011,n:46300},{t:"1340",nowp:11.06, av:11.011,n:95700},{t:"1341",nowp:11.06, av:11.012,n:117300},{t:"1342",nowp:11.06, av:11.012,n:116400},{t:"1343",nowp:11.06, av:11.012,n:54200},{t:"1344",nowp:11.07, av:11.013,n:30000},{t:"1345",nowp:11.06, av:11.013,n:160400},{t:"1346",nowp:11.06, av:11.013,n:21000},{t:"1347",nowp:11.05, av:11.013,n:106400},{t:"1348",nowp:11.05, av:11.013,n:67700},{t:"1349",nowp:11.05, av:11.014,n:64100},{t:"1350",nowp:11.06, av:11.014,n:78800},{t:"1351",nowp:11.07, av:11.014,n:27200},{t:"1352",nowp:11.08, av:11.014,n:11900},{t:"1353",nowp:11.08, av:11.014,n:13900},{t:"1354",nowp:11.09, av:11.015,n:332180},{t:"1355",nowp:11.07, av:11.016,n:35800},{t:"1356",nowp:11.08, av:11.016,n:23500},{t:"1357",nowp:11.07, av:11.016,n:65900},{t:"1358",nowp:11.04, av:11.016,n:134400},{t:"1359",nowp:11.02, av:11.016,n:94900},{t:"1400",nowp:11.02, av:11.017,n:152300},{t:"1401",nowp:11.02, av:11.017,n:180200},{t:"1402",nowp:11.04, av:11.017,n:242817},{t:"1403",nowp:11.04, av:11.017,n:49200},{t:"1404",nowp:11.04, av:11.017,n:81600},{t:"1405",nowp:11.04, av:11.017,n:102900},{t:"1406",nowp:11.05, av:11.017,n:48600},{t:"1407",nowp:11.04, av:11.017,n:42400},{t:"1408",nowp:11.05, av:11.017,n:30100},{t:"1409",nowp:11.04, av:11.017,n:55000},{t:"1410",nowp:11.05, av:11.017,n:30500},{t:"1411",nowp:11.04, av:11.018,n:49500},{t:"1412",nowp:11.04, av:11.018,n:31000},{t:"1413",nowp:11.04, av:11.018,n:41080},{t:"1414",nowp:11.05, av:11.018,n:129720},{t:"1415",nowp:11.04, av:11.018,n:65020},{t:"1416",nowp:11.05, av:11.018,n:116200},{t:"1417",nowp:11.04, av:11.018,n:464607},{t:"1418",nowp:11.04, av:11.018,n:28100},{t:"1419",nowp:11.04, av:11.018,n:21900},{t:"1420",nowp:11.02, av:11.018,n:35090},{t:"1421",nowp:11.04, av:11.018,n:24403},{t:"1422",nowp:11.04, av:11.018,n:31500},{t:"1423",nowp:11.02, av:11.018,n:53098},{t:"1424",nowp:11, av:11.018,n:181900},{t:"1425",nowp:11, av:11.018,n:85500},{t:"1426",nowp:11.01, av:11.018,n:65900},{t:"1427",nowp:11.02, av:11.018,n:96063},{t:"1428",nowp:11.03, av:11.018,n:31900},{t:"1429",nowp:11.04, av:11.018,n:69500},{t:"1430",nowp:11.03, av:11.018,n:48700},{t:"1431",nowp:11.03, av:11.018,n:34322},{t:"1432",nowp:11.02, av:11.018,n:62378},{t:"1433",nowp:11.03, av:11.018,n:26422},{t:"1434",nowp:11.03, av:11.018,n:32900},{t:"1435",nowp:11.03, av:11.018,n:31800},{t:"1436",nowp:11.03, av:11.018,n:30800},{t:"1437",nowp:11.04, av:11.018,n:22802},{t:"1438",nowp:11.03, av:11.018,n:41800},{t:"1439",nowp:11.06, av:11.019,n:373200},{t:"1440",nowp:11.06, av:11.019,n:143300},{t:"1441",nowp:11.05, av:11.019,n:47800},{t:"1442",nowp:11.03, av:11.02,n:109239},{t:"1443",nowp:11.04, av:11.02,n:27000},{t:"1444",nowp:11.04, av:11.02,n:42200},{t:"1445",nowp:11.04, av:11.02,n:74900},{t:"1446",nowp:11.06, av:11.02,n:151880},{t:"1447",nowp:11.04, av:11.02,n:149280},{t:"1448",nowp:11.05, av:11.02,n:36000},{t:"1449",nowp:11.04, av:11.02,n:42300},{t:"1450",nowp:11.04, av:11.02,n:79400},{t:"1451",nowp:11.04, av:11.02,n:42150},{t:"1452",nowp:11.03, av:11.021,n:75600},{t:"1453",nowp:11.03, av:11.021,n:91400},{t:"1454",nowp:11.04, av:11.021,n:56000},{t:"1455",nowp:11.03, av:11.021,n:110000},{t:"1456",nowp:11.02, av:11.021,n:46640},{t:"1457",nowp:11.02, av:11.021,n:102071},{t:"1458",nowp:11.02, av:11.021,n:145800},{t:"1459",nowp:11.02, av:11.021,n:71580},{t:"1500",nowp:11.02, av:11.021,n:2200}]
        }

        main.colorPickerInit();
        main.initKlineColorBox();
        main.outputKlineColor();
        main.importKlineColor();

        main.initFsColorBox();

    }

});