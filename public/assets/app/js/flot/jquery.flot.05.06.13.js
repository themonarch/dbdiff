/*! Javascript plotting library for jQuery, v. 0.7.
 *
 * Released under the MIT license by IOLA, December 2007.
 *
 */

// first an inline dependency, jquery.colorhelpers.js, we inline it here
// for convenience

/* Plugin for jQuery for working with colors.
 *
 * Version 1.1.
 *
 * Inspiration from jQuery color animation plugin by John Resig.
 *
 * Released under the MIT license by Ole Laursen, October 2009.
 *
 * Examples:
 *
 *   $.color.parse("#fff").scale('rgb', 0.25).add('a', -0.5).toString()
 *   var c = $.color.extract($("#mydiv"), 'background-color');
 *   console.log(c.r, c.g, c.b, c.a);
 *   $.color.make(100, 50, 25, 0.4).toString() // returns "rgba(100,50,25,0.4)"
 *
 * Note that .scale() and .add() return the same modified object
 * instead of making a new one.
 *
 * V. 1.1: Fix error handling so e.g. parsing an empty string does
 * produce a color rather than just crashing.
 */
(function(B){B.color={};B.color.make=function(F,E,C,D){var G={};G.r=F||0;G.g=E||0;G.b=C||0;G.a=D!=null?D:1;G.add=function(J,I){for(var H=0;H<J.length;++H){G[J.charAt(H)]+=I}return G.normalize()};G.scale=function(J,I){for(var H=0;H<J.length;++H){G[J.charAt(H)]*=I}return G.normalize()};G.toString=function(){if(G.a>=1){return"rgb("+[G.r,G.g,G.b].join(",")+")"}else{return"rgba("+[G.r,G.g,G.b,G.a].join(",")+")"}};G.normalize=function(){function H(J,K,I){return K<J?J:(K>I?I:K)}G.r=H(0,parseInt(G.r),255);G.g=H(0,parseInt(G.g),255);G.b=H(0,parseInt(G.b),255);G.a=H(0,G.a,1);return G};G.clone=function(){return B.color.make(G.r,G.b,G.g,G.a)};return G.normalize()};B.color.extract=function(D,C){var E;do{E=D.css(C).toLowerCase();if(E!=""&&E!="transparent"){break}D=D.parent()}while(!B.nodeName(D.get(0),"body"));if(E=="rgba(0, 0, 0, 0)"){E="transparent"}return B.color.parse(E)};B.color.parse=function(F){var E,C=B.color.make;if(E=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(F)){return C(parseInt(E[1],10),parseInt(E[2],10),parseInt(E[3],10))}if(E=/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(F)){return C(parseInt(E[1],10),parseInt(E[2],10),parseInt(E[3],10),parseFloat(E[4]))}if(E=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(F)){return C(parseFloat(E[1])*2.55,parseFloat(E[2])*2.55,parseFloat(E[3])*2.55)}if(E=/rgba\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\s*\)/.exec(F)){return C(parseFloat(E[1])*2.55,parseFloat(E[2])*2.55,parseFloat(E[3])*2.55,parseFloat(E[4]))}if(E=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(F)){return C(parseInt(E[1],16),parseInt(E[2],16),parseInt(E[3],16))}if(E=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(F)){return C(parseInt(E[1]+E[1],16),parseInt(E[2]+E[2],16),parseInt(E[3]+E[3],16))}var D=B.trim(F).toLowerCase();if(D=="transparent"){return C(255,255,255,0)}else{E=A[D]||[0,0,0];return C(E[0],E[1],E[2])}};var A={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0]}})(jQuery);

