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
            var d = [{t:"20161123",n:"6485995",i:"72.2",o:"73.09",a:"73.98",c:"72.59",s:"be",ma5:"71.928",ma10:"72.35",ma30:"70.2573"},{t:"20161124",n:"4329638",i:"71.6",o:"72.14999999999999",a:"72.94999999999999",c:"71.85",s:"be",ma5:"72.01",ma10:"72.374",ma30:"70.4547"},{t:"20161125",n:"3734351",i:"70.35",o:"71.35",a:"72.14999999999999",c:"71.97999999999999",s:"ab",ma5:"72.166",ma10:"72.275",ma30:"70.6843"},{t:"20161128",n:"10874380",i:"72.5",o:"74.05",a:"75.58",c:"72.98",s:"be",ma5:"72.52",ma10:"72.268",ma30:"70.94"},{t:"20161129",n:"7447597",i:"71.99",o:"72.08",a:"74.5",c:"73.69999999999999",s:"ab",ma5:"72.62",ma10:"72.32",ma30:"71.1473"},{t:"20161130",n:"25835082",i:"72.98",o:"73.4",a:"81.06",c:"78.67",s:"ab",ma5:"73.836",ma10:"72.882",ma30:"71.522"},{t:"20161201",n:"12840961",i:"76.8",o:"77.23",a:"78.67999999999999",c:"78.67",s:"ab",ma5:"75.2",ma10:"73.605",ma30:"71.909"},{t:"20161202",n:"11980196",i:"77.49",o:"78.14999999999999",a:"80.94999999999999",c:"78",s:"be",ma5:"76.404",ma10:"74.285",ma30:"72.2873"},{t:"20161205",n:"8725017",i:"76.25",o:"76.88",a:"80.87",c:"78.29",s:"ab",ma5:"77.466",ma10:"74.993",ma30:"72.5973"},{t:"20161206",n:"8380084",i:"75",o:"78.29",a:"78.87",c:"75.18",s:"be",ma5:"77.762",ma10:"75.191",ma30:"72.821"},{t:"20161207",n:"6738418",i:"73.7",o:"75.31",a:"75.8",c:"74.98",s:"be",ma5:"77.024",ma10:"75.43",ma30:"73.034"},{t:"20161208",n:"4831985",i:"73.8",o:"74.61",a:"75.28999999999999",c:"74.02",s:"be",ma5:"76.094",ma10:"75.647",ma30:"73.172"},{t:"20161209",n:"6330411",i:"73.5",o:"73.98",a:"74.89",c:"73.58",s:"be",ma5:"75.21",ma10:"75.807",ma30:"73.227"},{t:"20161212",n:"11926301",i:"68.1",o:"73.11",a:"73.35",c:"68.35",s:"be",ma5:"73.222",ma10:"75.344",ma30:"73.1273"},{t:"20161213",n:"5694407",i:"66.49",o:"68.24",a:"69.64999999999999",c:"68.8",s:"ab",ma5:"71.946",ma10:"74.854",ma30:"72.9777"},{t:"20161214",n:"3727004",i:"68.1",o:"68.6",a:"69.94999999999999",c:"68.80999999999999",s:"ab",ma5:"70.712",ma10:"73.868",ma30:"72.847"},{t:"20161215",n:"4457573",i:"68.18",o:"68.57000000000001",a:"70.09",c:"69.47000000000001",s:"ab",ma5:"69.802",ma10:"72.948",ma30:"72.7263"},{t:"20161216",n:"6241148",i:"69.04",o:"69.32000000000001",a:"73.19000000000001",c:"71.46000000000001",s:"ab",ma5:"69.378",ma10:"72.294",ma30:"72.745"},{t:"20161219",n:"3818624",i:"69.31",o:"70.88",a:"70.9",c:"69.94",s:"be",ma5:"69.696",ma10:"71.459",ma30:"72.743"},{t:"20161220",n:"3475555",i:"68.91",o:"69.72999999999999",a:"70.95",c:"69.39",s:"be",ma5:"69.814",ma10:"70.88",ma30:"72.704"},{t:"20161221",n:"2912580",i:"69.3",o:"70.09",a:"70.88",c:"69.81",s:"be",ma5:"70.014",ma10:"70.363",ma30:"72.7143"},{t:"20161222",n:"2457449",i:"68.66",o:"69.72999999999999",a:"69.97999999999999",c:"69.3",s:"be",ma5:"69.98",ma10:"69.891",ma30:"72.6373"},{t:"20161223",n:"4662413",i:"66.66",o:"69.17999999999999",a:"69.47999999999999",c:"67",s:"be",ma5:"69.088",ma10:"69.233",ma30:"72.4383"},{t:"20161226",n:"3845151",i:"66.01",o:"67.04",a:"68.55000000000001",c:"68.46000000000001",s:"ab",ma5:"68.792",ma10:"69.244",ma30:"72.2853"},{t:"20161227",n:"2520366",i:"67.78",o:"68.4",a:"69.23",c:"68.5",s:"ab",ma5:"68.614",ma10:"69.214",ma30:"72.1293"},{t:"20161228",n:"2421373",i:"68.07",o:"68.1",a:"68.78999999999999",c:"68.52999999999999",s:"ab",ma5:"68.358",ma10:"69.186",ma30:"71.9787"},{t:"20161229",n:"4874738",i:"68.35",o:"69.08999999999999",a:"70.36999999999999",c:"69.74",s:"ab",ma5:"68.446",ma10:"69.213",ma30:"71.922"},{t:"20161230",n:"3094281",i:"68.5",o:"69.73",a:"70.33",c:"68.78",s:"be",ma5:"68.802",ma10:"68.945",ma30:"71.8413"},{s:"ab",t:"20170104",o:"70.02",a:"73.1",i:"70",c:"72.55",n:"7206841",np:"518713980",h:"2.734",ma5:"69.62",ma10:"69.206",ma30:"71.886"}];


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
            $('.mousepng').css('left', (x(20)+14)+'px');

        }

        var outputKlineColor = function(){

            $(main.outputKlineDom).on('click', function(event) {
                event.preventDefault();
                
                var $ele = $('.configsetting');

                var cc = {
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
                };

                var tempcc;
                function arrangeColorSettings (o, name, obj){
                    if(typeof o == "string"){ 
                        var idname = 'idhqKl'+name;
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

                $('.klinecc-result').html(obj2string(cc));
            });
        }

        var importKlineColor = function(){
            $(main.importKlineDom).on('click', function(event) {
                event.preventDefault();
                $('.importKlineColorTextarea').css('display', 'block');
            });

            $('.importKlineColorTextarea').on('click', '.cancle', function(event) {
                event.preventDefault();
                $('.importKlineColorTextarea').css('display', 'none');
            });

            $('.importKlineColorTextarea').on('click', '.sayyes', function(event) {
                event.preventDefault();
                var ccstr = $(this).parent().siblings('textarea').val();
                var cc = eval('(' + ccstr + ')');

                var $ele = $('.configsetting');

                function arrangeColorSettings (o, name, obj){
                    if(typeof o == "string"){ 
                        var idname = 'idhqKl'+name;
                        var iddom = $ele.find('input[id^="'+idname+'"]');
                        if (iddom.length>0) {
                            iddom.val(o);
                            iddom.colorPicker(main.colorPickerConfigs);

                            var b = iddom.data('influence');

                            if(/cc$/.test(b)){
                                $('.'+b).css('color', o);
                            }else if(/svg$/.test(b)){
                                $('.'+b).css('stroke', o);
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

                $('.importKlineColorTextarea').css('display', 'none');
            });
        }

        var colorPickerInit = function(){
            $('.jscolor').colorPicker(main.colorPickerConfigs);
        }

        var main = {
            klineBoxDom:'#klineBox',
            outputKlineDom:'#outputKlineColor',
            importKlineDom:'#importKlineColor',
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
            initKlineColorBox: initKlineColorBox 
        }

        main.colorPickerInit();
        main.initKlineColorBox();
        main.outputKlineColor();
        main.importKlineColor();
    }

});