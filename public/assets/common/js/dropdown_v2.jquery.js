/** dropdown_v2s **/
(function($) {

var dropdown_v2 = function(action, callback) {

	_dropdown_v2 = this;

    var drop_selectors = Array();

	var selectors = [];
	_dropdown_v2.addSelector = function(selector, custom_options){
		drop_selectors.push(selector);
		selectors[selector] = custom_options;
	};

	_dropdown_v2.init = function(){
		//clicking triggers opens associated dropdown_v2s
		/*$('['+drop_selector+'-trigger]').on('click', function(){
			//$('#'+$(this).attr(drop_selector+'-trigger')).trigger('click');
			return false;
		});*/
		$(document).ready(function(){
			$('[data-dropdown-trigger]').on('click', function(){
				$($(this).attr('data-dropdown-trigger') + ' > .controls').trigger('click');
				return false;
			});
		});
		$(document).click(function(event) {
			var clicked_el = $(event.target);
			var in_dropdown_v2_content = 0;
			for(var i = 0; i < drop_selectors.length; i++){
				var drop_selector = drop_selectors[i];
			//if click was directly ON a dropdown
		    if (clicked_el.is(drop_selector)){//if click is directly on dropdown_v2
		    	//toggle the dropdown_v2 content
		    	//console.log('click was directly ON .dropdown_v2');
				if(clicked_el.hasClass('open')){
					_dropdown_v2.closeActivedropdown_v2(drop_selector);
				}else{
		   			_dropdown_v2.closeActivedropdown_v2(drop_selector);
		       		_dropdown_v2.adddropdown_v2Active(clicked_el).find('.'+selectors[drop_selector].contentsClass+':eq(0)').stop().slideDown(200);
				}
				break;
		    }else if(//or click was IN .dropdown_v2 but outside of .dropdown_v2-contents
		    		(in_dropdown_v2_content = clicked_el.parents(drop_selector).length) != 0 //child of dropdown_v2
		    		&& !clicked_el.is(drop_selector+' .'+selectors[drop_selector].contentsClass+'') //but not ON content div
		    		&& clicked_el.parents(drop_selector+' .'+selectors[drop_selector].contentsClass+'').length == 0 // and not IN content div
			){

		    	//console.log('click was IN .dropdown_v2 but outside of .dropdown_v2-contents');
				//toggle the dropdown_v2 content
				var drop_el = clicked_el.parents(drop_selector);
				if(drop_el.hasClass('open')){
		   		_dropdown_v2.closedropdown_v2(drop_el.find('.'+selectors[drop_selector].contentsClass+':eq(0)'));
				}else{
		   		_dropdown_v2.closeActivedropdown_v2(drop_selector);
		       	_dropdown_v2.adddropdown_v2Active(drop_el).find('.'+selectors[drop_selector].contentsClass+':eq(0)').stop().slideDown(200);
				}

				break;
		    }else if(in_dropdown_v2_content > 0){//click was inside content area, do nothing
		    	//console.log('click was inside content area, doing nothing');

				break;
		    }else{//click was outside the dropdown_v2, close open dropdown_v2s
		    	//console.log('click was outside the dropdown_v2, closing open dropdown_v2s ');
		    	if(selectors[drop_selector].closeOnOutsideClick){
		   			_dropdown_v2.closeActivedropdown_v2(drop_selector);
		   		}
		    }
		    }

		});

	};

	_dropdown_v2.removedropdown_v2Active = function(drop_el){
			drop_el.removeClass('open');
			return drop_el;
	};

	_dropdown_v2.adddropdown_v2Active = function(drop_el){
			drop_el.addClass('open');
			return drop_el;
	};

	_dropdown_v2.closedropdown_v2 = function(drop_el){
		drop_el.stop().slideUp(200, function(){
			_dropdown_v2.removedropdown_v2Active($(this).parent());
			if(typeof callback == 'function'){
				callback();
			}
		});
	};

	_dropdown_v2.closeActivedropdown_v2 = function(drop_selector, callback){

    	if(!selectors[drop_selector].closeWhenOtherOpens){
   			return;
   		}

		$('body').find(drop_selector+'.open > .'+selectors[drop_selector].contentsClass+'').each(function(){
			_dropdown_v2.closedropdown_v2($(this));
		return true;
		});
	};
    /*
	if (typeof action == 'string') {
		if(action == 'close'){
        	return _dropdown_v2.closeActivedropdown_v2(callback);
		}
    }*/

	_dropdown_v2.init();

};

	var dropdown_v2 = new dropdown_v2();

	$.fn.dropdown_v2 = function(options) {
		if(typeof options == 'object'){
	   		var opts = $.extend({}, $.fn.dropdown_v2.defaults, options);
		}else{
	   		var opts = $.fn.dropdown_v2.defaults;
		}

		var base = this;

		base.init = function(){
			//add selector to dropdown_v2s with custom options
			dropdown_v2.addSelector(this.selector, opts);
		};

		base.init();

	};

    // Public: Default values
    $.fn.dropdown_v2.defaults = {
    	contentsClass : 'dropdown_v2-contents',
    	closeOnOutsideClick : true,//true = close when clicking outside of this dropdown_v2
    	closeWhenOtherOpens : true,//close when ANY other dropdown_v2 opens
    	closeWhenOtherOpens : true//close when other dropdown_v2 of same class opens
    };

})(jQuery);
