var app;

$(document).ready(function(){

    app = new App();
    app.init();
    //_app.startBackgroundImage({'element': 'bg'});

});


var App = function() {
    _app = this;

    _app.lazyLoadEnabled = function(){
        if(docCookies.getItem('lazyLoad') === 'true'){
            //console.log('lazy loading is eneabled');
            return true;
        }

            //console.log('lazy loading is disabled');
        return false;
    };


    _app.bindLazyLoad = function(){
        //console.log('attempting to bind lazy loading');

        //make sure lazy load enabled
        if(_app.lazyLoadEnabled() !== true){
            //console.log('cant bind lazy loading because disabled');
            return false;
        }

        //lazy load
        $('.lazy_load .ajax').one('inview', _app.lazyLoad);
        //console.log('bound lazy loading successfully');

    };
    _app.unbindLazyLoad = function(){
        //console.log('attempting to bind lazy loading');

        //lazy load
        $('.lazy_load .ajax').unbind('inview');
        //console.log('bound lazy loading successfully');

    };

    _app.lazyLoad = function (event, visible, topOrBottomOrBoth) {
        //console.log('lazy load triggered');
            if (visible == true) {
                _app.loadMoreStories($(event.target));
                // element is now visible in the viewport
                if (topOrBottomOrBoth == 'top') {
                    // top part of element is visible
                } else if (topOrBottomOrBoth == 'bottom') {
                    // bottom part of element is visible
                } else {
                    // whole part of element is visible
                }
            } else {
                // element has gone out of viewport
            }
    };

    /**
     * structure should look like this:
     * <div data-tabs="">
     *  <nested divs>
     *      <div data-tabs-container="(optional: id of element with data-tabs-contents if not within data-tabs element)">
     *          <div data-tab="">
     *          <div data-tab="">
     *  <nested divs>
     *      <div data-tabs-contents=""> OR add data-tabs-contents to data-tabs element
     *          <div data-tab-content="">
     *          <div data-tab-content="">
     *          <div data-tab-content="">
     */
    _app.widgetTabsInit = function(){
        $('body').delegate('[data-tabs-container] > [data-tab]', 'click', function(){
            if($(this).hasClass('active')){
                if($(this).data('tab-untoggle') === true){
                    $(this).removeClass('active');
                    return _app.widgetTabsOpenActive($(this).parent());
                }else{
                    _app.widgetTabsOpenActive($(this).parent());
                    var radio = $(this).find('input[type="radio"]');
                    if(radio.length > 0){
                        radio.prop('checked', true);
                        return true;
                    }
                    return false;
                }
            }else{
                $(this).parent().find(' > [data-tab].active').removeClass('active');
                $(this).addClass('active');
                _app.widgetTabsOpenActive($(this).parent());
                var radio = $(this).find('input[type="radio"]');
                if(radio.length > 0){
                    radio.prop('checked', true);
                    return true;
                }
            }


            return false;
        });
        $('[data-tabs-container] > [data-tab].active').click();

        $('[data-tab_trigger]').on('click', function(){
            $($(this).data('tab_trigger')).click();
           return false;
        });

    };


    _app.widgetTabsOpenActive = function(tabs_container){
        if(tabs_container.data('tabs-container') !== ''){
            var content_container = $(tabs_container.data('tabs-container'));
        }else{
            var content_container = tabs_container.closest('[data-tabs]');
            if(typeof content_container.data('tabs-contents') == 'undefined'){
                content_container = content_container.find('[data-tabs-contents]:first');
            }
        }


        var activeTabNum = -1;
        var activeTab = tabs_container.find('[data-tab].active');
        if(activeTab.length == 0){
            //$( this ).find('[data-tab]:first').addClass('active');
        }else{
            activeTabNum = activeTab.index();
        }
        content_container.find('[data-tab-content]').each(function( index ) {
            if(index == activeTabNum){
                $( this ).show();
            }else{
                $( this ).hide();
            }
        });
    };

    _app.body = null;
    _app.init = function(){
        String.prototype.escapeHTML = function() {
            return this.replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
        };


        $('#recent_visitors').delegate('.item', 'click', function(){
            overlay('recent_visitor_breakdown');
            var el = $('#recent_visitor_breakdown');
            el.find('.section-header h2').html($(this).find('.control .text').html()
            ).prepend($(this).find('.control .flag').clone());
                app.ajax(
                    '/ajax/hits/'+$(this).data('ip_id')+'/'+$(this).parent().data('domain')+'',
                    {},
                    el.find('.visitor-breakdown'),
                    el
                );
        });
        $('#recent_visitor_breakdown').delegate('.load_more', 'click', function(){
            app.ajax(
                '/ajax/hits/'+$(this).data('ip_id')+'/'+$(this).data('domain')+'',
                {'start_id': $(this).data('start_id')},
                $(this).parents('.more_destination:first'),
                $(this)
            );
        });

        $('body .switches.radio .switch.active').find('input[type="radio"]').prop("checked", true);
        $('body .switches .switch.active[data-tab]').click();
        $('body').delegate('.switches.radio .switch', 'click', function(){
            if($(this).hasClass('active')){
                var switch_el = $(this).parent().find('.switch').last();
                if($(this).index() == switch_el.index()){
                    var switch_el = $(this).parent().find('.switch').first();
                }
                switch_el.click();
            }else{
                $(this).parent().find('.switch.active').removeClass('active');
                $(this).addClass('active').find('input[type="radio"]').prop("checked", true);
            }
        });

        $('#top_countries .item').on('click', function(){
            if(!$(this).hasClass('open')){
                app.ajax(
                    '/ajax/country/'+$(this).data('country_code')+'/'
                    +$(this).parent().data('start_date')+'/'
                    +$(this).parent().data('end_date')+'/'
                    +$(this).parent().data('domain')+'',
                    {},
                    $(this).find('.sub-items'),
                    $(this)

                );
            }
        });

        $('.list_dropdown').dropdown_v2({
            contentsClass : 'sub-items',
            closeOnOutsideClick : false,
            closeWhenOtherOpens : true
        });

        $('.dropbtn').dropdown_v2({
            contentsClass : 'dropdown-contents',
            closeOnOutsideClick : true,
            closeWhenOtherOpens : true
        });

        $('.widget.widget-dropdown').dropdown_v2({
            contentsClass : 'widget-content',
            closeOnOutsideClick : false,
            closeWhenOtherOpens : false
        });

        var timezone = (-1)*(new Date().getTimezoneOffset())/60;
        if(
            docCookies.getItem('timezone') === null
            || docCookies.getItem('timezone') !== timezone
        ){
            docCookies.setItem('timezone', timezone, Infinity, '/');
        }


        $.ajaxSetup({
            //tell our backend app that this was an ajax request
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-Ajax', 'true');
            },
            // Disable caching of AJAX responses
            cache: false
        });

        _app.body = $('body');

        _app.body.delegate('[data-dynamic_form_submit]', 'click', function(event){
        	var trigger = $(this);
        	if(trigger.hasClass('active')){
        		return false;
        	}
        	var trigger_val = '';
        	if(trigger.data('dynamic_form_submit') != ''){
				trigger_val = '&dynamic_form_submit='+trigger.data('dynamic_form_submit');
        	}
			var form = trigger.parents('[data-dynamic_form]');
            var output_destination = form.attr('data-output_destination');

            if(typeof form.attr('data-confirm') !== 'undefined'){
                if(!confirm(form.attr('data-confirm'))){
                    return false;
                }
            }
            var href = form.attr('href');
            if(href == undefined){
                href = form.data('action');
            }

            if(output_destination === ''){
                if(form.attr('id') !== undefined){
                    output_destination = '#'+form.attr('id');
                }else{
                    var uid = _app.uniqueId();
                    output_destination = '#'+uid;
                    form.attr('id', uid);
                }
            }

            var replace_destination = false;
            _app.ajax(
                href,
                form.find(':input').serialize()+trigger_val,
                output_destination,
                form.data('show_loader'),
                undefined,
                undefined,
                form.data('ajax_replace')
            );
        });


        _app.body.delegate('[data-ajax_form] [type="submit"]', 'click', function(event){
            $(this).addClass('clicked');
        });

        _app.body.delegate("[data-ajax_form]", 'submit', function(event){
            var submit_el = $(this).find("[type='submit'].clicked").removeClass('clicked');
            app.ajaxForm($(this), '&'+submit_el.attr('name')+'='+submit_el.val());
            return false;
        });

        _app.body.delegate("[data-trigger-click]", 'click', function(){
            $($(this).data('trigger-click')).click();
        });

        _app.body.delegate("[data-ajax=\"true\"]", 'click', function(){
            app.ajaxLink($(this));
            return false;
        });

        _app.body.delegate('.daterange .dropdown-select', 'click', function(){
            docCookies.setItem('daterange', $(this).data('value'), Infinity, '/');
            $(this).parent().find('.selected').removeClass('selected');
            $(this).addClass('selected');
            window.location.reload();
            return false;
        });
        _app.body.delegate("[data-widget_shift]", 'click', function(){
            var new_widget = $($(this).data('widget_shift'));
            var old_widget = new_widget.prev();
            var parent_widget = new_widget.parent();
            var widget_width = parent_widget.width();
            //parent_widget.css('overflow', 'hidden');
            new_widget.css('left', widget_width);
            old_widget.css('height', 1).animate({
                left: -widget_width
              }, 450, function() {
                // Animation complete.
            });
            new_widget.animate({
                left: 0
              }, 450, function() {
                // Animation complete.
            });

        });


        _app.body.delegate("", 'click', function(){

        });

        //new dropdown();

        _app.fadeMessages();
        //$('#login_form').overlay();

        //relative timestamps
        $(".timeago").livequery(function(){
            $(this).timeago();
        });

        $('.form textarea').autosize({append: "\n"});

        //text area auto expand
        $('.form .autosize').livequery(function(){
            $(this).autosize();
        });


        $('#daterange1').DatePicker({
            flat: true,
            date: ['2008-07-28','2008-07-31'],
            current: '2008-07-31',
            calendars: 1,
            mode: 'range',
            starts: 1
        });


        /** toggles **//*
        $('.toggle').on('click', function(){
            if($(this).hasClass('active')){
                $('#'+$(this).attr('data-toggle_id')).stop().slideDown();
                $(this).parent().find('.icon-right-open').stop().hide();
                $(this).removeClass('active').find('.icon-down-open').stop().show().fadeOut(150, function(){
                    $(this).parent().find('.icon-right-open').fadeIn(150);
                });
            }else{
                $('#'+$(this).attr('data-toggle_id')).stop().slideUp();
                $(this).parent().find('.icon-down-open').stop().hide();
                $(this).addClass('active').find('.icon-right-open').stop().show().fadeOut(150, function(){
                    $(this).parent().find('.icon-down-open').fadeIn(150);
                });
            }
            return false;
        });
        */

        /** toggles **/
        $('[data-toggle_id]').on('click', function(){
            var el = $('#'+$(this).attr('data-toggle_id'));
            var slideUp = el.data('slideUp');
            if(el.data('slideUp') !== true){
                console.log('slideDown');
                /*el.stop().slideDown(200, function(){
                    $(this).addClass('hideMobile').addClass('hideTablet').attr('style', 'display: inline-block!important;');
                });*/
               el.stop().addClass('hideMobile').addClass('hideTablet').attr('style', 'display: inline-block!important;');
            }else{
                console.log('slideUp');
                /*el.stop().slideUp(200, function(){
                    $(this).addClass('hideMobile').addClass('hideTablet').removeAttr('style');
                });*/
               el.stop().hide().addClass('hideMobile').addClass('hideTablet').removeAttr('style');
            }

            el.data('slideUp', !slideUp);

            return false;
        });

        if(docCookies.getItem('lazyLoad') === 'true'){
            $('.stories .auto input').prop('checked', true);
        }else{
            $('.stories .auto input').prop('checked', false);
        }


        /**
         * menu toggler
         */
        $('.sidebar_toggle').on('click', function(){
            if($('#wrapper').data('sidebar_open') !== true){
                $('#wrapper').data('sidebar_open', true);
                $('#user-sidebar').css({'display': 'block'});
                //$('.clear-sidebar').attr('style', 'overflow: unset; position: fixed; width: 100%; right: -320px;');
                //$('head').append('<link id="640px" rel="stylesheet" type="text/css" href="/css/640px.css">');
                $(this).find('i.icon-right').removeClass('icon-right').addClass('icon-left');
            }else{
                $('#wrapper').data('sidebar_open', false);
                //$('.clear-sidebar').removeAttr('style');
                $('#user-sidebar').removeAttr('style')
                    .find('.background').removeAttr('style');
                //$('#640px').remove();
                $(this).find('i.icon-left').removeClass('icon-left').addClass('icon-right');
            }

        });


        _app.widgetTabsInit();



        _app.sectionHeights();





    };

    _app.sectionHeights = function(){

        var dummy_height = 0;
        if($('#global-container .section > .dummy').length > 0){
            $(window).resize(function(){
                throttleEvent(function(){
                    init();
                }, 250, 'resize');
            });

            init();
        }

        /**
         * set section heights
         */
        function init(){
            $dummy = $('#global-container .section > .dummy');
            $new_dummy_height = ($(window).height()-$('#page-nav').height())*.80;
            $dummy.css('min-height', $new_dummy_height);

            dummy_height = $new_dummy_height/2;

            $dummy.first().css('min-height', $new_dummy_height-$('#header-with-nav').height());
        }


    };

    _app.fadeMessages = function(destination){
        if(typeof destination == 'object'){
            $(destination ).find('.messages, .input-error').hide().fadeIn(350);
        }else if(typeof destination == 'string'){
            $(destination + ' .messages, '+destination+' .input-error').hide().fadeIn(350);
        }else{
            $('.messages, .input-error').hide().fadeIn(350);
        }
        $('.tooltip').tipsy({gravity: 's'});
    };

    _app.search = function(_this){
        throttleEvent(function(){
            var form = _this;
            _app.ajax(
                form.attr('action'),
                {'query': _this.find('input[name="query"]').val()},
                '#items_searchbar > .results',
                '#items_searchbar > .results'
            );
        }, 1000, 'search');
    };

    _app.jsonp = function(
            url,
            data,
            destination,
            show_loader,/* true (default) = show full body loader;
                        false = don't show any loaders;
                        string = add ajax_loading class to element found by string;*/
            callback
        ){
        _app.ajax(url, data, destination, show_loader, callback, 'jsonp');
    };

    _app.scrollIntoView = function(element){

        //scroll element into view if it is above viewport
        var scrolltop = $(window).scrollTop();
        var height = $(window).height();

        //number of pixels away the destination element is from viewport
        var topset = 0;

        if($(element).length != 0 ){
            topset = $(element).offset().top - scrolltop;
        }

        if(topset < 0){//element above the viewport
            /*//scroll the viewport up to element
            var scrolltop_new = scrolltop + topset - $(window).height()*.1;
            //$(window).scrollTop(scrolltop_new);
            var overlay_container = $('#overlay-fade');
            if(overlay_container.length > 0){
                overlay_container.animate({ 'scrollTop':0 }, 100);
            }else{
                $('html').animate({ 'scrollTop':scrolltop_new }, 100);
            }*/

        }else if(topset > height-20){
            //scroll the viewport up to element
            var scrolltop_new = topset - height/2;
            //$(window).scrollTop(scrolltop_new);
            var overlay_container = $('#overlay-fade');
            if(overlay_container.length > 0){
                overlay_container.scrollTop(scrolltop_new);
            }else{
                $('html').scrollTop(scrolltop_new);
            }
        }





    };

    _app.ajax = function(
            url,
            data,
            destination,
            show_loader,/* true (default) = show full body loader;
                        false = don't show any loaders;
                        string = add ajax_loading class to element found by string;*/
            callback,
            dataType,
            replace_destination,
            complete_callback
        ){
        if(show_loader === undefined){
            show_loader = true;
        }
        var destination_el = $(destination);
        if(show_loader === true){
            //if loader overlay not open
            if(!_app.body.hasClass('ajax_loading')){
                //display loader overlay
                if(_app.body.find(' > .ajax_loader').length === 0){
                    _app.body.append('<div class="ajax_loader"></div>');
                if(destination_el.is(':empty'))
                    destination_el.append('<div style="height: 100px;"></div>');
                }
            }
        }else if(show_loader !== false){
            if(typeof show_loader === 'object'){
                var loader_el = show_loader;
            }else{
                var loader_el = $(show_loader).first();
            }
            //display loader overlay
            if(loader_el.find(' > .ajax_loader').length === 0){
                if(destination_el.is(':empty'))
                    destination_el.append('<div style="height: 100px;"></div>');
                loader_el.append('<div class="ajax_loader"></div>');
            }

        }

        if(loader_el !== undefined){
            _app.scrollIntoView(loader_el);
            loader_el.addClass('ajax_loading').show();
        }


        $.ajax({
            type: "POST",
            url: url,
            //timeout: 5000,
            dataType: dataType,
            data: data,
            error: function(jqXHR, textStatus, errorThrown){
                if(jqXHR.getResponseHeader('X-Error_msg') !== null){
                    alert('Error: ' + jqXHR.getResponseHeader('X-Error_msg'));
                }else{
                    alert(textStatus+ ': '+errorThrown);
                }
            },
            complete: function(){
                //hide loader overlay if open
                if(show_loader === true){
                    _app.body.find('.ajax_loading > .ajax_loader').fadeOut(250, function(){
                        _app.body.removeClass('ajax_loading');
                        $(this).removeAttr('style');
                    });
                }else if(show_loader !== false){
                    _app.body.find('.ajax_loading > .ajax_loader').fadeOut(250, function(){
                        $(show_loader).removeClass('ajax_loading');
                        $(this).removeAttr('style');
                    });
                }
                if(typeof complete_callback === 'function'){
                    complete_callback(data);
                }

            },
            success: function(data, textStatus, jqXHR){
                if(typeof callback === 'function'){
                    callback(data);
                }
                if(jqXHR.getResponseHeader('X-Ajax-Redirect') !== null){
                    window.location = jqXHR.getResponseHeader('X-Ajax-Redirect');
                    return false;
                }
                if(destination == undefined){
                    return;
                }
                if(replace_destination === true){
                    data = $(data);
                    $(destination).replaceWith(data);
                    destination = '#'+data.attr('id');
                }else{
                    $(destination).html(data);
                }
                $('.timeago').timeago();
                //scroll lement into view if it is above viewport
                var scrolltop = $(window).scrollTop();
                //number of pixels away the destination element is from viewport

                var topset = 0;

                if($(destination).length != 0 ){
                    var msg_el = $(destination).find('.messages').first();
                    if(msg_el.length > 0){
                        topset = msg_el.offset().top - scrolltop;
                    }else{
                        topset = $(destination).offset().top - scrolltop;
                    }
                }
                if(topset < 0 && $(destination).find('.messages').length > 0){//destination above the viewport
                    //scroll the viewport up to destination
                    var scrolltop_new = scrolltop + topset - $(window).height()*.1;
                    //$(window).scrollTop(scrolltop_new);
                    var overlay_container = $('#overlay-fade');
                    if(overlay_container.length > 0){
                        overlay_container.animate({ 'scrollTop': 0 }, 205, function(){
                            _app.fadeMessages(destination);
                        });
                    }else{
                        $('html').animate({ 'scrollTop':scrolltop_new }, 205, function(){
                            _app.fadeMessages(destination);
                        });
                    }
                }else{
                    //alert(destination);
                    _app.fadeMessages(destination);
                }



                //replace html into destination
                //alert(destination);
            }
        });
    };

    _app.ajaxLink = function(link_element){
        var output_destination = link_element.attr('data-output_destination');

        if(typeof link_element.attr('data-confirm') !== 'undefined'){
            if(!confirm(link_element.attr('data-confirm'))){
                return false;
            }
        }

        if(output_destination === ''){
            if(link_element.attr('id') !== undefined){
                output_destination = '#'+link_element.attr('id');
            }else{
                var uid = _app.uniqueId();
                output_destination = '#'+uid;
                link_element.attr('id', uid);
            }
        }

        var replace_destination = false;
		var post_data = decodeURIComponent(link_element.data('post'));
		if(post_data != ''){
			post_data = JSON.parse(post_data);
		}else{
			post_data = [];
		}
        _app.ajax(
                link_element.attr('href'),
                post_data,
                output_destination,
                link_element.data('show_loader'),
                undefined,
                undefined,
                link_element.data('ajax_replace')
            );


    };

    _app.ajaxForm = function(form_element, submit_value){
        if(typeof submit_value == 'undefined'){
            submit_value = '';
        }

        var output_destination = form_element.attr('data-ajax_form');

        if(typeof form_element.attr('data-confirm') !== 'undefined'){
            if(!confirm(form_element.attr('data-confirm'))){
                return false;
            }
        }

        if(output_destination === ''){
            if(form_element.attr('id') !== undefined){
                output_destination = '#'+form_element.attr('id');
            }else{
                var uid = _app.uniqueId();
                output_destination = '#'+uid;
                form_element.attr('id', uid);
            }
        }

        if(form_element.data('form_toggle') === true){
            var loader_el = $(form_element.data('show_loader'));
            if(loader_el.css('display') !== 'none'){
                loader_el.hide();
                return false;
            }
        }
		var loader_el = form_element.data('show_loader');
		if(typeof loader_el == "undefined"){
			loader_el = output_destination;
		}

        _app.ajax(
                form_element.attr('action'),
                form_element.serialize()+submit_value,
                output_destination,
                loader_el,
                undefined,
                undefined,
                form_element.data('ajax_replace')
            );


    };
    _app.uniqueId = function(){
      return Math.round(new Date().getTime() + (Math.random() * 100));
    };


    var bg_multiplier = 1;

    _app.startBackgroundImage = function(options){
        _app.background = {
                                'element': null,
                                'loopBackAndForth': false,
                                'speed': 50
                        };

        if(typeof options == 'object'){
            _app.background = $.extend({}, _app.background, options);
        }
        $('.'+_app.background.element).parent().prepend($('.'+_app.background.element).clone());
        window.setInterval(app.moveBackgroundImage,_app.background.speed);
    };

    _app.moveBackgroundImage = function(){
        var bg1 = $('.'+_app.background.element+':eq(1)');
        var bg2 = $('.'+_app.background.element+':eq(0)');
        var multiplier = 1;
        var bg2_pos = bg2.css('background-position').replace('px', '').split(" ");
        bg2_pos[0] = parseInt(bg2_pos[0]);
        if(_app.background.loopBackAndForth){
            if(bg2_pos[0] == -100){
                bg_multiplier = -1;
            }else if(bg2_pos[0] == 0){
                bg_multiplier = 1;
            }
        }
        var bg1_opacity = bg1.css('opacity');
        //console.log(bg1_opacity, bg2_pos[0]);
        if(bg1_opacity == 0){//time to reset

            bg1.css({'background-position': bg2_pos[0]+'px 0'});
            bg1.css({'opacity': 1});
            bg2.css({'background-position': bg2_pos[0]+1*bg_multiplier+'px 0'});
        }else{
            bg1.css({'opacity': bg1_opacity-.1});
        }

        return false;
    };

    _app.findLocation = function(callback){
        _app.jsonp(
                'http://ip-api.com/json',
                null,
                null,
                '#content > .page-placeholder',
                function(data){
                    callback(data);
                }
            );

    };

    _app.rand = function(min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    };


};



//delay firing function until [ms] since last event, calling only the last queued event.
var waitForFinalEvent = (function() {
    var timers = {};
    return function(callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout (timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

//fire function once instantly and queue consecutive calls, firing only the last
//queued call after [ms] since last call
var throttleEvent = (function() {
    var timers = {};
    var finalTimers = {};
    return function(callback, ms, uniqueId) {

        //if function already queued, save this one as last one to fire.
        if (timers[uniqueId]) {
            //clearTimeout (timers[uniqueId]);
            finalTimers[uniqueId] = callback;
            return false;
        }else{//fire this event and tell next one to wait
            //fire event
            callback();

            //set final event to fire after delay
            timers[uniqueId] = setTimeout(function(){

                if(finalTimers[uniqueId]){
                    finalTimers[uniqueId]();
                    finalTimers[uniqueId] = null;
                }
                timers[uniqueId] = null;
            }, ms);


        }
    };
})();
