// the actual Flot code
(function($) {
    function Plot(placeholder, data_, options_, plugins) {
        // data is on the form:
        //   [ series1, series2 ... ]
        // where series is either just the data as [ [x1, y1], [x2, y2], ... ]
        // or { data: [ [x1, y1], [x2, y2], ... ], label: "some label", ... }

        var series = [],
            options = {
                // the color theme used for graphs
                colors: ["#F2B705", "#A60303", "#D95204", "#F28705", "#5B0000", "#FFFF37"],
                legend: {
                    show: true,
                    noColumns: 1, // number of colums in legend table
                    labelFormatter: null, // fn: string -> string
                    labelBoxBorderColor: "#ccc", // border color for the little label boxes
                    container: null, // container (as jQuery object) to put legend in, null means default on top of graph
                    position: "ne", // position of default legend container within plot
                    margin: 5, // distance from grid edge to default legend container within plot
                    backgroundColor: null, // null means auto-detect
                    backgroundOpacity: 0 // set to 0 to avoid background
                },
                onReady: null,
                xaxis: {
                    show: null, // null = auto-detect, true = always, false = never
                    position: "bottom", // or "top"
                    mode: null, // null or "time"
                    color: null, // base color, labels, ticks
                    tickColor: null, // possibly different color of ticks, e.g. "rgba(0,0,0,0.15)"
                    transform: null, // null or f: number -> number to transform axis
                    inverseTransform: null, // if transform is set, this should be the inverse function
                    //min: null, // min. value to show, null means set automatically
                    //max: null, // max. value to show, null means set automatically
                    min: daterange_start_timestamp,
                    max: daterange_end_timestamp,
                    autoscaleMargin: null, // margin in % to add if auto-setting min/max
                    ticks: null, // either [1, 3] or [[1, "a"], 3] or (fn: axis info -> ticks) or app. number of ticks for auto-ticks
                    tickFormatter: null, // fn: number -> string
                    labelWidth: null, // size of tick labels in pixels
                    labelHeight: null,
                    invert: false, // inverts the axis when true
                    reserveSpace: null, // whether to reserve space even if axis isn't shown
                    tickLength: 10, // size in pixels of ticks, or "full" for whole line
                    alignTicksWithAxis: null, // axis number or null for no sync

                    // mode specific options
                    tickDecimals: null, // no. of decimals, null means auto
                    tickSize: null, // number or [number, "unit"]
                    minTickSize: null, // number or [number, "unit"]
                    minSize: "day",
                    monthNames: null, // list of names of months
                    timeformat: null, // format string to use
                	magn: null,
                    twelveHourClock: false // 12 or 24 time in time mode
                },
                yaxis: {
                    autoscaleMargin: .3,
                    invert: false, // inverts the axis when true
                    logarithmicScale: false,
                    position: "left" // or "right"
                },

                chartName : 'flot',
                xaxes: [],
                yaxes: [],
                series: {
                    points: {
                        show: false,
                        radius: 4,
                        lineWidth: 3, // in pixels
                        fill: true,
                        shadow: true,
                        fillColor: "#ffffff",
                        symbol: "circle" // or callback
                    },
                    lines: {
                        // we don't put in show: false so we can see
                        // whether lines were actively disabled
                        lineWidth: 4, // in pixels
                        fill: false,
                        fillColor: null,
                        steps: false
                    },
                    bars: {
                        show: false,
                        lineWidth: 2, // in pixels
                        barWidth: 1, // in units of the x axis
                        fill: true,
                        fillColor: null,
                        align: "left", // or "center"
                        horizontal: false
                    },
            watermark : {
                show : true,
                alpha : .13,
                xposition : 'center',
                yposition : 'center',
                xoffset : 0,
                yoffset : 0,
                width: 150//set to null for no scaling
            },
            animate : {
                show: true,
                start : .1,
                speed : 20
            },
        grow: {
                active: false,
                valueIndex: 1,
                stepDelay: 0,
                steps: 20,
                stepMode: "linear",
                stepDirection: "up"
            },

                    shadowSize: 4
                },
                grid: {
                    show: true,
                    aboveData: false,
                    color: "#545454", // primary color used for outline and labels
                    backgroundColor: null, // null for transparent, else color
                    borderColor: null, // set if different from the grid color
                    tickColor: null, // color for the ticks, e.g. "rgba(0,0,0,0.15)"
                    labelMargin: 5, // in pixels
                    axisMargin: 8, // in pixels
                    borderWidth: 1, // in pixels
                    minBorderMargin: null, // in pixels, null means taken from points radius
                    markings: null, // array of ranges or fn: axes -> array of ranges
                    markingsColor: "#f4f4f4",
                    markingsLineWidth: 1,
                    // interactive stuff
                    clickable: false,
                    hoverable: false,
                    autoHighlight: true, // highlight in case mouse is near
                    mouseActiveRadius: 10 // how far the mouse can be away to activate an item
                },

                hooks: {}
            },
        canvas = null,      // the canvas for the plot itself
        overlay = null,     // canvas for interactive stuff on top of plot
        eventHolder = null, // jQuery object that events should be bound to
        ctx = null, octx = null,
        xaxes = [], yaxes = [],
        plotOffset = { left: 0, right: 0, top: 0, bottom: 0},
        canvasWidth = 0, canvasHeight = 0,
        plotWidth = 0, plotHeight = 0,
        hooks = {
            processOptions: [],
            processRawData: [],
            processDatapoints: [],
            drawSeries: [],
            draw: [],
            bindEvents: [],
            drawOverlay: [],
            shutdown: []
        },
        plot = this;

        // public functions
        plot.setData = setData;
        plot.setupGrid = setupGrid;
        plot.draw = draw;
        plot.getPlaceholder = function() { return placeholder; };
        plot.getCanvas = function() { return canvas; };
        plot.getPlotOffset = function() { return plotOffset; };
        plot.width = function () { return plotWidth; };
        plot.height = function () { return plotHeight; };
        plot.offset = function () {
            var o = eventHolder.offset();
            o.left += plotOffset.left;
            o.top += plotOffset.top;
            return o;
        };
        plot.getData = function () { return series; };
        plot.getAxes = function () {
            var res = {}, i;
            $.each(xaxes.concat(yaxes), function (_, axis) {
                if (axis)
                    res[axis.direction + (axis.n != 1 ? axis.n : "") + "axis"] = axis;
            });
            return res;
        };
        plot.getXAxes = function () { return xaxes; };
        plot.getYAxes = function () { return yaxes; };
        plot.c2p = canvasToAxisCoords;
        plot.p2c = axisToCanvasCoords;
        plot.getOptions = function () { return options; };
        plot.highlight = highlight;
        plot.unhighlight = unhighlight;
        plot.triggerRedrawOverlay = triggerRedrawOverlay;
        plot.pointOffset = function(point) {
            return {
                left: parseInt(xaxes[axisNumber(point, "x") - 1].p2c(+point.x) + plotOffset.left),
                top: parseInt(yaxes[axisNumber(point, "y") - 1].p2c(+point.y) + plotOffset.top)
            };
        };
        plot.shutdown = shutdown;
        plot.resize = function () {
            getCanvasDimensions();
            resizeCanvas(canvas);
            resizeCanvas(overlay);
        };

        // public attributes
        plot.hooks = hooks;

        plot.highlightSeries = function (event, series_id) {
              if (event.type == 'mouseover' && series_id != undefined) {
                  for(var i = 0; i < series.length; i++){
                      series[i].opacity = .4;
                  }
                  series[series_id].opacity = 1;
                  for(var i = 0; i < options.seriesOrder.length; i++){
                      if(options.seriesOrder[options.seriesOrder.length-1] == series_id)
                      break;
                      options.seriesOrder.unshift(options.seriesOrder.pop());
                  }
                $('.y'+series[series_id].yaxis.n+'Axis').attr('id', 'yAxisHighlight').find(".tick_highlight").css({'border-top': "2px solid "+series[series_id].color, 'background': $.color.parse(series[series_id].color).scale('a', .1).toString()});
                  draw();
              } else if(event.type == 'mouseout') {
                  for(var i = 0; i < series.length; i++){
                      series[i].opacity = 1;
                  }
                  draw();
                $('#yAxisHighlight').css({'color': 'inherit'}).find(".tick_highlight").css({'border-top': 'none', 'background': 'none'}).parent().removeAttr('id');
              }


            };

        // initialize
        initPlugins(plot);
        parseOptions(options_);
        setupCanvases();
        setData(data_);
        setupGrid();
        insertLegend();

        if (typeof options.onReady == "function"){
        	options.onReady();
        }

        if(options.series.animate.show == true){
        var growfunc = [];

        growfunc[options.chartName] = window.setInterval(animatedDraw, options.series.animate.speed);



       }else{
           draw();
           bindEvents();
       }


        function animatedDraw(){

            if(options.series.animate.start >= 1) {
                options.series.animate.start = 1;

                bindEvents();
                window.clearInterval(growfunc[options.chartName]);
            }
            else{ options.series.animate.start += .02;


            draw();
            }

        }



        function executeHooks(hook, args) {
            args = [plot].concat(args);
            for (var i = 0; i < hook.length; ++i)
                hook[i].apply(this, args);
        }

        function initPlugins() {
            for (var i = 0; i < plugins.length; ++i) {
                var p = plugins[i];
                p.init(plot);
                if (p.options)
                    $.extend(true, options, p.options);
            }
        }

        function parseOptions(opts) {
            var i;

            $.extend(true, options, opts);

            if (options.xaxis.color == null)
                options.xaxis.color = options.grid.color;
            if (options.yaxis.color == null)
                options.yaxis.color = options.grid.color;

            if (options.xaxis.tickColor == null) // backwards-compatibility
                options.xaxis.tickColor = options.grid.tickColor;
            if (options.yaxis.tickColor == null) // backwards-compatibility
                options.yaxis.tickColor = options.grid.tickColor;
            if (options.grid.borderColor == null)
                options.grid.borderColor = options.grid.color;
            if (options.grid.tickColor == null)
                options.grid.tickColor = $.color.parse(options.grid.color).scale('a', 0.22).toString();


            // fill in defaults in axes, copy at least always the
            // first as the rest of the code assumes it'll be there
            for (i = 0; i < Math.max(1, options.xaxes.length); ++i)
                options.xaxes[i] = $.extend(true, {}, options.xaxis, options.xaxes[i]);
            for (i = 0; i < Math.max(1, options.yaxes.length); ++i)
                options.yaxes[i] = $.extend(true, {}, options.yaxis, options.yaxes[i]);



            // backwards compatibility, to be removed in future
            if (options.xaxis.noTicks && options.xaxis.ticks == null)
                options.xaxis.ticks = options.xaxis.noTicks;
            if (options.yaxis.noTicks && options.yaxis.ticks == null)
                options.yaxis.ticks = options.yaxis.noTicks;
            if (options.x2axis) {
                options.xaxes[1] = $.extend(true, {}, options.xaxis, options.x2axis);
                options.xaxes[1].position = "top";
            }
            if (options.y2axis) {
                options.yaxes[1] = $.extend(true, {}, options.yaxis, options.y2axis);
                options.yaxes[1].position = "right";
            }
            if (options.grid.coloredAreas)
                options.grid.markings = options.grid.coloredAreas;
            if (options.grid.coloredAreasColor)
                options.grid.markingsColor = options.grid.coloredAreasColor;
            if (options.lines)
                $.extend(true, options.series.lines, options.lines);
            if (options.points)
                $.extend(true, options.series.points, options.points);
            if (options.bars)
                $.extend(true, options.series.bars, options.bars);
            if (options.shadowSize != null)
                options.series.shadowSize = options.shadowSize;

            // save options on axes for future reference
            for (i = 0; i < options.xaxes.length; ++i)
                getOrCreateAxis(xaxes, i + 1).options = options.xaxes[i];
            for (i = 0; i < options.yaxes.length; ++i)
                getOrCreateAxis(yaxes, i + 1).options = options.yaxes[i];

            // add hooks from options
            for (var n in hooks)
                if (options.hooks[n] && options.hooks[n].length)
                    hooks[n] = hooks[n].concat(options.hooks[n]);

            if(options.barrier_limits == undefined) options.barrier_limits = [];
            if(options.barrier_limits.xaxis == undefined) options.barrier_limits.xaxis = {last_date:0, first_date:0};

            executeHooks(hooks.processOptions, [options]);
        }

        function setData(d) {
            series = parseData(d);
            setSeriesOrder();
            fillInSeriesOptions();
            processData();

        }

        function parseData(d) {
            var res = [];
            for (var i = 0; i < d.length; ++i) {
                options.series.n = i;
                var s = $.extend(true, {}, options.series);

                if (d[i] != null && d[i].data != null) {
                    s.data = d[i].data; // move the data instead of deep-copy
                    delete d[i].data;

                    $.extend(true, s, d[i]);

                    d[i].data = s.data;
                }
                else
                    s.data = d[i];
                res.push(s);
            }

            return res;
        }
        function setSeriesOrder(){
            options.seriesOrder = [];
            for (i = 0; i < series.length; ++i) {
                options.seriesOrder[i] = i;
            }
        }
        function axisNumber(obj, coord) {
            var a = obj[coord + "axis"];
            if (typeof a == "object") // if we got a real axis, extract number
                a = a.n;
            if (typeof a != "number")
                a = 1; // default to first axis
            return a;
        }

        function allAxes() {
            // return flat array without annoying null entries
            return $.grep(xaxes.concat(yaxes), function (a) { return a; });
        }

        function canvasToAxisCoords(pos) {
            // return an object with x/y corresponding to all used axes
            var res = {}, i, axis;
            for (i = 0; i < xaxes.length; ++i) {
                axis = xaxes[i];
                if (axis && axis.used)
                    res["x" + axis.n] = axis.c2p(pos.left);
            }

            for (i = 0; i < yaxes.length; ++i) {
                axis = yaxes[i];
                if (axis && axis.used)
                    res["y" + axis.n] = axis.c2p(pos.top);
            }

            if (res.x1 !== undefined)
                res.x = res.x1;
            if (res.y1 !== undefined)
                res.y = res.y1;

            return res;
        }

        function axisToCanvasCoords(pos) {
            // get canvas coords from the first pair of x/y found in pos
            var res = {}, i, axis, key;

            for (i = 0; i < xaxes.length; ++i) {
                axis = xaxes[i];
                if (axis && axis.used) {
                    key = "x" + axis.n;
                    if (pos[key] == null && axis.n == 1)
                        key = "x";

                    if (pos[key] != null) {
                        res.left = axis.p2c(pos[key]);
                        break;
                    }
                }
            }

            for (i = 0; i < yaxes.length; ++i) {
                axis = yaxes[i];
                if (axis && axis.used) {
                    key = "y" + axis.n;
                    if (pos[key] == null && axis.n == 1)
                        key = "y";

                    if (pos[key] != null) {
                        res.top = axis.p2c(pos[key]);
                        break;
                    }
                }
            }

            return res;
        }

        function getOrCreateAxis(axes, number) {
            if (!axes[number - 1])
                axes[number - 1] = {
                    n: number, // save the number for future reference
                    direction: axes == xaxes ? "x" : "y",
                    options: $.extend(true, {}, axes == xaxes ? options.xaxis : options.yaxis)
                };
            return axes[number - 1];
        }

        function fillInSeriesOptions() {
            var i;

            // collect what we already got of colors
            var neededColors = series.length,
                usedColors = [],
                assignedColors = [];
            for (i = 0; i < series.length; ++i) {
                var sc = series[i].color;
                if (sc != null) {
                    --neededColors;
                    if (typeof sc == "number")
                        assignedColors.push(sc);
                    else
                        usedColors.push($.color.parse(series[i].color));
                }
            }

            // we might need to generate more colors if higher indices
            // are assigned
            for (i = 0; i < assignedColors.length; ++i) {
                neededColors = Math.max(neededColors, assignedColors[i] + 1);
            }

            // produce colors as needed
            var colors = [], variation = 0;
            i = 0;
            while (colors.length < neededColors) {
                var c;
                if (options.colors.length == i) // check degenerate case
                    c = $.color.make(100, 100, 100);
                else
                    c = $.color.parse(options.colors[i]);

                // vary color if needed
                var sign = variation % 2 == 1 ? -1 : 1;
                c.scale('rgb', 1 + sign * Math.ceil(variation / 2) * 0.4)

                // FIXME: if we're getting to close to something else,
                // we should probably skip this one
                colors.push(c);

                ++i;
                if (i >= options.colors.length) {
                    i = 0;
                    ++variation;
                }
            }


            // fill in the options
            var colori = 0, s;
            for (i = 0; i < series.length; ++i) {
                s = series[i];

                // assign colors
                if (s.color == null) {
                    s.color = colors[colori].toString();
                    s.opacity = 1;
                    ++colori;
                }
                else if (typeof s.color == "number")
                    s.color = colors[s.color].toString();

                // turn on lines automatically in case nothing is set
                if (s.lines.show == null) {
                    var v, show = true;
                    for (v in s)
                        if (s[v] && s[v].show) {
                            show = false;
                            break;
                        }
                    if (show)
                        s.lines.show = true;
                }

                // setup axes
                s.xaxis = getOrCreateAxis(xaxes, axisNumber(s, "x"));
                s.yaxis = getOrCreateAxis(yaxes, axisNumber(s, "y"));
            }
        }

        function processData() {
            var topSentry = Number.POSITIVE_INFINITY,
                bottomSentry = Number.NEGATIVE_INFINITY,
                fakeInfinity = Number.MAX_VALUE,
                i, j, k, m, length,
                s, points, ps, x, y, axis, val, f, p;

            function updateAxis(axis, min, max) {
                if (min < axis.datamin && min != -fakeInfinity)
                    axis.datamin = min;
                if (max > axis.datamax && max != fakeInfinity)
                    axis.datamax = max;
            }

            $.each(allAxes(), function (_, axis) {
                // init axis
                axis.datamin = topSentry;
                axis.datamax = bottomSentry;
 if (axis.direction == "x") {
 axis.invert = axis.options.invert;
 }else{
 axis.invert = axis.options.invert;
 }
                axis.used = false;
            });

            for (i = 0; i < series.length; ++i) {
                s = series[i];
                s.datapoints = { points: [] };

                executeHooks(hooks.processRawData, [ s, s.data, s.datapoints ]);
            }

            // first pass: clean and copy data
            for (i = 0; i < series.length; ++i) {
                s = series[i];

                var data = s.data, format = s.datapoints.format;

                if (!format) {
                    format = [];
                    // find out how to copy
                    format.push({ x: true, number: true, required: true });
                    format.push({ y: true, number: true, required: true });

                    if (s.bars.show || (s.lines.show && s.lines.fill)) {
                        format.push({ y: true, number: true, required: false, defaultValue: 0 });
                        if (s.bars.horizontal) {
                            delete format[format.length - 1].y;
                            format[format.length - 1].x = true;
                        }
                    }

                    s.datapoints.format = format;
                }

                if (s.datapoints.pointsize != null)
                    continue; // already filled in

                s.datapoints.pointsize = format.length;

                ps = s.datapoints.pointsize;
                points = s.datapoints.points;

                insertSteps = s.lines.show && s.lines.steps;
                s.xaxis.used = s.yaxis.used = true;
                if(data != null)
                for (j = k = 0; j < data.length; ++j, k += ps) {
                    p = data[j];

                    var nullify = p == null;
                    if (!nullify) {
                        for (m = 0; m < ps; ++m) {
                            val = p[m];
                            f = format[m];

                            if (f) {
                                if (f.number && val != null) {
                                    val = +val; // convert to number
                                    if (isNaN(val))
                                        val = null;
                                    else if (val == Infinity)
                                        val = fakeInfinity;
                                    else if (val == -Infinity)
                                        val = -fakeInfinity;
                                }

                                if (val == null) {
                                    if (f.required)
                                        nullify = true;

                                    if (f.defaultValue != null)
                                        val = f.defaultValue;
                                }
                            }

                            points[k + m] = val;
                        }
                    }

                    if (nullify) {
                        for (m = 0; m < ps; ++m) {
                            val = points[k + m];
                            if (val != null) {
                                f = format[m];
                                // extract min/max info
                                if (f.x)
                                    updateAxis(s.xaxis, val, val);
                                if (f.y)
                                    updateAxis(s.yaxis, val, val);
                            }
                            points[k + m] = null;
                        }
                    }
                    else {
                        // a little bit of line specific stuff that
                        // perhaps shouldn't be here, but lacking
                        // better means...
                        if (insertSteps && k > 0
                            && points[k - ps] != null
                            && points[k - ps] != points[k]
                            && points[k - ps + 1] != points[k + 1]) {
                            // copy the point to make room for a middle point
                            for (m = 0; m < ps; ++m)
                                points[k + ps + m] = points[k + m];

                            // middle point has same y
                            points[k + 1] = points[k - ps + 1];

                            // we've added a point, better reflect that
                            k += ps;
                        }
                    }
                }
            }

            // give the hooks a chance to run
            for (i = 0; i < series.length; ++i) {
                s = series[i];

                executeHooks(hooks.processDatapoints, [ s, s.datapoints]);
            }

            // second pass: find datamax/datamin for auto-scaling
            for (i = 0; i < series.length; ++i) {
                s = series[i];
                points = s.datapoints.points,
                ps = s.datapoints.pointsize;

                var xmin = topSentry, ymin = topSentry,
                    xmax = bottomSentry, ymax = bottomSentry;

                for (j = 0; j < points.length; j += ps) {
                    if (points[j] == null)
                        continue;

                    for (m = 0; m < ps; ++m) {
                        val = points[j + m];
                        f = format[m];
                        if (!f || val == fakeInfinity || val == -fakeInfinity)
                            continue;

                        if (f.x) {
                            if (val < xmin)
                                xmin = val;
                            if (val > xmax)
                                xmax = val;
                        }
                        if (f.y) {
                            if (val < ymin)
                                ymin = val;
                            if (val > ymax)
                                ymax = val;
                        }
                    }
                }

                if (s.bars.show) {
                    // make sure we got room for the bar on the dancing floor
                    var delta = s.bars.align == "left" ? 0 : -s.bars.barWidth/2;
                    if (s.bars.horizontal) {
                        ymin += delta;
                        ymax += delta + s.bars.barWidth;
                    }
                    else {
                        xmin += delta;
                        xmax += delta + s.bars.barWidth;
                    }
                }

                updateAxis(s.xaxis, xmin, xmax);
                updateAxis(s.yaxis, ymin, ymax);
            }

            $.each(allAxes(), function (_, axis) {
                if (axis.datamin == topSentry)
                    axis.datamin = null;
                if (axis.datamax == bottomSentry)
                    axis.datamax = null;
            });
        }

        function makeCanvas(skipPositioning, cls) {
            var c = document.createElement('canvas');
            c.className = cls;
            c.width = canvasWidth;
            c.height = canvasHeight;

            if (!skipPositioning)
                $(c).css({ position: 'absolute', left: 0, top: 0 });

            $(c).appendTo(placeholder);

            if (!c.getContext){ // excanvas hack
                c = window.G_vmlCanvasManager.initElement(c);
                c.excanvas = true;
            }

            // used for resetting in case we get replotted
            c.getContext("2d").save();

            return c;
        }

        function getCanvasDimensions() {
            canvasWidth = placeholder.width();
            canvasHeight = placeholder.height();

            if (canvasWidth <= 0 || canvasHeight <= 0)
                throw "Invalid dimensions for plot, width = " + canvasWidth + ", height = " + canvasHeight;
        }

        function resizeCanvas(c) {
            // resizing should reset the state (excanvas seems to be
            // buggy though)
            if (c.width != canvasWidth)
                c.width = canvasWidth;

            if (c.height != canvasHeight)
                c.height = canvasHeight;

            // so try to get back to the initial state (even if it's
            // gone now, this should be safe according to the spec)
            var cctx = c.getContext("2d");
            cctx.restore();

            // and save again
            cctx.save();
        }

        function setupCanvases() {
            var reused,
                existingCanvas = placeholder.children("canvas.base"),
                existingOverlay = placeholder.children("canvas.overlay");

            if (existingCanvas.length == 0 || existingOverlay == 0) {
                // init everything

                //placeholder.html(""); // make sure placeholder is clear

                placeholder.css({ padding: 0 }); // padding messes up the positioning

                if (placeholder.css("position") == 'static')
                    placeholder.css("position", "relative"); // for positioning labels and overlay

                getCanvasDimensions();

                canvas = makeCanvas(true, "base");
                overlay = makeCanvas(false, "overlay"); // overlay canvas for interactive features

                reused = false;
            }
            else {
                // reuse existing elements

                canvas = existingCanvas.get(0);
                overlay = existingOverlay.get(0);

                reused = true;
            }

            ctx = canvas.getContext("2d");
            octx = overlay.getContext("2d");
            ctx.dashedLine = function(x, y, x2, y2, da) {
                if (!da) da = [10,5];
                this.save();
                var dx = (x2-x), dy = (y2-y);
                var len = Math.sqrt(dx*dx + dy*dy);
                var rot = Math.atan2(dy, dx);
                this.translate(x, y);
                this.moveTo(0, 0);
                this.rotate(rot);
                var dc = da.length;
                var di = 0, draw = true;
                x = 0;
                while (len > x) {
                    x += da[di++ % dc];
                    if (x > len) x = len;
                    draw ? this.lineTo(x, 0): this.moveTo(x, 0);
                    draw = !draw;
                }
                this.restore();
            }
            // we include the canvas in the event holder too, because IE 7
            // sometimes has trouble with the stacking order
            eventHolder = $([overlay, canvas]);

            if (reused) {
                // run shutdown in the old plot object
                placeholder.data("plot").shutdown();

                // reset reused canvases
                plot.resize();

                // make sure overlay pixels are cleared (canvas is cleared when we redraw)
                octx.clearRect(0, 0, canvasWidth, canvasHeight);

                // then whack any remaining obvious garbage left
                eventHolder.unbind();
                placeholder.children().not([canvas, overlay]).remove();
            }

            // save in case we get replotted
            placeholder.data("plot", plot);
        }

        function bindEvents() {
            // bind events
            if (options.grid.hoverable) {
                eventHolder.mousemove(onMouseMove);
                eventHolder.mouseleave(onMouseLeave);
            }

            if (options.grid.clickable)
                eventHolder.click(onClick);

            executeHooks(hooks.bindEvents, [eventHolder]);
        }

        function shutdown() {
            if (redrawTimeout)
                clearTimeout(redrawTimeout);

            eventHolder.unbind("mousemove", onMouseMove);
            eventHolder.unbind("mouseleave", onMouseLeave);
            eventHolder.unbind("click", onClick);

            executeHooks(hooks.shutdown, [eventHolder]);
        }

        function setTransformationHelpers(axis) {
            // set helper functions on the axis, assumes plot area
            // has been computed already

            function identity(x) { return x; }

            var s, m, t = axis.options.transform || identity,
                it = axis.options.inverseTransform;

            // precompute how much the axis is scaling a point
            // in canvas space
            if (axis.direction == "x") {
                s = axis.scale = plotWidth / Math.abs(t(axis.max) - t(axis.min));
            }
 else {
 s = axis.scale = plotHeight / (t(axis.max) - t(axis.min));
 }

 if(axis.invert || axis.direction == 'x') {
                m = Math.min(t(axis.max), t(axis.min));
            }
            else if(axis.direction != 'x') {
                //s = axis.scale = plotHeight / Math.abs(t(axis.max) - t(axis.min));
                s = -s;
                m = Math.max(t(axis.max), t(axis.min));
            }

            // data point to canvas coordinate
            if (t == identity) // slight optimization
                //normal points
                axis.p2c = function (p) { return (p - m) * s; };
            /* //upside down scale
                axis.p2c = function (p) {
                    if(p < 1000000)
                    return ((m - p) - m) * s;
                    else
                    return (p - m) * s;

                    };
            */
            else
                axis.p2c = function (p) { return (t(p) - m) * s; };
            // canvas coordinate to data point
            if (!it)
                axis.c2p = function (c) { return m + c / s; };
            else
                axis.c2p = function (c) { return it(m + c / s); };
        }

        function measureTickLabels(axis) {
            var opts = axis.options, i, ticks = axis.ticks || [], labels = [],
                l, w = opts.labelWidth, h = opts.labelHeight, dummyDiv;

            function makeDummyDiv(labels, width) {
                return $('<div style="position:absolute;top:-10000px;' + width + 'font-size:smaller">' +
                         '<div class="' + axis.direction + 'Axis ' + axis.direction + axis.n + 'Axis">'
                         + labels.join("") + '</div></div>')
                    .appendTo(placeholder);
            }

            if (axis.direction == "x") {
                // to avoid measuring the widths of the labels (it's slow), we
                // construct fixed-size boxes and put the labels inside
                // them, we don't need the exact figures and the
                // fixed-size box content is easy to center
                if (w == null)
                    w = Math.floor(canvasWidth / (ticks.length > 0 ? ticks.length : 1));

                // measure x label heights
                if (h == null) {
                    labels = [];
                    for (i = 0; i < ticks.length; ++i) {
                        l = ticks[i].label;
                        if (l)
                            labels.push('<div class="tickLabel" style="float:left;width:' + w + 'px">' + l + '</div>');
                    }

                    if (labels.length > 0) {
                        // stick them all in the same div and measure
                        // collective height
                        labels.push('<div style="clear:left"></div>');
                        dummyDiv = makeDummyDiv(labels, "width:10000px;");
                        h = dummyDiv.height();
                        dummyDiv.remove();
                    }
                }
            }
            else if (w == null || h == null) {
                // calculate y label dimensions
                for (i = 0; i < ticks.length; ++i) {
                    l = ticks[i].label;
                    if (l)
                        labels.push('<div class="tickLabel">' + l + '</div>');
                }

                if (labels.length > 0) {
                    dummyDiv = makeDummyDiv(labels, "");
                    if (w == null)
                        w = dummyDiv.children().width();
                    if (h == null)
                        h = dummyDiv.find("div.tickLabel").height();
                    dummyDiv.remove();
                }
            }

            if (w == null)
                w = 0;
            if (h == null)
                h = 0;

            axis.labelWidth = w;
            axis.labelHeight = h;
        }

        function allocateAxisBoxFirstPhase(axis) {
            // find the bounding box of the axis by looking at label
            // widths/heights and ticks, make room by diminishing the
            // plotOffset

            var lw = axis.labelWidth,
                lh = axis.labelHeight,
                pos = axis.options.position,
                tickLength = axis.options.tickLength,
                axismargin = options.grid.axisMargin,
                padding = options.grid.labelMargin,
                all = axis.direction == "x" ? xaxes : yaxes,
                index;

            // determine axis margin
            var samePosition = $.grep(all, function (a) {
                return a && a.options.position == pos && a.reserveSpace;
            });
            if ($.inArray(axis, samePosition) == samePosition.length - 1)
                axismargin = 0; // outermost

            // determine tick length - if we're innermost, we can use "full"
            if (tickLength == null)
                tickLength = "full";

            var sameDirection = $.grep(all, function (a) {
                return a && a.reserveSpace;
            });

            var innermost = $.inArray(axis, sameDirection) == 0;
            if (!innermost && tickLength == "full")
                tickLength = 5;

            if (!isNaN(+tickLength))
                padding += +tickLength;

            // compute box
            if (axis.direction == "x") {
                lh += padding;

                if (pos == "bottom") {
                    plotOffset.bottom += lh + axismargin;
                    axis.box = { top: canvasHeight - plotOffset.bottom, height: lh };
                }
                else {
                    axis.box = { top: plotOffset.top + axismargin, height: lh };
                    plotOffset.top += lh + axismargin;
                }
            }
            else {
                lw += padding;

                if (pos == "left") {
                    axis.box = { left: plotOffset.left + axismargin, width: lw };
                    plotOffset.left += lw + axismargin;
                }
                else {
                    plotOffset.right += lw + axismargin;
                    axis.box = { left: canvasWidth - plotOffset.right, width: lw };
                }
            }

            // save for future reference
            axis.position = pos;
            axis.tickLength = tickLength;
            axis.box.padding = padding;
            axis.innermost = innermost;
        }

        function allocateAxisBoxSecondPhase(axis) {
            // set remaining bounding box coordinates
            if (axis.direction == "x") {
                axis.box.left = plotOffset.left;
                axis.box.width = plotWidth;
            }
            else {
                axis.box.top = plotOffset.top;
                axis.box.height = plotHeight;
            }
        }

        function setupGrid() {
            var i, axes = allAxes();

            // first calculate the plot and axis box dimensions

            $.each(axes, function (_, axis) {
                axis.show = axis.options.show;
                if (axis.show == null)
                    axis.show = axis.used; // by default an axis is visible if it's got data

                axis.reserveSpace = axis.show || axis.options.reserveSpace;

                setRange(axis);
            });

            allocatedAxes = $.grep(axes, function (axis) { return axis.reserveSpace; });

            plotOffset.left = plotOffset.right = plotOffset.top = plotOffset.bottom = 0;


            if (options.grid.show) {
                $.each(allocatedAxes, function (_, axis) {
                    // make the ticks
                    setupTickGeneration(axis);
                    setTicks(axis);
                    snapRangeToTicks(axis, axis.ticks);

                    // find labelWidth/Height for axis
                    measureTickLabels(axis);
                });

                // with all dimensions in house, we can compute the
                // axis boxes, start from the outside (reverse order)
                for (i = allocatedAxes.length - 1; i >= 0; --i)
                    allocateAxisBoxFirstPhase(allocatedAxes[i]);

                // make sure we've got enough space for things that
                // might stick out
                var minMargin = options.grid.minBorderMargin;
                if (minMargin == null) {
                    minMargin = 0;
                    for (i = 0; i < series.length; ++i)
                        minMargin = Math.max(minMargin, series[i].points.radius + parseInt(series[i].points.lineWidth/2));
                }

                for (var a in plotOffset) {
                    plotOffset[a] += options.grid.borderWidth;
                    plotOffset[a] = Math.max(minMargin, plotOffset[a]);
                    if(plotOffset[a] != 1.5)
                    plotOffset[a] += .5;
                }

            }
            plotWidth = canvasWidth - plotOffset.left - plotOffset.right;
            plotHeight = canvasHeight - plotOffset.bottom - plotOffset.top;

            // now we got the proper plotWidth/Height, we can compute the scaling
            $.each(axes, function (_, axis) {
                setTransformationHelpers(axis);
            });

            if (options.grid.show) {
                $.each(allocatedAxes, function (_, axis) {
                    allocateAxisBoxSecondPhase(axis);
                });

                insertAxisLabels();
            }

        }

        function setRange(axis) {
            var opts = axis.options,
                min = +(opts.min != null ? opts.min : axis.datamin),
                max = +(opts.max != null ? opts.max : axis.datamax),
                delta = max - min;

            if (delta == 0.0) {
                // degenerate case
                var widen = max == 0 ? 1 : 0.01;

                if (opts.min == null)
                    min -= widen;
                // always widen max if we couldn't widen min to ensure we
                // don't fall into min == max which doesn't work
                if (opts.max == null || opts.min != null)
                    max += widen;
            }
            else {
                // consider autoscaling
                if(opts.logarithmicScale == true)
                var margin = opts.autoscaleMargin*30;
                else
                var margin = opts.autoscaleMargin;

                if (margin != null) {
                    if (opts.min == null) {
                        min -= delta * margin;
                        // make sure we don't go below zero if all values
                        // are positive
                        if (min < 0 && axis.datamin != null && axis.datamin >= 0)
                            min = 0;
                    }
                    if (opts.max == null) {
                        max += delta * margin;
                        if(opts.logarithmicScale == true)
                        max += max*2;

                        if (max > 0 && axis.datamax != null && axis.datamax <= 0)
                            max = 0;
                    }
                }
            }
            axis.min = min;
            axis.max = max;
        }

        function setupTickGeneration(axis) {
            var opts = axis.options;

            if(opts.logarithmicScale){
                            opts.min = 0;
                            //set datamax here
                            opts.logarithmicScale = true;
                            opts.transform = function (v) { if(v <= 1 || v == null) return v;  return Math.log(v); };
                            opts.inverseTransform = function (v) { if(v < 1 || v == null) return v; return Math.exp(v);
                                };
                        }

            // estimate number of ticks
            var noTicks;
            if (typeof opts.ticks == "number" && opts.ticks > 0)
                noTicks = opts.ticks;
            else
                // heuristic based on the model a*sqrt(x) fitted to
                // some data points that seemed reasonable
                noTicks = 0.3 * Math.sqrt(axis.direction == "x" ? canvasWidth : canvasHeight);

            var delta = (axis.max - axis.min) / noTicks,
                size, generator, unit, formatter, i, magn, norm;

            if (opts.mode == "time") {
                // pretty handling of time
                // map of app. size of time units in milliseconds
                var timeUnitSize = {
                    "second": 1000,
                    "minute": 60 * 1000,
                    "hour": 60 * 60 * 1000,
                    "day": 24 * 60 * 60 * 1000,
                    "month": 30 * 24 * 60 * 60 * 1000,
                    "year": 365.2425 * 24 * 60 * 60 * 1000
                };


                // the allowed tick sizes, after 1 year we use
                // an integer algorithm
                if (opts.minSize != null && typeof opts.minSize == "string") {
	                if(opts.minSize == "day"){
		                var spec = [
		                    //[1, "second"], [2, "second"], [5, "second"], [10, "second"],
		                    [1, "day"], [2, "day"], [5, "day"], [10, "day"],
		                    //[30, "second"],
		                    [30, "day"],
		                    //[1, "minute"], [2, "minute"], [5, "minute"], [10, "minute"],
		                    [1, "day"], [2, "day"], [5, "day"], [10, "day"],
		                    //[30, "minute"],
		                    [30, "day"],
		                    //[1, "hour"], [2, "hour"], [4, "hour"],
		                    [1, "day"], [2, "day"], [4, "day"],
		                    //[8, "hour"], [12, "hour"],
		                    [8, "day"], [12, "day"],
		                    [1, "day"], [2, "day"], [3, "day"],
		                    [0.25, "month"], [0.5, "month"], [1, "month"],
		                    [2, "month"], [3, "month"], [6, "month"],
		                    [1, "year"]
		                ];
	                }else if(opts.minSize == "month"){
		                var spec = [
		                    //[1, "second"], [2, "second"], [5, "second"], [10, "second"],
		                    [1, "month"], [2, "month"], [5, "month"], [10, "month"],
		                    //[30, "second"],
		                    [30, "month"],
		                    //[1, "minute"], [2, "minute"], [5, "minute"], [10, "minute"],
		                    [1, "month"], [2, "month"], [5, "month"], [10, "month"],
		                    //[30, "minute"],
		                    [30, "month"],
		                    //[1, "hour"], [2, "hour"], [4, "hour"],
		                    [1, "month"], [2, "month"], [4, "month"],
		                    //[8, "hour"], [12, "hour"],
		                    [8, "month"], [12, "month"],
		                    [1, "month"], [2, "month"], [3, "month"],
		                    [0.25, "month"], [0.5, "month"], [1, "month"],
		                    [2, "month"], [3, "month"], [6, "month"],
		                    [1, "year"]
		                ];
	            	}
            	}else{
	                var spec = [
	                    [1, "second"], [2, "second"], [5, "second"], [10, "second"],
	                    [30, "second"],
	                    [1, "minute"], [2, "minute"], [5, "minute"], [10, "minute"],
	                    [30, "minute"],
	                    [1, "hour"], [2, "hour"], [4, "hour"],
	                    [8, "hour"], [12, "hour"],
	                    [1, "day"], [2, "day"], [3, "day"],
	                    [0.25, "month"], [0.5, "month"], [1, "month"],
	                    [2, "month"], [3, "month"], [6, "month"],
	                    [1, "year"]
	                ];
				}


                var minSize = 0;
                /*
                if (opts.minTickSize != null) {
                    if (typeof opts.tickSize == "number")
                        minSize = opts.tickSize;
                    else
                        minSize = opts.minTickSize[0] * timeUnitSize[opts.minTickSize[1]];
                }
				*/
                for (var i = 0; i < spec.length - 1; ++i)
                    if (delta < (spec[i][0] * timeUnitSize[spec[i][1]]
                                 + spec[i + 1][0] * timeUnitSize[spec[i + 1][1]]) / 2
                       && spec[i][0] * timeUnitSize[spec[i][1]] >= minSize)
                        break;

                size = spec[i][0];
                if (opts.minSize != null && typeof opts.minSize == "string") {
               		unit = spec[i][1];
                }else{
                	unit = spec[i][1];
                }
                // special-case the possibility of several years
                if (unit == "year") {
                    magn = Math.pow(10, Math.floor(Math.log(delta / timeUnitSize.year) / Math.LN10));
                    norm = (delta / timeUnitSize.year) / magn;
                    if (norm < 1.5)
                        size = 1;
                    else if (norm < 3)
                        size = 2;
                    else if (norm < 7.5)
                        size = 5;
                    else
                        size = 10;

                    size *= magn;
                }
                axis.tickSize = opts.tickSize || [size, unit];

                generator = function(axis) {
                    var ticks = [],
                        tickSize = axis.tickSize[0], unit = axis.tickSize[1],
                        d = new Date(axis.min);

                    //console.log(tickSize, unit, timeUnitSize[unit]);
                    var step = tickSize * timeUnitSize[unit];

                    if (unit == "second")
                        d.setUTCSeconds(floorInBase(d.getUTCSeconds(), tickSize));
                    if (unit == "minute")
                        d.setUTCMinutes(floorInBase(d.getUTCMinutes(), tickSize));
                    if (unit == "hour")
                        d.setUTCHours(floorInBase(d.getUTCHours(), tickSize));
                    if (unit == "month")
                        d.setUTCMonth(floorInBase(d.getUTCMonth(), tickSize));
                    if (unit == "year")
                        d.setUTCFullYear(floorInBase(d.getUTCFullYear(), tickSize));

                    // reset smaller components
                    d.setUTCMilliseconds(0);
                    if (step >= timeUnitSize.minute)
                        d.setUTCSeconds(0);
                    if (step >= timeUnitSize.hour)
                        d.setUTCMinutes(0);
                    if (step >= timeUnitSize.day)
                        d.setUTCHours(0);
                    if (step >= timeUnitSize.day * 4)
                        d.setUTCDate(1);
                    if (step >= timeUnitSize.year)
                        d.setUTCMonth(0);


                    var carry = 0, v = Number.NaN, prev;
                    do {
                        prev = v;
                        v = d.getTime();
                        ticks.push(v);
                        if (unit == "month") {
                            if (tickSize < 1) {
                                // a bit complicated - we'll divide the month
                                // up but we need to take care of fractions
                                // so we don't end up in the middle of a day
                                d.setUTCDate(1);
                                var start = d.getTime();
                                d.setUTCMonth(d.getUTCMonth() + 1);
                                var end = d.getTime();
                                d.setTime(v + carry * timeUnitSize.hour + (end - start) * tickSize);
                                carry = d.getUTCHours();
                                d.setUTCHours(0);
                            }
                            else
                                d.setUTCMonth(d.getUTCMonth() + tickSize);
                        }
                        else if (unit == "year") {
                            d.setUTCFullYear(d.getUTCFullYear() + tickSize);
                        }
                        else
                            d.setTime(v + step);
                    } while (v < axis.max && v != prev);
                    return ticks;
                };

                formatter = function (v, axis) {
                    var d = new Date(v);

                    // first check global format
                    if (opts.timeformat != null)
                        return $.plot.formatDate(d, opts.timeformat, opts.monthNames);

                    var t = axis.tickSize[0] * timeUnitSize[axis.tickSize[1]];
                    var span = axis.max - axis.min;
                    var suffix = (opts.twelveHourClock) ? " %p" : "";

                    if (t < timeUnitSize.minute)
                        fmt = "%h:%M:%S" + suffix;
                    else if (t < timeUnitSize.day) {
                        if (span < 2 * timeUnitSize.day)
                            fmt = "%h:%M" + suffix;
                        else
                            fmt = "%b %d %h:%M" + suffix;
                    }
                    else if (t < timeUnitSize.month)
                        fmt = "%b %d";
                    else if (t < timeUnitSize.year) {
                        if (span < timeUnitSize.year)
                            fmt = "%b";
                        else
                            fmt = "%b %y";
                    }
                    else
                        fmt = "%y";

                    return $.plot.formatDate(d, fmt, opts.monthNames);
                };
            }
            else {
                // pretty rounding of base-10 numbers
                var maxDec = opts.tickDecimals;
                var dec = -Math.floor(Math.log(delta) / Math.LN10);
                if (maxDec != null && dec > maxDec)
                    dec = maxDec;

                magn = Math.pow(10, -dec);
                norm = delta / magn; // norm is between 1.0 and 10.0

                if (norm < 1.5)
                    size = 1;
                else if (norm < 3) {
                    size = 2;
                    // special case for 2.5, requires an extra decimal
                    if (norm > 2.25 && (maxDec == null || dec + 1 <= maxDec)) {
                        size = 2.5;
                        ++dec;
                    }
                }
                else if (norm < 7.5)
                    size = 5;
                else
                    size = 10;

                if(options.xaxis.magn != null) magn = magn*options.xaxis.magn;
                size *= magn;
                if (opts.minTickSize != null && size < opts.minTickSize)
                    size = opts.minTickSize;

                axis.tickDecimals = Math.max(0, maxDec != null ? maxDec : dec);
                axis.tickSize = opts.tickSize || size;

                generator = function (axis) {
                    var ticks = [];

                    // spew out all possible ticks
                    var start = floorInBase(axis.min, axis.tickSize),
                        i = 0, v = Number.NaN, prev;
                    do {
                        prev = v;
                        v = start + i * axis.tickSize;
                        ticks.push(v);
                        ++i;
                    } while (v < axis.max && v != prev);
                    return ticks;
                };

                formatter = function (v, axis) {
                    var units = "";
                    if(v < 1){
                        v = v.toFixed(3);
                    }else if(v < 1000){//leave number as is
                        v = v.toFixed(axis.tickDecimals);
                    }else if(v < 1000000){//format number to ---K
                        v = v/1000;
                        units = ' K';
                    }else if(v < 1000000000){//format number to ---M
                        v = v/1000000;
                        units = ' M';
                    }else{//format numbers to ---B
                        v = v/1000000000;
                        units = " B";
                    }
                    v = Math.round(v*1000)/1000;
                    return v + units;
                };
            }

            if (opts.alignTicksWithAxis != null) {
                var otherAxis = (axis.direction == "x" ? xaxes : yaxes)[opts.alignTicksWithAxis - 1];
                if (otherAxis && otherAxis.used && otherAxis != axis) {
                    // consider snapping min/max to outermost nice ticks
                    var niceTicks = generator(axis);
                    if (niceTicks.length > 0) {
                        if (opts.min == null)
                            axis.min = Math.min(axis.min, niceTicks[0]);
                        if (opts.max == null && niceTicks.length > 1)
                            axis.max = Math.max(axis.max, niceTicks[niceTicks.length - 1]);
                    }

                    generator = function (axis) {
                        // copy ticks, scaled to this axis
                        var ticks = [], v, i;
                        for (i = 0; i < otherAxis.ticks.length; ++i) {
                            v = (otherAxis.ticks[i].v - otherAxis.min) / (otherAxis.max - otherAxis.min);
                            v = axis.min + v * (axis.max - axis.min);
                            ticks.push(v);
                        }
                        return ticks;
                    };

                    // we might need an extra decimal since forced
                    // ticks don't necessarily fit naturally
                    if (axis.mode != "time" && opts.tickDecimals == null) {
                        var extraDec = Math.max(0, -Math.floor(Math.log(delta) / Math.LN10) + 1),
                            ts = generator(axis);

                        // only proceed if the tick interval rounded
                        // with an extra decimal doesn't give us a
                        // zero at end
                        if (!(ts.length > 1 && /\..*0$/.test((ts[1] - ts[0]).toFixed(extraDec))))
                            axis.tickDecimals = extraDec;
                    }
                }
            }

            axis.tickGenerator = generator;
            if ($.isFunction(opts.tickFormatter))
                axis.tickFormatter = function (v, axis) { return "" + opts.tickFormatter(v, axis); };
            else
                axis.tickFormatter = formatter;
        }

        function setTicks(axis) {

            if(axis.options.logarithmicScale && oticks == axis.options.ticks){
                    axis.options.ticks = function (v, axis) {
                    var ticks = [];
                    if(v.max >= 1)
                    var increments = Math.log(v.max)/10;
                    else{
                        v.max = 1;
                        ticks.push(.2);
                        ticks.push(.45);
                        ticks.push(.7);
                        ticks.push(.9);
                        var increments = v.max/10;

                    }

                    if(axis.options.max != undefined) var limit = axis.options.max;
                    else var limit = v.max*4;

                    if(v.max != null){
                    for(var max = 0; max <= limit && max >= 0; max++){
                        var number = (max*axis.options.autoscaleMargin*30).toPrecision(3);
                        var length = (1+Math.floor(Math.log(number)/Math.log(10)));
                        number = number/Math.pow(10,length-1);
                        number = (Math.round(number * 4) / 4).toFixed(2)*Math.pow(10,length);

                        ticks.push(number);
                        max = max*10;
                        if(increments > 0) max = max + increments;
                    }
                            ticks.push(0);
                }
                    if(axis.options.max != undefined)
                    ticks.push(axis.options.max);
                    return ticks;
                };

                /*
                  axis.options.ticks = function (v, axis) {
                    var ticks = [];
                    var log = Math.log(v.max);
                    if(axis.options.max != undefined) var limit = axis.options.max;
                    else var limit = v.max*log*4;

                    var num = log;
                    ticks.push(0);
                    ticks.push(num);
                    if(log > 1){
                    for(var max = 0; num <= limit; max++){
                        var num = num*log;
                        ticks.push(num);
                    }
                    }
                 */

            }

            var oticks = axis.options.ticks, ticks = [];

            if (oticks == null || (typeof oticks == "number" && oticks > 0)){
               //alert(axis.innermost);
                //if(axis.innermost || axis.datamin != null)

                ticks = axis.tickGenerator(axis);
            }
            else if (oticks) {
                if ($.isFunction(oticks)){
                    // generate the ticks
                    ticks = oticks({ min: axis.datamin, max: axis.datamax }, axis);
                }
                else{
                    ticks = oticks;
                }
            }

            // clean up/labelify the supplied ticks, copy them over
            var i, v;
            axis.ticks = [];

            for (i = 0; i < ticks.length; ++i) {
                var label = null;
                var t = ticks[i];
                if (typeof t == "object") {
                    v = +t[0];
                    if (t.length > 1)
                        label = t[1];
                }
                else{
                    v = +t;
                }
                if (label == null)
                    label = axis.tickFormatter(v, axis);
                if (!isNaN(v))
                    axis.ticks.push({ v: v, label: label });
            }

        }

        function snapRangeToTicks(axis, ticks) {
            if (axis.options.autoscaleMargin && ticks.length > 0) {
                // snap to ticks
                if (axis.options.min == null)
                    axis.min = Math.min(axis.min, ticks[0].v);
                if (axis.options.max == null && ticks.length > 1)
                    axis.max = Math.max(axis.max, ticks[ticks.length - 1].v);
            }
        }

        function draw() {
            ctx.clearRect(0, 0, canvasWidth, canvasHeight);
            var grid = options.grid;

            // draw background, if any
            if (grid.show && grid.backgroundColor)
                drawBackground();

            if (grid.show && !grid.aboveData)
                drawGrid();



            for (var i = 0; i < series.length; ++i) {
                executeHooks(hooks.drawSeries, [ctx, series[options.seriesOrder[i]]]);
                drawSeries(series[options.seriesOrder[i]], options.seriesOrder[i]);
            }

            executeHooks(hooks.draw, [ctx]);

            if (grid.show && grid.aboveData)
                drawGrid();
        }

        function extractRange(ranges, coord) {
            var axis, from, to, key, axes = allAxes();

            for (i = 0; i < axes.length; ++i) {
                axis = axes[i];
                if (axis.direction == coord) {
                    key = coord + axis.n + "axis";
                    if (!ranges[key] && axis.n == 1)
                        key = coord + "axis"; // support x1axis as xaxis
                    if (ranges[key]) {
                        from = ranges[key].from;
                        to = ranges[key].to;
                        break;
                    }
                }
            }

            // backwards-compat stuff - to be removed in future
            if (!ranges[key]) {
                axis = coord == "x" ? xaxes[0] : yaxes[0];
                from = ranges[coord + "1"];
                to = ranges[coord + "2"];
            }

            // auto-reverse as an added bonus
            if (from != null && to != null && from > to) {
                var tmp = from;
                from = to;
                to = tmp;
            }

            return { from: from, to: to, axis: axis };
        }

        function drawBackground() {
            ctx.save();
            ctx.translate(plotOffset.left, plotOffset.top);

            ctx.fillStyle = getColorOrGradient(options.grid.backgroundColor, plotHeight, 0, "rgba(255, 255, 255, 0)");
            ctx.fillRect(0, 0, plotWidth, plotHeight);
            ctx.restore();
        }

        function drawGrid() {
            var i;

            ctx.save();
            ctx.translate(plotOffset.left, plotOffset.top);

            // draw markings
            var markings = options.grid.markings;
            if (markings) {
                if ($.isFunction(markings)) {
                    var axes = plot.getAxes();
                    // xmin etc. is backwards compatibility, to be
                    // removed in the future
                    axes.xmin = axes.xaxis.min;
                    axes.xmax = axes.xaxis.max;
                    axes.ymin = axes.yaxis.min;
                    axes.ymax = axes.yaxis.max;

                    markings = markings(axes);
                }

                for (i = 0; i < markings.length; ++i) {
                    var m = markings[i],
                        xrange = extractRange(m, "x"),
                        yrange = extractRange(m, "y");

                    // fill in missing
                    if (xrange.from == null)
                        xrange.from = xrange.axis.min;
                    if (xrange.to == null)
                        xrange.to = xrange.axis.max;
                    if (yrange.from == null)
                        yrange.from = yrange.axis.min;
                    if (yrange.to == null)
                        yrange.to = yrange.axis.max;

                    // clip
                    if (xrange.to < xrange.axis.min || xrange.from > xrange.axis.max ||
                        yrange.to < yrange.axis.min || yrange.from > yrange.axis.max)
                        continue;

                    xrange.from = Math.max(xrange.from, xrange.axis.min);
                    xrange.to = Math.min(xrange.to, xrange.axis.max);
                    yrange.from = Math.max(yrange.from, yrange.axis.min);
                    yrange.to = Math.min(yrange.to, yrange.axis.max);

                    if (xrange.from == xrange.to && yrange.from == yrange.to)
                        continue;

                    // then draw
                    xrange.from = xrange.axis.p2c(xrange.from);
                    xrange.to = xrange.axis.p2c(xrange.to);
                    yrange.from = yrange.axis.p2c(yrange.from);
                    yrange.to = yrange.axis.p2c(yrange.to);

                    if (xrange.from == xrange.to || yrange.from == yrange.to) {
                        // draw line
                        ctx.beginPath();
                        ctx.strokeStyle = m.color || options.grid.markingsColor;
                        ctx.lineWidth = m.lineWidth || options.grid.markingsLineWidth;
                        ctx.moveTo(xrange.from, yrange.from);
                        ctx.lineTo(xrange.to, yrange.to);
                        ctx.stroke();
                    }
                    else {
                        // fill area
                        ctx.fillStyle = m.color || options.grid.markingsColor;
                        ctx.fillRect(xrange.from, yrange.to,
                                     xrange.to - xrange.from,
                                     yrange.from - yrange.to);
                    }
                }
            }

            //draw watermark
        if (options.series.watermark != undefined && options.series.watermark.show != undefined && options.series.watermark.show != false ){
            var img = new Image();
            img.src = '/dash/images/bg_logomark_transparent-small.png';
            var x1, y1, x2, y2;
            var maxScale = canvasWidth*.8;
                if(options.series.watermark.width == undefined){
                    x2 = img.width;
                    if(maxScale < img.width) x2 = maxScale; else x2 = img.width;
                    y2 = img.height;
                }else{
                    if(maxScale < options.series.watermark.width) x2 = maxScale; else x2 = options.series.watermark.width;
                    y2 = x2/img.width * img.height;
                }
                if(options.series.watermark.xposition == undefined || options.series.watermark.xposition == 'center'){
                    x1 = (canvasWidth)/2 - x2/2 - plotOffset.left;
                }
                if(options.series.watermark.yposition == undefined || options.series.watermark.yposition == 'center'){
                    y1 = (canvasHeight)/2 - 10 - y2/2 - plotOffset.top;
                }
                if(options.series.watermark.yoffset != undefined){
                    y1 += options.series.watermark.yoffset;
                }
                if(options.series.watermark.xoffset != undefined){
                    x1 += options.series.watermark.xoffset;
                }

            // actually we should check img.complete, but it
            // appears to be a somewhat unreliable indicator in
            // IE6 (false even after load event)

if(canvas.excanvas != true){
            tmp = ctx.globalAlpha;
            ctx.globalAlpha *= options.series.watermark.alpha;
            ctx.drawImage(img, x1, y1, x2, y2);
            ctx.globalAlpha = tmp;
}

     }


            var axes = allAxes(), bw = options.grid.borderWidth;


            // draw border
            if (bw) {
                ctx.lineWidth = bw;
                ctx.strokeStyle = "#ffffff";
                ctx.strokeRect(-parseInt(bw/2)+1, -parseInt(bw/2)+1, plotWidth + parseInt(bw), parseInt(plotHeight + bw));
                ctx.strokeStyle = options.grid.borderColor;
                ctx.strokeRect(-parseInt(bw/2), -parseInt(bw/2), plotWidth + parseInt(bw), parseInt(plotHeight + bw));
            }


            // draw the ticks
            for (var j = 0; j < axes.length; ++j) {
                var axis = axes[j], box = axis.box,
                    t = axis.tickLength, x, y, xoff, yoff;
                if (!axis.show || axis.ticks.length == 0)
                    continue;

                ctx.strokeStyle = axis.options.tickColor || $.color.parse(axis.options.color).scale('a', 0.22).toString();
                ctx.lineWidth = .5;

                // find the edges
                if (axis.direction == "x") {
                    x = 0;
                    if (t == "full")
                        y = (axis.position == "top" ? 0 : plotHeight);
                    else
                        y = box.top - plotOffset.top + (axis.position == "top" ? box.height : 0);
                }
                else {
                    y = 0;
                    if (t == "full")
                        x = (axis.position == "left" ? 0 : plotWidth);
                    else
                        x = box.left - plotOffset.left + (axis.position == "left" ? box.width : 0);
                }

                // draw tick bar
                if (!axis.innermost) {
                    ctx.beginPath();
                    xoff = yoff = 0;
                    if (axis.direction == "x")
                        xoff = plotWidth;
                    else
                        yoff = plotHeight;

                    if (ctx.lineWidth == 1) {
                        x = Math.floor(x) + 0.5;
                        y = Math.floor(y) + 0.5;
                    }
					x = parseInt(x)+2;
                    ctx.moveTo(x, y);
                    ctx.lineTo(x + xoff, y + yoff);
                    ctx.stroke();
                }

                // draw ticks
                ctx.beginPath();

                for (i = 0; i < axis.ticks.length; ++i) {
                    var v = axis.ticks[i].v;
                    xoffstart = 0;
                    xoff = yoff = 0;
					//if(t == "full") continue;
                    if (v < axis.min || v > axis.max
                        // skip those lying on the axes if we got a border
                        || (t == "full" && bw > 0
                            && (v == axis.min || v == axis.max)))
                        continue;

                    if (axis.direction == "x") {

                        x = axis.p2c(v);
                        yoff = t == "full" ? -plotHeight : t;

                        if (axis.position == "top")
                            yoff = -yoff;
                    }
                    else {

                        y = axis.p2c(v);
                        if(t == "full"){
                        xoff = -plotWidth;
                        xoffstart = -4;
                        }else{
                        	 xoff = t;
                        	 xoffstart = 0;
                    	}

                        if (axis.position == "left")
                            xoff = -xoff;
                    }

                    if (ctx.lineWidth == 1) {
                        if (axis.direction == "x")
                            x = Math.floor(x) + 0.5;
                        else
                            y = Math.floor(y) + 0.5;
                    }

                    ctx.moveTo(x+xoffstart, y);
                    ctx.lineTo(x + xoff, y + yoff);
                }
                ctx.stroke();

            }



            ctx.restore();
        }

        function insertAxisLabels() {
            placeholder.find(".tickLabels").remove();

            var html = ['<div class="tickLabels" style="font-size:smaller">'];

            var axes = allAxes();
            for (var j = 0; j < axes.length; ++j) {
                var axis = axes[j], box = axis.box;
                if (!axis.show)
                    continue;
                //debug: html.push('<div style="position:absolute;opacity:0.10;background-color:red;left:' + box.left + 'px;top:' + box.top + 'px;width:' + box.width +  'px;height:' + box.height + 'px"></div>')
                html.push('<div class="' + axis.direction + 'Axis ' + axis.direction + axis.n + 'Axis" style="color:' + axis.options.color + '">');
                var legendHighlight = false;
                for (var i = 0; i < axis.ticks.length; ++i) {

                    var tick = axis.ticks[i];
                    if (!tick.label || tick.v < axis.min || tick.v > axis.max)
                        continue;

                    var pos = {}, align;

                    if (axis.direction == "x") {
                        align = "center";
                        pos.left = Math.round(plotOffset.left + axis.p2c(tick.v) - axis.labelWidth/2);
                        if (axis.position == "bottom")
                            pos.top = box.top + box.padding;
                        else
                            pos.bottom = canvasHeight - (box.top + box.height - box.padding);
                    }
                    else {
                        pos.top = Math.round(plotOffset.top + axis.p2c(tick.v) - axis.labelHeight/2);
                        if (axis.position == "left") {
                            pos.right = canvasWidth - (box.left + box.width - box.padding)
                            align = "right";
                        }
                        else {
                            pos.left = box.left + box.padding;
                            align = "left";
                        }
                    }

                    pos.width = axis.labelWidth;

                    var style = ["position:absolute", "text-align:" + align];
                    for (var a in pos)
                        style.push(a + ":" + pos[a] + "px")

                    if(axis.direction == 'y' && legendHighlight == false){
                        var styleHighlight = '';
                        for (var a in pos)
                        styleHighlight += a + ":" + pos[a] + "px;";
                        html.push('<div class="tick_highlight" style="'+styleHighlight+'"></div>');
                        legendHighlight = true;
                    }

                    html.push('<div class="tickLabel" style="' + style.join(';') + '">' + tick.label + '</div>');

                }
                html.push('</div>');
            }

            html.push('</div>');

            placeholder.append(html.join(""));
        }

        function drawSeries(series, seriesnum) {

            if (series.lines.show)
                drawSeriesLines(series, seriesnum);
            if (series.bars.show)
                drawSeriesBars(series);
            if (series.points.show)
                drawSeriesPoints(series);
                ctx.restore();
        }

        function drawSeriesLines(series, seriesnum) {
            function plotLine(datapoints, xoffset, yoffset, axisx, axisy) {
                var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
                var points = datapoints.points,
                    ps = datapoints.pointsize,
                    prevx = null, prevy = null;

                ctx.beginPath();
                for (var i = ps; i < points.length; i += ps) {
                    var x1 = points[i - ps], y1 = points[i - ps + 1],
                        x2 = points[i], y2 = points[i + 1];

                    if (x1 == null || x2 == null)
                        continue;

                    // clip with ymin
                    if (y1 <= y2 && y1 < axisy.min) {
                        if (y2 < axisy.min)
                            continue;   // line segment is outside
                        // compute new intersection point
                        x1 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.min;
                    }
                    else if (y2 <= y1 && y2 < axisy.min) {
                        if (y1 < axisy.min)
                            continue;
                        x2 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.min;
                    }

                    // clip with ymax
                    if (y1 >= y2 && y1 > axisy.max) {
                        if (y2 > axisy.max)
                            continue;
                        x1 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.max;
                    }
                    else if (y2 >= y1 && y2 > axisy.max) {
                        if (y1 > axisy.max)
                            continue;
                        x2 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.max;
                    }

                    // clip with xmin
                    if (x1 <= x2 && x1 < axisx.min) {

                        if (x2 < axisx.min)
                            continue;
                        //TODO: Make special case for logarithmic points
                        y1 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = axisx.min;

                    }
                    else if (x2 <= x1 && x2 < axisx.min) {
                        if (x1 < axisx.min)
                            continue;
                        y2 =  (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = axisx.min;
                    }

                    // clip with xmax
                    if (x1 >= x2 && x1 > xmax) {
                        if (x2 > xmax)
                            continue;
                        y1 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = xmax;
                    }
                    else if (x2 >= x1 && x2 > xmax) {
                        if (x1 > xmax)
                            continue;
                        y2 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = xmax;
                    }

                    if (x1 != prevx || y1 != prevy)
                        ctx.moveTo(axisx.p2c(x1) + xoffset, axisy.p2c(y1) + yoffset);

                    prevx = x2;
                    prevy = y2;




                    ctx.lineTo(axisx.p2c(x2) + xoffset, axisy.p2c(y2) + yoffset);


                     /*
                     if(prevx != null){
                     if((currentx - prevx) > pointIntervals	){
                     //draw line from prevx,prevy to (nullLine = prevx+pointIntervals+),0
                     prevx = nullLine
                     prevy = 0
                     while(nullLine < currentx)  nullLine += pointIntervals
                     	if( nullLine > xbarrier){ nullLine = xbarrier; break;}

                     }
                     draw line from prevx, prevy to nullLine, 0
                     prevx = nullLine
                     prevy = 0




                     }



                     */
                }
                if(xmax > options.barrier_limits.xaxis.last_date*1000) options.series.animate.start = 1;
                ctx.stroke();
            }

          function plotLineWithDashes(datapoints, xoffset, yoffset, axisx, axisy, color, seriesnum) {
              var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
            if(color != undefined) ctx.strokeStyle = color;
                var points = datapoints.points,
                    ps = datapoints.pointsize,
                    prevx = null, prevy = null;

                ctx.beginPath();

                //Draw dashed line from first point in series to current last point
                var x1 = points[0], y1 = points[1];//,
                    x2 = points[0], y2 = points[0 + 1];
                    /*
                    if(x1 > xmax){
                        x1 = xmax;
                    }
                    */

                var y1c = axisy.p2c(y1);
                var x1c = axisx.p2c(x1);

                if(x1 != null && y1 != null){
                    var xstart_real = axisx.p2c(options.barrier_limits[seriesnum].first.date+"000");
                    if(xstart_real != -1 && x1c >= 0){
                        //alert(y1);
                        if(xstart_real < 0) xstart = 0;
                        else{
                            if(xstart_real < axisx.p2c(xmax)){
                            ctx.moveTo(xstart_real, y1c-15);
                            ctx.lineTo(xstart_real, y1c+15);
                            }
                            xstart = xstart_real;
                        }
                        ctx.dashedLine(x1c, y1c, xstart, y1c);
                        //ctx.dashedLine(xstart, axisy.p2c(axisy.min), axisx.p2c(x1), axisy.p2c(y1));
                    }
                }


                /*
                //Draw vertical line on starting point in series
                var x1 = points[0], y1 = points[1];//,
                    x2 = points[0], y2 = points[0 + 1];
                if(x1 != null && y1 != null){
                    xstart = axisx.p2c(options.barrier_limits.xaxis.first_date+"000")-1;
                    if(xstart != -1){
                        //alert(y1);
                        if(xstart < 0) xstart = 0;
                        ctx.dashedLine(xstart, axisy.p2c(y1), axisx.p2c(x1), axisy.p2c(y1));
                        //ctx.dashedLine(xstart, axisy.p2c(axisy.min), axisx.p2c(x1), axisy.p2c(y1));
                    }
                }
                */
                for (var i = ps; i < points.length; i += ps) {
                    var x1 = points[i - ps], y1 = points[i - ps + 1],
                        x2 = points[i], y2 = points[i + 1];

                    if (x1 == null || x2 == null)
                        continue;

                    // clip with ymin
                    if (y1 <= y2 && y1 < axisy.min) {
                        if (y2 < axisy.min)
                            continue;   // line segment is outside
                        // compute new intersection point
                        x1 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.min;
                    }
                    else if (y2 <= y1 && y2 < axisy.min) {
                        if (y1 < axisy.min)
                            continue;
                        x2 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.min;
                    }

                    // clip with ymax
                    if (y1 >= y2 && y1 > axisy.max) {
                        if (y2 > axisy.max)
                            continue;
                        x1 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.max;
                    }
                    else if (y2 >= y1 && y2 > axisy.max) {
                        if (y1 > axisy.max)
                            continue;
                        x2 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.max;
                    }

                    // clip with xmin
                    if (x1 <= x2 && x1 < axisx.min) {
                        //if (x2 < axisx.min)
                            //continue;
                        y1 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = axisx.min;

                    }
                    else if (x2 <= x1 && x2 < axisx.min) {
                        if (x1 < axisx.min)
                            continue;
                        y2 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = axisx.min;
                    }

                    // clip with xmax
                    if (x1 >= x2 && x1 > xmax) {
                        if (x2 > xmax)
                            continue;
                        y1 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = xmax;
                    }
                    else if (x2 >= x1 && x2 > xmax) {
                        if (x1 > xmax)
                            continue;
                        y2 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = xmax;
                    }


                    prevx = x2;
                    prevy = y2;

                }

                var prevyc = axisy.p2c(prevy);
                var prevxc = axisx.p2c(prevx);
                //alert(prevxc + " <= " + axisx.p2c(xmax));
                //NO DATA DASHES
                if(prevx != null){
                    //ctx.dashedLine(axisx.p2c(prevx), axisy.p2c(prevy), axisx.p2c(prevx)+1, axisy.p2c(axisy.min));
                    var xend = options.barrier_limits[seriesnum].last.date+"000";
                    if(xend > xmax){
                        xend = xmax;
                        var xendc = axisx.p2c(xend);
                    }else{
                        var xendc = axisx.p2c(xend);
                        ctx.moveTo(xendc, prevyc-15);
                        ctx.lineTo(xendc, prevyc+15);
                    }



                    //alert(xend + " = " + options.barrier_limits[seriesnum].last.date+"000");
                    //console.log(prevxc, prevyc, xendc, prevyc);
                    ctx.dashedLine(prevxc, prevyc, xendc, prevyc);

                }
                ctx.stroke();

            }

            function plotLineArea(datapoints, axisx, axisy) {
                var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
                var points = datapoints.points,
                    ps = datapoints.pointsize,
                    bottom = Math.min(Math.max(0, axisy.min), axisy.max),
                    i = 0, top, areaOpen = false,
                    ypos = 1, segmentStart = 0, segmentEnd = 0;

                // we process each segment in two turns, first forward
                // direction to sketch out top, then once we hit the
                // end we go backwards to sketch the bottom
                while (true) {
                    if (ps > 0 && i > points.length + ps)
                        break;

                    i += ps; // ps is negative if going backwards

                    var x1 = points[i - ps],
                        y1 = points[i - ps + ypos],
                        x2 = points[i], y2 = points[i + ypos];

                    if (areaOpen) {
                        if (ps > 0 && x1 != null && x2 == null) {
                            // at turning point
                            segmentEnd = i;
                            ps = -ps;
                            ypos = 2;
                            continue;
                        }

                        if (ps < 0 && i == segmentStart + ps) {
                            // done with the reverse sweep
                            ctx.fill();
                            areaOpen = false;
                            ps = -ps;
                            ypos = 1;
                            i = segmentStart = segmentEnd + ps;
                            continue;
                        }
                    }

                    if (x1 == null || x2 == null)
                        continue;

                    // clip x values

                    // clip with xmin
                    if (x1 <= x2 && x1 < axisx.min) {
                        if (x2 < axisx.min)
                            continue;
                        y1 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = axisx.min;
                    }
                    else if (x2 <= x1 && x2 < axisx.min) {
                        if (x1 < axisx.min)
                            continue;
                        y2 = (axisx.min - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = axisx.min;
                    }

                    // clip with xmax
                    if (x1 >= x2 && x1 > xmax) {
                        if (x2 > xmax)
                            continue;
                        y1 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x1 = xmax;
                    }
                    else if (x2 >= x1 && x2 > xmax) {
                        if (x1 > xmax)
                            continue;
                        y2 = (xmax - x1) / (x2 - x1) * (y2 - y1) + y1;
                        x2 = xmax;
                    }

                    if (!areaOpen) {
                        // open area
                        ctx.beginPath();
                        ctx.moveTo(axisx.p2c(x1), axisy.p2c(bottom));
                        areaOpen = true;
                    }

                    // now first check the case where both is outside
                    if (y1 >= axisy.max && y2 >= axisy.max) {
                        ctx.lineTo(axisx.p2c(x1), axisy.p2c(axisy.max));
                        ctx.lineTo(axisx.p2c(x2), axisy.p2c(axisy.max));
                        continue;
                    }
                    else if (y1 <= axisy.min && y2 <= axisy.min) {
                        ctx.lineTo(axisx.p2c(x1), axisy.p2c(axisy.min));
                        ctx.lineTo(axisx.p2c(x2), axisy.p2c(axisy.min));
                        continue;
                    }

                    // else it's a bit more complicated, there might
                    // be a flat maxed out rectangle first, then a
                    // triangular cutout or reverse; to find these
                    // keep track of the current x values
                    var x1old = x1, x2old = x2;

                    // clip the y values, without shortcutting, we
                    // go through all cases in turn

                    // clip with ymin
                    if (y1 <= y2 && y1 < axisy.min && y2 >= axisy.min) {
                        x1 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.min;
                    }
                    else if (y2 <= y1 && y2 < axisy.min && y1 >= axisy.min) {
                        x2 = (axisy.min - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.min;
                    }

                    // clip with ymax
                    if (y1 >= y2 && y1 > axisy.max && y2 <= axisy.max) {
                        x1 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y1 = axisy.max;
                    }
                    else if (y2 >= y1 && y2 > axisy.max && y1 <= axisy.max) {
                        x2 = (axisy.max - y1) / (y2 - y1) * (x2 - x1) + x1;
                        y2 = axisy.max;
                    }

                    // if the x value was changed we got a rectangle
                    // to fill
                    if (x1 != x1old) {
                        ctx.lineTo(axisx.p2c(x1old), axisy.p2c(y1));
                        // it goes to (x1, y1), but we fill that below
                    }

                    // fill triangular section, this sometimes result
                    // in redundant points if (x1, y1) hasn't changed
                    // from previous line to, but we just ignore that
                    ctx.lineTo(axisx.p2c(x1), axisy.p2c(y1));
                    ctx.lineTo(axisx.p2c(x2), axisy.p2c(y2));

                    // fill the other rectangle if it's there
                    if (x2 != x2old) {
                        ctx.lineTo(axisx.p2c(x2), axisy.p2c(y2));
                        ctx.lineTo(axisx.p2c(x2old), axisy.p2c(y2));
                    }
                }
            }

            ctx.save();
            ctx.translate(plotOffset.left, plotOffset.top);

            ctx.lineJoin = "round";

            var lw = series.lines.lineWidth,
                sw = series.shadowSize;
            // FIXME: consider another form of shadow when filling is turned on
            if (lw > 0 && sw > 0) {
                // draw shadow as a thick and thin line with transparency
                ctx.lineWidth = sw;
                ctx.strokeStyle = "rgba(0,0,0,"+.1*series.opacity+")";
                // position shadow at angle from the mid of line
                var angle = Math.PI/18;
                //plotLine(series.datapoints, Math.sin(angle) * (lw/2 + sw/2), Math.cos(angle) * (lw/2 + sw/2), series.xaxis, series.yaxis);
                ctx.lineWidth = sw/2;
                plotLine(series.datapoints, Math.sin(angle) * (lw/2 + sw/4), Math.cos(angle) * (lw/2 + sw/4), series.xaxis, series.yaxis);
                plotLineWithDashes(series.datapoints, Math.sin(angle) * (lw/2 + sw/4), Math.cos(angle) * (lw/2 + sw/4), series.xaxis, series.yaxis, null, seriesnum);
            }

            ctx.lineWidth = lw;
            ctx.strokeStyle = $.color.parse(series.color).scale('a', series.opacity).toString();
            var fillStyle = getFillStyle(series.lines, series.color, 0, plotHeight);
            if (fillStyle && series.yaxis.options.invert != true) {
                ctx.fillStyle = fillStyle;
                plotLineArea(series.datapoints, series.xaxis, series.yaxis);
            }

            if (lw > 0){
                plotLine(series.datapoints, 0, 0, series.xaxis, series.yaxis);

                //grayscale
                //plotLineWithDashes(series.datapoints, 0, 0, series.xaxis, series.yaxis, $.color.parse("#aaaaaa").scale('a', 0.19).toString(), seriesnum);
                //with color
                plotLineWithDashes(series.datapoints, 0, 0, series.xaxis, series.yaxis, $.color.parse(series.color).scale('a', 0.19).toString(), seriesnum);


           }
            ctx.restore();
        }

        function drawSeriesPoints(series) {
            function plotPoints(datapoints, radius, fillStyle, offset, shadow, axisx, axisy, symbol) {
                var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
                var points = datapoints.points, ps = datapoints.pointsize;

                for (var i = 0; i < points.length; i += ps) {

                    if(series.skipPoints != null && series.skipPoints[i] == true) continue;
                    var x = points[i], y = points[i + 1];
                    if (x == null || x < axisx.min || x > xmax || y < axisy.min || y > axisy.max)
                        continue;

                    ctx.beginPath();
                    x = axisx.p2c(x);
                    y = axisy.p2c(y) + offset;
                    if (symbol == "circle")
                        ctx.arc(x, y, radius, 0, shadow ? Math.PI : Math.PI * 2, false);
                    else
                        symbol(ctx, x, y, radius, shadow);
                    ctx.closePath();

                    if (fillStyle) {
                        ctx.fillStyle = fillStyle;
                        ctx.fill();
                    }
                    ctx.stroke();
                }
            }

            ctx.save();
            ctx.translate(plotOffset.left, plotOffset.top);



            var lw = series.points.lineWidth,
                sw = series.shadowSize,
                radius = series.points.radius,
                symbol = series.points.symbol;
            if (lw > 0 && sw > 0 && series.points.shadow) {
                // draw shadow in two steps
                var w = sw / 2;
                ctx.lineWidth = w;
                ctx.strokeStyle = "rgba(0,0,0,"+.1*series.opacity+")";
                plotPoints(series.datapoints, radius, null, w + w/2, true,
                           series.xaxis, series.yaxis, symbol);

                ctx.strokeStyle = "rgba(0,0,0,"+.2*series.opacity+")";
                plotPoints(series.datapoints, radius, null, w/2, true,
                           series.xaxis, series.yaxis, symbol);
            }

            ctx.lineWidth = lw;
            ctx.strokeStyle = $.color.parse(series.color).scale('a', series.opacity).toString();
            plotPoints(series.datapoints, radius,
                       getFillStyle(series.points, series.color), 0, false,
                       series.xaxis, series.yaxis, symbol);

        }

        function drawBar(x, y, b, barLeft, barRight, offset, fillStyleCallback, axisx, axisy, c, horizontal, lineWidth) {
            var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
            var left, right, bottom, top,
                drawLeft, drawRight, drawTop, drawBottom,
                tmp;

            // in horizontal mode, we start the bar from the left
            // instead of from the bottom so it appears to be
            // horizontal rather than vertical
            if (horizontal) {
                drawBottom = drawRight = drawTop = true;
                drawLeft = false;
                left = b;
                right = x;
                top = y + barLeft;
                bottom = y + barRight;

                // account for negative bars
                if (right < left) {
                    tmp = right;
                    right = left;
                    left = tmp;
                    drawLeft = true;
                    drawRight = false;
                }
            }
            else {
                drawLeft = drawRight = drawTop = true;
                drawBottom = false;
                left = x + barLeft;
                right = x + barRight;
                bottom = b;
                top = y;

                // account for negative bars
                if (top < bottom) {
                    tmp = top;
                    top = bottom;
                    bottom = tmp;
                    drawBottom = true;
                    drawTop = false;
                }
            }

            // clip
            if (right < axisx.min || left > xmax ||
                top < axisy.min || bottom > axisy.max)
                return;

            if (left < axisx.min) {
                left = axisx.min;
                drawLeft = false;
            }

            if (right > xmax) {
                right = xmax;
                drawRight = false;
            }

            if (bottom < axisy.min) {
                bottom = axisy.min;
                drawBottom = false;
            }

            if (top > axisy.max) {
                top = axisy.max;
                drawTop = false;
            }

            left = axisx.p2c(left);
            bottom = axisy.p2c(bottom);
            right = axisx.p2c(right);
            top = axisy.p2c(top);

            // fill the bar
            if (fillStyleCallback) {
                c.beginPath();
                c.moveTo(left, bottom);
                c.lineTo(left, top);
                c.lineTo(right, top);
                c.lineTo(right, bottom);
                c.fillStyle = fillStyleCallback(bottom, top);
                c.fill();
            }

            // draw outline
            if (lineWidth > 0 && (drawLeft || drawRight || drawTop || drawBottom)) {
                c.beginPath();

                // FIXME: inline moveTo is buggy with excanvas
                c.moveTo(left, bottom + offset);
                if (drawLeft)
                    c.lineTo(left, top + offset);
                else
                    c.moveTo(left, top + offset);
                if (drawTop)
                    c.lineTo(right, top + offset);
                else
                    c.moveTo(right, top + offset);
                if (drawRight)
                    c.lineTo(right, bottom + offset);
                else
                    c.moveTo(right, bottom + offset);
                if (drawBottom)
                    c.lineTo(left, bottom + offset);
                else
                    c.moveTo(left, bottom + offset);
                c.stroke();
            }
        }

        function drawSeriesBars(series) {
            function plotBars(datapoints, barLeft, barRight, offset, fillStyleCallback, axisx, axisy) {
                var xmax = axisx.min + (axisx.max-axisx.min)*options.series.animate.start;
                var points = datapoints.points, ps = datapoints.pointsize;

                for (var i = 0; i < points.length; i += ps) {
                    if (points[i] == null)
                        continue;
                    drawBar(points[i], points[i + 1], points[i + 2], barLeft, barRight, offset, fillStyleCallback, axisx, axisy, ctx, series.bars.horizontal, series.bars.lineWidth);
                }
            }

            ctx.save();
            ctx.translate(plotOffset.left, plotOffset.top);

            // FIXME: figure out a way to add shadows (for instance along the right edge)
            ctx.lineWidth = series.bars.lineWidth;
            ctx.strokeStyle = $.color.parse(series.color).scale('a', series.opacity).toString();
            var barLeft = series.bars.align == "left" ? 0 : -series.bars.barWidth/2;
            var fillStyleCallback = series.bars.fill ? function (bottom, top) { return getFillStyle(series.bars, series.color, bottom, top); } : null;
            plotBars(series.datapoints, barLeft, barLeft + series.bars.barWidth, 0, fillStyleCallback, series.xaxis, series.yaxis);
            ctx.restore();
        }

        function getFillStyle(filloptions, seriesColor, bottom, top) {
            var fill = filloptions.fill;
            if (!fill)
                return null;

            if (filloptions.fillColor)
                return getColorOrGradient(filloptions.fillColor, bottom, top, seriesColor);

            var c = $.color.parse(seriesColor);
            c.a = typeof fill == "number" ? fill : 0.4;
            c.normalize();
            return c.toString();
        }

        function insertLegend() {
            placeholder.find(".legend").remove();

            if (!options.legend.show)
                return;

            var fragments = [], rowStarted = false,
                lf = options.legend.labelFormatter, s, label;

            for (var i = 0; i < series.length; ++i) {
                s = series[i];
                label = s.label;

                metric_function = s.metric_function;
                if (!label)
                    continue;

                if (i % options.legend.noColumns == 0) {
                    //if (rowStarted)
                        //fragments.push('</div>');
                    //fragments.push('<div>');
                    rowStarted = true;
                }

                if (lf)
                    label = lf(label, s);
                 var metric_id = metric_function;
                //var description = "";
                //if(s.datetype != '') description = '<div class="description">'+s.datetype+' datapoints</div>';
                if(options.legend.showCheckboxes != false)
                fragments.push(
                    '<div class="legendColorBox legendHighlight explain_metric" id="'+ options.chartName + '_' + s.n + '" data-metric_id="' + metric_id + '"><div class="colorHolder" style="border: 1px solid '+s.color+';" ><div style="width:14px;height:13px;background-color:' + s.color + ';overflow:hidden" class="color-checked"><img style="vertical-align: top;" src="/dash/images/checkmark_transparent.png"/></div></div>' +
                    '' + label + '<div class="explain"></div></div>');
                else {

                fragments.push(
                    '<div class="deltaContainer"><div class="legendColorBox legendHighlight explain_metric" id="'+ options.chartName + '_' + s.n + '" style="border-bottom: 3px solid '+s.color+';" data-metric_id="' + metric_id + '">' +
                    '' + label + '<div class="explain"></div></div><div class="catchall"></div></div>');

                   }
            }
            if(options.series.lines.show && options.legend.showCheckboxes != false){
            fragments.push(
                    '<div class="legendColorBox"><div class="colorHolder"><div class="dataContinues"></div></div>' +
                    'data continues</div>');
            fragments.push(
                    '<div class="legendColorBox"><div class="colorHolder"><div class="dataEnds"></div></div>' +
                    'data ends</div>');
            }
            if (fragments.length == 0)
                return;

            var table = fragments.join("");
            if (options.legend.container != null)
                $(options.legend.container).html(table);
            else {
                var pos = "",
                    p = options.legend.position,
                    m = options.legend.margin;
                if (m[0] == null)
                    m = [m, m];
                if (p.charAt(0) == "n")
                    pos += 'top:' + (m[1] + plotOffset.top) + 'px;';
                else if (p.charAt(0) == "s")
                    pos += 'bottom:' + (m[1] + plotOffset.bottom) + 'px;';
                if (p.charAt(1) == "e")
                    pos += 'right:' + (m[0] + plotOffset.right) + 'px;';
                else if (p.charAt(1) == "w")
                    pos += 'left:' + (m[0] + plotOffset.left) + 'px;';
                var legend = $('<div class="legend">' + table.replace('style="', 'style="position:absolute;' + pos +';') + '</div>').appendTo(placeholder);
                if (options.legend.backgroundOpacity != 0.0) {
                    // put in the transparent background
                    // separately to avoid blended labels and
                    // label boxes
                    var c = options.legend.backgroundColor;
                    if (c == null) {
                        c = options.grid.backgroundColor;
                        if (c && typeof c == "string")
                            c = $.color.parse(c);
                        else
                            c = $.color.extract(legend, 'background-color');
                        c.a = 1;
                        c = c.toString();
                    }
                    var div = legend.children();
                    $('<div style="position:absolute;width:' + div.width() + 'px;height:' + div.height() + 'px;' + pos +'background-color:' + c + ';"> </div>').prependTo(legend).css('opacity', options.legend.backgroundOpacity);
                }
            }
            $('#'+options.chartName+'-legend .legendHighlight').live('mouseover mouseout', function(event){plot.highlightSeries(event, $(this).attr('id').replace(options.chartName+'_', ''))});
        }



        function insertLegendCompact() {
            placeholder.find(".legend").remove();

            if (!options.legend.show)
                return;

            var fragments = [], rowStarted = false,
                lf = options.legend.labelFormatter, s, label;
	         fragments.push('<table id="table_id">'
	        	+ '<tbody>');
            for (var i = 0; i < series.length; ++i) {
                s = series[i];
                label = s.label;
                metric_function = s.metric_function;
                if (!label)
                    continue;

                if (i % options.legend.noColumns == 0) {
                    rowStarted = true;
                }

                if (lf)
                    label = lf(label, s);

                 var metric_id = metric_function;

                if(options.legend.showCheckboxes != false)
                fragments.push(
                    '<td><div class="legendColorBox legendHighlight explain_metric" id="'+ options.chartName + '_' + s.n + '" data-metric_id="' + metric_id + '"><div class="colorHolder" style="border: 1px solid '+s.color+';" ><div style="width:14px;height:13px;background-color:' + s.color + ';overflow:hidden" class="color-checked"><img style="vertical-align: top;" src="/dash/images/checkmark_transparent.png"/></div></div>' +
                    '' + label + '<div class="explain"></div></div></td>');
                else {
                fragments.push('<tr>'
                	+ '<td><div class="legendColorBox legendColorBox-compact legendHighlight explain_metric" id="'
                			+ options.chartName + '_' + s.n
                			+ '" style="border-bottom: 5px solid '+s.color+';" data-metric_id="' + metric_id + '">'
        			+ label + '<div class="explain"></div>'
        			+ '</div></td>'
                	+ '</tr>');

        			}

                   }

            if(options.series.lines.show && options.legend.showCheckboxes != false){
            fragments.push(
                    '<div class="legendColorBox"><div class="colorHolder"><div class="dataContinues"></div></div>' +
                    'data continues</div>');
            fragments.push(
                    '<div class="legendColorBox"><div class="colorHolder"><div class="dataEnds"></div></div>' +
                    'data ends</div>');
            }
            fragments.push('</tbody>'
            	+ '</table>' );

            var table = fragments.join("");
            if (options.legend.container != null)
                $(options.legend.container).html(table);
            else {
                var pos = "",
                    p = options.legend.position,
                    m = options.legend.margin;
                if (m[0] == null)
                    m = [m, m];
                if (p.charAt(0) == "n")
                    pos += 'top:' + (m[1] + plotOffset.top) + 'px;';
                else if (p.charAt(0) == "s")
                    pos += 'bottom:' + (m[1] + plotOffset.bottom) + 'px;';
                if (p.charAt(1) == "e")
                    pos += 'right:' + (m[0] + plotOffset.right) + 'px;';
                else if (p.charAt(1) == "w")
                    pos += 'left:' + (m[0] + plotOffset.left) + 'px;';
                var legend = $('<div class="legend">' + table.replace('style="', 'style="position:absolute;' + pos +';') + '</div>').appendTo(placeholder);
                if (options.legend.backgroundOpacity != 0.0) {
                    // put in the transparent background
                    // separately to avoid blended labels and
                    // label boxes
                    var c = options.legend.backgroundColor;
                    if (c == null) {
                        c = options.grid.backgroundColor;
                        if (c && typeof c == "string")
                            c = $.color.parse(c);
                        else
                            c = $.color.extract(legend, 'background-color');
                        c.a = 1;
                        c = c.toString();
                    }
                    var div = legend.children();
                    $('<div style="position:absolute;width:' + div.width() + 'px;height:' + div.height() + 'px;' + pos +'background-color:' + c + ';"> </div>').prependTo(legend).css('opacity', options.legend.backgroundOpacity);
                }
            }
            $('#'+options.chartName+'-legend .legendHighlight').live('mouseover mouseout', function(event){plot.highlightSeries(event, $(this).attr('id').replace(options.chartName+'_', ''))});
        }








        // interactive features

        var highlights = [],
            redrawTimeout = null;

        // returns the data item the mouse is over, or null if none is found
        function findNearbyItem(mouseX, mouseY, seriesFilter) {
            var maxDistance = options.grid.mouseActiveRadius,
                smallestDistance = maxDistance * maxDistance + 1,
                item = null, foundPoint = false, i, j;

            for (i = series.length - 1; i >= 0; --i) {
                if (!seriesFilter(series[i]))
                    continue;

                var s = series[i],
                    axisx = s.xaxis,
                    axisy = s.yaxis,
                    points = s.datapoints.points,
                    ps = s.datapoints.pointsize,
                    mx = axisx.c2p(mouseX), // precompute some stuff to make the loop faster
                    my = axisy.c2p(mouseY),
                    maxx = maxDistance / axisx.scale,
                    maxy = maxDistance / axisy.scale;

                // with inverse transforms, we can't use the maxx/maxy
                // optimization, sadly
                if (axisx.options.inverseTransform)
                    maxx = Number.MAX_VALUE;
                if (axisy.options.inverseTransform)
                    maxy = Number.MAX_VALUE;

                if (s.lines.show || s.points.show) {
                    for (j = 0; j < points.length; j += ps) {
                        var x = points[j], y = points[j + 1];
                        if (x == null)
                            continue;

                        // For points and lines, the cursor must be within a
                        // certain distance to the data point
                        if (x - mx > maxx || x - mx < -maxx ||
                            y - my > maxy || y - my < -maxy)
                            continue;

                        // We have to calculate distances in pixels, not in
                        // data units, because the scales of the axes may be different
                        var dx = Math.abs(axisx.p2c(x) - mouseX),
                            dy = Math.abs(axisy.p2c(y) - mouseY),
                            dist = dx * dx + dy * dy; // we save the sqrt

                        // use <= to ensure last point takes precedence
                        // (last generally means on top of)
                        if (dist < smallestDistance) {
                            smallestDistance = dist;
                            item = [i, j / ps];
                        }
                    }
                }

                if (s.bars.show && !item) { // no other point can be nearby
                    var barLeft = s.bars.align == "left" ? 0 : -s.bars.barWidth/2,
                        barRight = barLeft + s.bars.barWidth;

                    for (j = 0; j < points.length; j += ps) {
                        var x = points[j], y = points[j + 1], b = points[j + 2];
                        if (x == null)
                            continue;

                        // for a bar graph, the cursor must be inside the bar
                        if (series[i].bars.horizontal ?
                            (mx <= Math.max(b, x) && mx >= Math.min(b, x) &&
                             my >= y + barLeft && my <= y + barRight) :
                            (mx >= x + barLeft && mx <= x + barRight &&
                             my >= Math.min(b, y) && my <= Math.max(b, y)))
                                item = [i, j / ps];
                    }
                }
            }

            if (item) {
                i = item[0];
                j = item[1];
                ps = series[i].datapoints.pointsize;

                return { datapoint: series[i].datapoints.points.slice(j * ps, (j + 1) * ps),
                         dataIndex: j,
                         series: series[i],
                         seriesIndex: i };
            }

            return null;
        }

        function onMouseMove(e) {
            if (options.grid.hoverable)
                triggerClickHoverEvent("plothover", e,
                                       function (s) { return s["hoverable"] != false; });
        }

        function onMouseLeave(e) {
            if (options.grid.hoverable)
                triggerClickHoverEvent("plothover", e,
                                       function (s) { return false; });
        }

        function onClick(e) {
            triggerClickHoverEvent("plotclick", e,
                                   function (s) { return s["clickable"] != false; });
        }

        // trigger click or hover event (they send the same parameters
        // so we share their code)
        function triggerClickHoverEvent(eventname, event, seriesFilter) {
            var offset = eventHolder.offset(),
                canvasX = event.pageX - offset.left - plotOffset.left,
                canvasY = event.pageY - offset.top - plotOffset.top,
            pos = canvasToAxisCoords({ left: canvasX, top: canvasY });

            pos.pageX = event.pageX;
            pos.pageY = event.pageY;

            var item = findNearbyItem(canvasX, canvasY, seriesFilter);

            if (item) {
                // fill in mouse pos for any listeners out there
                item.pageX = parseInt(item.series.xaxis.p2c(item.datapoint[0]) + offset.left + plotOffset.left);
                item.pageY = parseInt(item.series.yaxis.p2c(item.datapoint[1]) + offset.top + plotOffset.top);
            }

            if (options.grid.autoHighlight) {
                // clear auto-highlights
                for (var i = 0; i < highlights.length; ++i) {
                    var h = highlights[i];
                    if (h.auto == eventname &&
                        !(item && h.series == item.series &&
                          h.point[0] == item.datapoint[0] &&
                          h.point[1] == item.datapoint[1]))
                        unhighlight(h.series, h.point);
                }

                if (item){
                    for(var i = 0; i < series.length; i++){
                        series[i].opacity = .4;
                    }
                    highlight(item.series, item.datapoint, eventname);

                }else{
                    for(var i = 0; i < series.length; i++){
                        series[i].opacity = 1;
                    }
                }
            }

            placeholder.trigger(eventname, [ pos, item ]);
        }

        function triggerRedrawOverlay() {
            if (!redrawTimeout)
                redrawTimeout = setTimeout(drawOverlay, 30);
        }

        function drawOverlay() {
            redrawTimeout = null;

            // draw highlights
            octx.save();
            octx.clearRect(0, 0, canvasWidth, canvasHeight);
            octx.translate(plotOffset.left, plotOffset.top);

            var i, hi;
            for (i = 0; i < highlights.length; ++i) {
                hi = highlights[i];

                if (hi.series.bars.show)
                    drawBarHighlight(hi.series, hi.point);
                else
                    drawPointHighlight(hi.series, hi.point);
            }
            octx.restore();
            draw();
            executeHooks(hooks.drawOverlay, [octx]);
        }

        function highlight(s, point, auto) {
            if (typeof s == "number")
                s = series[s];
                s.opacity = 1;
                  for(var i = 0; i < options.seriesOrder.length; i++){
                      if(options.seriesOrder[options.seriesOrder.length-1] == s.n)
                      break;
                      options.seriesOrder.unshift(options.seriesOrder.pop());
                  }
            if (typeof point == "number") {
                var ps = s.datapoints.pointsize;
                point = s.datapoints.points.slice(ps * point, ps * (point + 1));
            }

            var i = indexOfHighlight(s, point);
            if (i == -1) {
                highlights.push({ series: s, point: point, auto: auto });

                triggerRedrawOverlay();
            }
            else if (!auto)
                highlights[i].auto = false;

        }

        function unhighlight(s, point) {
            if (s == null && point == null) {
                highlights = [];
                triggerRedrawOverlay();
            }

            if (typeof s == "number")
                s = series[s];

            if (typeof point == "number")
                point = s.data[point];

            var i = indexOfHighlight(s, point);
            if (i != -1) {
                highlights.splice(i, 1);

                triggerRedrawOverlay();
            }
        }

        function indexOfHighlight(s, p) {
            for (var i = 0; i < highlights.length; ++i) {
                var h = highlights[i];
                if (h.series == s && h.point[0] == p[0]
                    && h.point[1] == p[1])
                    return i;
            }
            return -1;
        }

        function drawPointHighlight(series, point) {
            var x = point[0], y = point[1],
                axisx = series.xaxis, axisy = series.yaxis;

            if (x < axisx.min || x > axisx.max || y < axisy.min || y > axisy.max)
                return;

            var pointRadius = series.points.radius + series.points.lineWidth / 2;
            octx.lineWidth = pointRadius;
            octx.strokeStyle = $.color.parse(series.color).scale('a', 0.5).toString();
            var radius = 1.5 * pointRadius,
                x = axisx.p2c(x),
                y = axisy.p2c(y);

            octx.beginPath();
            if (series.points.symbol == "circle")
                octx.arc(x, y, radius, 0, 2 * Math.PI, false);
            else
                series.points.symbol(octx, x, y, radius, false);
            octx.closePath();
            octx.stroke();
        }

        function drawBarHighlight(series, point) {
            octx.lineWidth = series.bars.lineWidth;
            octx.strokeStyle = $.color.parse(series.color).scale('a', 0.5).toString();
            var fillStyle = $.color.parse(series.color).scale('a', 0.5).toString();
            var barLeft = series.bars.align == "left" ? 0 : -series.bars.barWidth/2;
            drawBar(point[0], point[1], point[2] || 0, barLeft, barLeft + series.bars.barWidth,
                    0, function () { return fillStyle; }, series.xaxis, series.yaxis, octx, series.bars.horizontal, series.bars.lineWidth);
        }

        function getColorOrGradient(spec, bottom, top, defaultColor) {
            if (typeof spec == "string")
                return spec;
            else {
                // assume this is a gradient spec; IE currently only
                // supports a simple vertical gradient properly, so that's
                // what we support too
                var gradient = ctx.createLinearGradient(0, top, 0, bottom);

                for (var i = 0, l = spec.colors.length; i < l; ++i) {
                    var c = spec.colors[i];
                    if (typeof c != "string") {
                        var co = $.color.parse(defaultColor);
                        if (c.brightness != null)
                            co = co.scale('rgb', c.brightness)
                        if (c.opacity != null)
                            co.a *= c.opacity;
                        c = co.toString();
                    }
                    gradient.addColorStop(i / (l - 1), c);
                }

                return gradient;
            }
        }
    }

    $.plot = function(placeholder, data, options) {
        //var t0 = new Date();
        var plot = new Plot($(placeholder), data, options, $.plot.plugins);
        //(window.console ? console.log : alert)("time used (msecs): " + ((new Date()).getTime() - t0.getTime()));
        return plot;
    };

    $.plot.version = "0.7";

    $.plot.plugins = [];

    // returns a string with the date d formatted according to fmt
    $.plot.formatDate = function(d, fmt, monthNames) {
        var leftPad = function(n) {
            n = "" + n;
            return n.length == 1 ? "0" + n : n;
        };

        var r = [];
        var escape = false, padNext = false;
        var hours = d.getUTCHours();
        var isAM = hours < 12;
        if (monthNames == null)
            monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        if (fmt.search(/%p|%P/) != -1) {
            if (hours > 12) {
                hours = hours - 12;
            } else if (hours == 0) {
                hours = 12;
            }
        }

        for (var i = 0; i < fmt.length; ++i) {
            var c = fmt.charAt(i);

            if (escape) {
                switch (c) {
                case 'h': c = "" + hours; break;
                case 'H': c = leftPad(hours); break;
                case 'M': c = leftPad(d.getUTCMinutes()); break;
                case 'S': c = leftPad(d.getUTCSeconds()); break;
                case 'd': c = "" + d.getUTCDate(); break;
                case 'm': c = "" + (d.getUTCMonth() + 1); break;
                case 'y': c = "" + d.getUTCFullYear(); break;
                case 'b': c = "" + monthNames[d.getUTCMonth()]; break;
                case 'p': c = (isAM) ? ("" + "am") : ("" + "pm"); break;
                case 'P': c = (isAM) ? ("" + "AM") : ("" + "PM"); break;
                case '0': c = ""; padNext = true; break;
                }
                if (c && padNext) {
                    c = leftPad(c);
                    padNext = false;
                }
                r.push(c);
                if (!padNext)
                    escape = false;
            }
            else {
                if (c == "%")
                    escape = true;
                else
                    r.push(c);
            }
        }
        return r.join("");
    };

    // round to nearby lower multiple of base
    function floorInBase(n, base) {
        return base * Math.floor(n / base);
    }

})(jQuery);

/*

 * The MIT License



Copyright (c) 2010 by Juergen Marsch



Permission is hereby granted, free of charge, to any person obtaining a copy

of this software and associated documentation files (the "Software"), to deal

in the Software without restriction, including without limitation the rights

to use, copy, modify, merge, publish, distribute, sublicense, and/or sell

copies of the Software, and to permit persons to whom the Software is

furnished to do so, subject to the following conditions:



The above copyright notice and this permission notice shall be included in

all copies or substantial portions of the Software.



THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR

IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,

FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE

AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER

LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,

OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN

THE SOFTWARE.

*/

/*

(function ($)

{    var options =

     {  series: {

        grow: {

            active: true,

            valueIndex: 1,

            stepDelay: 1,

            steps:15,

            stepMode: "linear",

            stepDirection: "up"

        }

     }

    };

    function init(plot) {

        var done = false;

        var growfunc;

        var plt = plot;

        var data = null;

        var opt = null;

        var valueIndex;

        plot.hooks.bindEvents.push(processbindEvents);

        plot.hooks.drawSeries.push(processSeries);

        function processSeries(plot, canvascontext, series)

        {   opt = plot.getOptions();

            valueIndex = opt.series.grow.valueIndex;

            if(opt.series.grow.active == true)

            {   if (done == false)

                {   data = plot.getData();

                    data.actualStep = 0;

                    for (var j = 0; j < data.length; j++)

                    {   data[j].dataOrg = clone(data[j].data);

                        for (var i = 0; i < data[j].data.length; i++)

                            data[j].data[i][valueIndex] = 0;

                    }

                    plot.setData(data);

                    done = true;

                }

            }

        }

        function processbindEvents(plot,eventHolder)

        {   opt = plot.getOptions();

            if (opt.series.grow.active == true)

            {   var d = plot.getData();

                for (var j = 0; j < data.length; j++) {

                    opt.series.grow.steps = Math.max(opt.series.grow.steps, d[j].grow.steps);

                }

                if(opt.series.grow.stepDelay == 0) opt.series.grow.stepDelay++;

                growfunc = window.setInterval(growing, opt.series.grow.stepDelay);

            }

        }

        function growing()

        {   if (data.actualStep < opt.series.grow.steps)

            {   data.actualStep++;

                for(var j = 0; j < data.length; j++)

                { if (typeof data[j].grow.stepMode == "function")

                    {   data[j].grow.stepMode(data[j],data.actualStep,valueIndex); }

                    else

                    {   if (data[j].grow.stepMode == "linear") growLinear();

                        else if (data[j].grow.stepMode == "maximum") growMaximum();

                        else if (data[j].grow.stepMode == "delay") growDelay();

                        else growNone();

                    }

                }

                plt.setData(data);

                plt.draw();

            }

            else

            {   window.clearInterval(growfunc); }

            function growNone()

            {   if (data.actualStep == 1)

                {   for (var i = 0; i < data[j].data.length; i++)

                    {   data[j].data[i][valueIndex] = data[j].dataOrg[i][valueIndex]; }

                }

            }

            function growLinear()

            {   if (data.actualStep <= data[j].grow.steps)

                {   for (var i = 0; i < data[j].data.length; i++)

                    {   if (data[j].grow.stepDirection == "up")

                        {   data[j].data[i][valueIndex] = (data[j].dataOrg[i][valueIndex] - data[j].yaxis.min) / data[j].grow.steps * data.actualStep+data[j].yaxis.min;}

                        else if(data[j].grow.stepDirection == "down")

                        {   data[j].data[i][valueIndex] = data[j].dataOrg[i][valueIndex] + (data[j].yaxis.max - data[j].dataOrg[i][valueIndex]) / data[j].grow.steps * (data[j].grow.steps - data.actualStep); }

                    }

                }

            }

            function growMaximum()

            {   if (data.actualStep <= data[j].grow.steps)

                {   for (var i = 0; i < data[j].data.length; i++)

                    {   if (data[j].grow.stepDirection == "up")

                        {   data[j].data[i][valueIndex] = Math.min(data[j].dataOrg[i][valueIndex], data[j].yaxis.max / data[j].grow.steps * data.actualStep); }

                        else if (data[j].grow.stepDirection == "down")

                        {   data[j].data[i][valueIndex] = Math.max(data[j].dataOrg[i][valueIndex], data[j].yaxis.max / data[j].grow.steps * (data[j].grow.steps - data.actualStep) ); }

                    }

                }

            }

            function growDelay()

            {   if (data.actualStep == data[j].grow.steps)

                {   for (var i = 0; i < data[j].data.length; i++)

                    {   data[j].data[i][valueIndex] = data[j].dataOrg[i][valueIndex]; }

                }

            }

        }

        function clone(obj)

        {   if(obj == null || typeof(obj) != 'object') return obj;

            var temp = new obj.constructor();

            for(var key in obj) temp[key] = clone(obj[key]);

            return temp;

        }

    }



    $.plot.plugins.push({

      init: init,

      options: options,

      name: 'grow',

      version: '0.2'

    });

})(jQuery);
*/



//flot graph tooltips
function showTooltip_old(x, y, contents, color) {

        clearTimeout($(document).data('plothover_remove'));
         $("#tooltip").remove();

    $('<div id="tooltip">' + contents + '</div>').css({
        position : 'absolute',
        display : 'none',
        'z-index' : 1000,
        top : y + 5,
        left : x + 5,
        border : '1px solid '+color,
        'border-radius' : '3px 3px',
        'box-shadow' : '1px 1px 5px #777',
        padding : '5px',
        'background-color' : 'rgb(255, 255, 255)',
        opacity : 0.80
    }).appendTo("body");
        clearTimeout($(document).data('plothover_add'));
        var t = setTimeout(function() {
            $("#tooltip").fadeIn(200);

         }, 500);
        $(document).data('plothover_add', t);
}

var previousPoint = null;
function plothover(pos, item){
        if (item) {

            if (previousPoint != item.dataIndex + "-" + item.seriesIndex) {

                previousPoint = item.dataIndex + "-" + item.seriesIndex;



                var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(3);

var date = new Date(Math.round(x));
var month = date.getUTCMonth() + 1;
var day = date.getUTCDate();
var year = date.getUTCFullYear();

            $('#yAxisHighlight').css({'color': 'inherit'}).find(".tick_highlight").css({'border-top': 'none', 'background': 'none'}).parent().removeAttr('id');
            y = parseFloat(y).toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");

            if(item.series.units == undefined) item.series.units = '';

            if(item.series.datetype == "monthly"){
                showTooltip_old(item.pageX, item.pageY, "<b>"+ y.toString() + "" + item.series.units + "</b><br>" + item.series.label + "", item.series.color );
            }else if(item.series.datetype == "monthly-avg"){
                showTooltip_old(item.pageX, item.pageY, "<b>"+ y.toString() + "</b><br>" + item.series.label + "" + item.series.units + "", item.series.color );
            }else if(item.series.datetype == "monthly-sum"){
                showTooltip_old(item.pageX, item.pageY, "<b>"+ y.toString() + "" + item.series.units + "</b><br>" + item.series.label + "", item.series.color );
            }else{
                showTooltip_old(item.pageX, item.pageY, "<b>"+ y.toString() + "" + item.series.units + "</b><br>" + item.series.label + "", item.series.color );
            }
                $('.y'+item.series.yaxis.n+'Axis').attr('id', 'yAxisHighlight').find(".tick_highlight").css({'border-top': "2px solid "+item.series.color, 'background': $.color.parse(item.series.color).scale('a', .1).toString()});

            }
        } else {

            previousPoint = null;
        clearTimeout($(document).data('plothover_remove'));
        var t = setTimeout(function() {
            $("#tooltip").remove();

         }, 0);
        $(document).data('plothover_remove', t);
            $('#yAxisHighlight').css({'color': 'inherit'}).find(".tick_highlight").css({'border-top': 'none', 'background': 'none'}).parent().removeAttr('id');

        }


}
