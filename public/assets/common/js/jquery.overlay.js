
var current_overlay = null;
var overlay = function(overlay_id, options){
		//get the overlay element
		var overlay = $('#'+overlay_id);

		//create it if not exists
		if(overlay.length == 0){
			$('body').append('<div id="'+overlay_id+'" class="modal-overlay"></div>');
			overlay = $('#'+overlay_id);
            if(options.ajax_url == undefined){
               overlay.overlay('close');
               overlay.remove();
            }
		}

		//set the configs
		$('#'+overlay_id).overlay(options);

		//open
		$('#'+overlay_id).overlay('open');



};

(function($) {
	var current_overlay = null;
	//on click triggers to open/create overlays
	$(document).ready(function(){
		//register click triggers
		$('body').on('click', '[data-overlay-id]', function(e) {
			e.preventDefault();
			var overlay_id = $(this).attr('data-overlay-id');
			if(
                $(this).data('ajax') === undefined
                && $(this).data('ajax_url') === undefined
                && $(this).attr('href') !== undefined
                && $(this).attr('href') !== '#'
            ){
                $(this).data('ajax_url', $(this).attr('href'));
			}
			//create overlay
			overlay(overlay_id, $(this).data());
		});

		//register click triggers
		$('body').on('click', '[data-overlay-action]', function(e) {
			e.preventDefault();
			if(current_overlay != null){
				current_overlay[$(this).data('overlay-action')]($(this).data());
			}
		});
	});

    $.fn.overlay = function(options, function_options) {
		var base = $(this).data('overlay');
		if(base == undefined){
			base = this;
		}
		base.overlay_id = $(this).attr('id');
		base.xhr;

    	if(base.opts == undefined){
    		base.opts = $.extend({}, $.fn.overlay.defaults);
    	}


    	//if custom settings, merge them with config
		if(typeof options == 'object'){
       		base.opts = $.extend(base.opts, options);
		}

		if(base.opts.autoOpenCountLimit !== false){
			var overlyCookie = docCookies.getItem('overlayOpenCount'+base.overlay_id);
			if(overlyCookie == '' || isNaN(overlyCookie)){
				overlyCookie = '0';
			}
			if(parseInt(overlyCookie) >= base.opts.autoOpenCountLimit){
				base.opts.autoOpen = false;
			}
		}

    	var opts = $.extend({}, base.opts);


		current_overlay = base;


		base.init = function(){
			if(base.opts.addCloseButton){
				base.append('<div class="close_overlay" data-close-overlay=""></div>');
			}
			//bind close events
			base.container.on('click', function(event){
				var target_id = $(event.target).attr('id');
				var target_class = $(event.target).attr('class');
				if(base.opts.closeOnFadeClick && (
					target_id == 'overlay-fade'
					|| target_id == 'overlay-vertical_center'
					|| target_id == 'overlay-horizontal_center'
					|| target_class == 'overlay-wrapper'
				)){
					base.close();
				}
			});
			$('#overlay-fade').delegate('[data-close-overlay]', 'click', function(){
				base.close();

                if($(this).data('close-overlay') === false){
                    return true;
                }
				return false;
			});

		};

		//prevent the user from being able to close overlay
		base.disableClose = function(){
			$('#overlay-fade').addClass('disableClose');
			opts.disable_close = true;
		};

		//allow the user to close overlay
		base.enableClose = function(){
			$('#overlay-fade').removeClass('disableClose');
			opts.disable_close = false;
		};

		base.forceClose = function(data){
			var result = base.close(true);
			if(data != undefined && data.overlayGoToUrl != undefined){
				window.location.href = data.overlayGoToUrl;
			}
			return result;
		};

		base.close = function(force){

			if(force !== true && opts.disable_close == true){
				return false;
			}

            if(base.xhr && base.xhr.readystate != 4){
                base.xhr.abort();
            }

			//remove generated close
			$('#overlay-fade .close_overlay').last().remove();
			var current_overlay = $('#overlay-fade .modal-overlay');
			if(current_overlay.length == 0){
				return;
			}

			//remove container
			current_overlay.unwrap();
			current_overlay.unwrap();
			current_overlay.unwrap();
			current_overlay.unwrap();

			$('body').removeClass('activeOverlay');
			opts.isOpen = false;
			if(opts.close_function != false && typeof opts.close_function == 'function'){
				opts.close_function(opts);
			}

		};


		base.prepareOverlay = function(){

			if(opts.isOpen && opts.overlay_id === base.overlay_id){
				base.container = $('#overlay-fade');
				return false;
			}

			opts.overlay_id = base.overlay_id;

			//close any open overlays
			base.forceClose();
			var style = '';
			if(opts.closeOnFadeClick === false){
				style = 'cursor: default;';
			}
            var style_overlay_horizontal_center = '';
            var style_overlay_horizontal_center = '';
			if(typeof opts.max_width !== 'undefined'){
                style_overlay_horizontal_center += 'width: 100%;';
                base.css('max-width', opts.max_width);
			}else{
			    base.css('max-width', '');
			}

			//wrap contents in overlay
			base.wrap('<div id="overlay-fade" style="'+style+'" class="'+opts.overlay_id+'"></div>');
			base.wrap('<div id="overlay-horizontal_center" style="'+style+' '+style_overlay_horizontal_center+'"></div>');
			base.wrap('<div id="overlay-vertical_center" style="'+style+'"></div>');
			base.wrap('<div class="overlay-wrapper" style="'+style+'"></div>');
			base.container = $('#overlay-fade');

			return true;
		};

		base.scrollOpen = function(){
				var scrollOpenPosition = opts.scrollOpen;
				var overlayScrollHandler = function() {
					throttleEvent(function() {
					    var windscroll = $(document).scrollTop();
					    var percentage = windscroll/$(document).height()*100;
					    if(percentage > scrollOpenPosition){
					    	if(base.opts.scrollOpen !== false){
								base.open();
							}
					    }
					}, 100, "overlay_scroll_tracker");
				};
				//open after x page scrolled
				$(document).scroll(overlayScrollHandler);
				opts.scrollOpen = false;
				open = false;
		};

		base.mouseTrackInit = function(seconds){

		    var mouseleaveHandler = function(evt) {
                if(
                    (evt.toElement !== undefined
                    && evt.toElement !== null)
                    || (evt.relatedTarget !== undefined
                    && evt.relatedTarget !== null)
                ){
                    //console.log('mouse not REALLY left page..');
                    //console.log(evt.toElement);
                    //console.log(evt.relatedTarget);
                    return;
                }

                //console.log('evt.clientY = ' + evt.clientY);
                //console.log('evt.pageY = ' + evt.pageY);
                if(evt.clientY > 20){
                    continueCountingMouseTime = false;
                    //console.log('Mouse left but not at top.');
                    return;
                }

                if(window.mouseTotalTimeOnPage < seconds){
                    //console.log('mouse left page, but threshold not met.');
                    //continueCountingMouseTime = false;
                    return;
                }

                if(window.overlayAllowed != true){
                    //console.log('mouse left page, but not allowed to open.');
                    //continueCountingMouseTime = false;
                    return;
                }

                //console.log('Mouse has left the page.');

                $(document.documentElement).off('mouseleave.overlay', evt.callee);

                base.open();

            };

		    //if previous instance exists
		    if(typeof window.mouseTotalTimeOnPage !== 'undefined'){
                //clear out mouseleave handler
                //console.log('clearing mouseleave');
                $(document.documentElement).off('mouseleave.overlay');

                //set new timeout limit

		    }else{//first instance
    		    //console.log(typeof base.mouseEnterTimer);
    		    window.overlayAllowed = true;
    		    if(typeof base.mouseEnterTimer !== "undefined"){
    		        //console.log('timer already initialized');
    		        return;
    		    }

                //console.log('starting mouse tracking interval.');
    			window.mouseTotalTimeOnPage  = 0;//total time mouse was on page
    			var continueCountingMouseTime = true;

    			//start tracking seconds mouse is on page
    			base.mouseEnterTimer = window.setInterval(function(){
    			    if(continueCountingMouseTime == false){
    			        //console.log('mouse not on page');
    			        return;
    			    }

    				window.mouseTotalTimeOnPage += 1;
    				//console.log('window.mouseTotalTimeOnPage = ' + window.mouseTotalTimeOnPage);
    				//if mouse on page time reached threshold
    				if(window.mouseTotalTimeOnPage >= seconds){
    				    //console.log('clear interval: window.mouseTotalTimeOnPage = ' + window.mouseTotalTimeOnPage +' >= '+seconds);
    					window.clearInterval(base.mouseEnterTimer);//stop counting
    				}
    			}, 1000);

                $(document).on('mouseenter', function(evt) {
                        //console.log('mouse is back on the page.');
                        continueCountingMouseTime = true;
                });


                $(document.documentElement).on('mousemove.overlay', function(e){
                    var target = e.target.tagName.toLowerCase();

                    if(target == 'select' || target == 'option'){
                        mouse_unbounce = false;
                        return;
                    }
                    //////console.log(target);

                    var pos = (e.pageY - $(window).scrollTop()) / $(window).height();
                   if(pos < 0.1){
                       window.overlayAllowed = true;
                   }else{
                       window.overlayAllowed = false;
                   }

                });
            }



            // Exit intent trigger
            $(document.documentElement).on('mouseleave.overlay', mouseleaveHandler);




        };

		//private
		base.open = function(){

            //if overlay element no longer exists...
            if(typeof base.data('overlay') == 'undefined'){
                //console.log('overlay contents were removed or replaced.');
                return false;
            }

			var open = true;




			//prepare popup
			base.prepareOverlay();




			//allow closing popup
			if(opts.disable_close){
				base.disableClose();
			}else{
				base.enableClose();
			}

			//disable open-triggers
			base.opts.mouseLeaveOnPageDelay = false;
			base.opts.timer = false;
			base.opts.scrollOpen = false;

			//load contents if needed
			base.ajax();

			//fade in
			if(opts.fade){
				base.container.fadeIn(321, function(){
					$(this).css('display', 'block');
				});
			}else{
				base.container.show();
			}

			opts.isOpen = true;
			$('body').addClass('activeOverlay');
			if(opts.autoOpenCountLimit !== false){
				var count = docCookies.getItem('overlayOpenCount'+base.overlay_id);
				if(count == '' || isNaN(count)){
					count = 1;
				}else{
					count = parseInt(count) + 1;
				}
				docCookies.setItem('overlayOpenCount'+base.overlay_id, count, Infinity, '/');
			}
			base.init();


		};


		//private
		base.ajax = function(){
			if(opts.ajax_url === false){
				return;
			}

			//if ajax results already exists
			base.ajax_contents = base.find('.ajax_results');
			if(base.ajax_contents.length == 0){
				base.append('<div class="ajax_results"></div>');
				base.ajax_contents = base.find('.ajax_results');
			}

			//if ajax caching enabled and url is same, stop here
			if(opts.ajax_caching === true && base.ajax_contents.data('ajax_url') === opts.ajax_url){
				return false;
			}

			//else replace the ajax results with new ajax results with url data tag
			base.ajax_contents.replaceWith('<div class="ajax_results" data-ajax_url="'+opts.ajax_url+'"></div>');

			base.ajax_contents = base.find('.ajax_results');
			base.ajax_contents.html('');
			$('#overlay-fade').addClass('loading');

			base.data('ajax', opts.ajax_url);
			var dataType = 'html';
			if(opts.response_type == 'jsonp'){
				dataType = 'jsonp';
			}
	        base.xhr = $.ajax({
	            url : opts.ajax_url,
	            async : true,
           		dataType : dataType,
	            type : 'GET',
            	//data : {'hs_app_source' : hs_app_source},
	            success : function(data, textStatus, jqXHR) {
	            	if(this.dataType == 'jsonp'){
						base.ajax_contents.html(data.html);
					}else{
						base.ajax_contents.html(data);
					}
					base.trigger('overlay_loaded');

                    if(jqXHR.getResponseHeader('X-Ajax-Redirect') !== null){
                        window.location = jqXHR.getResponseHeader('X-Ajax-Redirect');
                        base.close();
                    }

	            },
            error: function(jqXHR, textStatus, errorThrown){
                base.enableClose();
                if(jqXHR.getResponseHeader('x-error_msg') !== null){
                    base.close();
                    alert('Error: ' + jqXHR.getResponseHeader('x-error_msg'));
                }
            },
	            complete : function() {
	                $('#overlay-fade').removeClass('loading');
	            }
	        });

		};


		$(base).data('overlay', base);

		if (typeof options == 'string' && typeof $(base).data('overlay') != 'undefined') {
            return this.data('overlay')[options](function_options);
        }else if(opts.autoOpen){
        	base.open();
        }


      if(!opts.autoOpen){
        if(opts.mouseLeaveOnPageDelay != false){
            base.mouseTrackInit(opts.mouseLeaveOnPageDelay);
            opts.mouseLeaveOnPageDelay = false;
        }


        if(opts.scrollOpen !== false){
            if(opts.scrollOpenDelay !== false){
                scrollOpenDelay = window.setInterval(function(){
                        base.scrollOpen();
                        window.clearInterval(scrollOpenDelay);
                    }, opts.scrollOpenDelay*1000);
            }else{
                base.scrollOpen();
            }
        }else{
            $(document).unbind("scroll");
        }

        //open it after timer if timer set.
        if(opts.timer !== false){
            var overlay_timer = window.setInterval( function(){
                if(base.opts.timer !== false){
                    base.open();
                }
                window.clearInterval(overlay_timer);
            }, opts.timer*1000);
            opts.timer = false;
        }
    }




    };

    // Public: Default values
    $.fn.overlay.defaults = {
    	disable_close : false,//allow closing the popup
    	closeOnFadeClick : true,//if closing allowed, clicking on faded part closes overlay
    	addCloseButton : true,//if closing allowed, auto add close button
    	fade : true,//allow fading in and out
    	ajax_url : false,//path to ajax content
    	ajax_caching : true,//dont reload ajax content if already loaded
    	close_function : false,
    	timer: false,//number of seconds to wait before opening automatically
    	scrollOpen: false,//percent of page from top to scroll before opening automatically
    	scrollOpenDelay: false,//number of seconds to wait before checking scrollOpen position
    	mouseLeaveOnPageDelay: false,//total number of seconds mouse is on page before showing popup when mouse leaves.
    	autoOpen : false, //open popup instantly
    	autoOpenCountLimit : false //number of times to auto open (tracked via cookie)

    };



})($);




