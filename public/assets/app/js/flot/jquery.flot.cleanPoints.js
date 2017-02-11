(function ($) {
    var options = {
        series: { points: {
                    cleanpoints: true
                } } 
    };
    
    function init(plot) {
        function cleanPoints(plot, s, datapoints) {
            s.skipPoints = [];
            if (s.points.show == false || s.points.cleanpoints != true  ){
                return;
            }
                var options = plot.getAxes();
                var skipPoints = [];
                var current_point = datapoints.points[0];
                var canvas_size = options.xaxis.options.max - options.xaxis.options.min;

                for(var i = 0; i < datapoints.points.length; i+=2){
                    var next_point = datapoints.points[i];
                    var difference = next_point-current_point;
                    if( i != 0 && datapoints.points.length > 4 && difference/canvas_size <= .05){
                        if(i == datapoints.points.length-2){
                        skipPoints[i-2] = true;
                        }else{
                        skipPoints[i] = true;
                        }//delete datapoints.points[i];
                        //delete datapoints.points[i+1];
                    }else{
                        current_point = next_point;
                    }
                    
                    if(options.xaxis.options.zoomInRange !== false && difference != 0){
                        if(options.xaxis.options.zoomInRange === true || options.xaxis.options.zoomInRange < difference){
                            options.xaxis.options.zoomInRange = difference;
                        }
                    }
                    
                }
                //datapoints.points = datapoints.points.filter(function(){return true})
                s.skipPoints = skipPoints;
        }
        
        plot.hooks.processDatapoints.push(cleanPoints);
    }
    
    $.plot.plugins.push({
        init: init,
        options: options,
        name: 'cleanpoints',
        version: '1.0'
    });
})(jQuery);