$(document).ready(function(){
(function($) {
	var sticky_elements = [];

	$(window).scroll(function() {
	    var windscroll = $(this).scrollTop();

		$.each(sticky_elements, function(i, element){
	    if (windscroll >= element.getYPos()) {

	    	if(!element.isStuck()){
	    		//if offset changed, don't stick just yet
		    	if(element.recheckPos()){
		    		return;
		    	}
	    		element.stick();
	    	}
			if(element.opts.scrollTop){
    			element.css('top', windscroll);
    		}

			throttleEvent(function() {
				element.onScroll(windscroll);
			}, 100, "stickOnScroll"+i);

	    } else if(element.isStuck()) {
	    	element.unstick();
    		element.css('top', 0);
	    }
		});

	});


    $.fn.stickyElement = function(options) {

		if(typeof options == 'object'){
       		var opts = $.extend({}, $.fn.stickyElement.defaults, options);
		}else{
       		var opts = $.fn.stickyElement.defaults;
		}

		var base = this;
		base.opts = opts;
		base.el = $(base);
		base.offset = base.el.offset();
		base.offset.top = base.offset.top - base.el.parent().offset().top;
		var stuck = false;

		base.init = function(){

		};

		base.getYPos = function(){
			return base.offset.top;
		};

		base.recheckPos = function(){
			var orig = base.offset.top;
			base.offset = base.el.offset();
			base.offset.top = base.offset.top - base.el.parent().offset().top;
			if(orig != base.offset.top){
				return true;
			}
			return false;
		};

		base.isStuck = function(){
			return stuck;
		};

		base.onScroll = function(windscroll){
	        base.opts.onScroll(windscroll);
		};

		base.stick = function(){
			stuck = true;
	    	if(opts.noJump){
	    		base.el.before($('<div id="stick-noJump-'+base.el.attr('id')+'"></div>').css('height', base.el.height()));
	    	}

			base.el.addClass('sticky');
		};

		base.unstick = function(){
			stuck = false;
	    	if(opts.noJump){
	    		$('#stick-noJump-'+base.el.attr('id')+'').remove();
	    	}
			base.el.removeClass('sticky');
		};

		if (typeof options == 'string' && typeof base.el.data('stickyElement') != 'undefined') {
            return this.data('stickyElement')[options]();
        }

		base.el.data('stickyElement', base);
		sticky_elements.push(base);


    	$(window).trigger('scroll');

    };

    // Public: Default values
    $.fn.stickyElement.defaults = {
    	noJump : true,
    	scrollTop : true,
    	onScroll : function(){}
    };



})(jQuery);
});



