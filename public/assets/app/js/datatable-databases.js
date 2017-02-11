var resize_datatable = function(){

    var table = $('.table-container.resizable:visible:eq(0)');
    if(table.length == 0){
        return;
    }
    var table_height = $(window).height() - table.offset().top - 160;
    if(table_height < 300){
        table_height = 300;
    }
    if($('#custom_style').length == 0){
        $('html > head').append('<style id="custom_style"></style>');
    }
	if($('body').hasClass('activeOverlay')){
	    $('#custom_style').html('');
	}else{
	    $('#custom_style').html('.table-container{'
	    +'max-height: '+table_height+'px;'
	    +'}');
	}

};

$(document).ready(function(){


    function disableContext(){
        return false;
    }

    var x,y,top,left,down,once;
    $('body').delegate(".table-container", 'mousedown', function(e){
        if( e.button !== 2 ) {
          return true;
        }
        once = true;
        $('body').addClass('dragging');
        e.preventDefault();
        down=true;
        x=e.pageX;
        y=e.pageY;
        top=$(this).scrollTop();
        left=$(this).scrollLeft();


        return false;
    });

    $("body").mousemove(function(e){
        if(once === true){
            $('html').bind("contextmenu", disableContext);
            once = false;
        }
        if(down){
            var newX=e.pageX;
            //var newY=e.pageY;

            //console.log(y+", "+newY+", "+top+", "+(top+(newY-y)));

            //$("#stuff").scrollTop(top-newY+y);
            $(".table-container").scrollLeft(left-newX+x);
        }
    });

    function stopscroll(e){
        if(down){
            down=false;
            $('body').removeClass('dragging');
            if(once){
                $('html').unbind("contextmenu", disableContext);
            }
        }else{
            $('html').unbind("contextmenu", disableContext);
        }
    }

    window.addEventListener('mouseup', stopscroll);

        });

function getUrlParams(src){
    var query = src.substring(src.indexOf('?'));

    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
            query  = query.substring(1);

        var urlParams = {};
        while (match = search.exec(query)){
            urlParams[decode(match[1])] = decode(match[2]);
        }

        return urlParams;
    }
    $(document).ready(function(){

        function search(input){
            var form_inputs = {};
            input.parents('tr').find(':input').each(function() {
            form_inputs[this.name] = this.value;
        });
        var reload = input.parents('.datatable:first').find('> a.link');

        var url = reload.attr('href');

        var params = $.extend(getUrlParams(url), form_inputs);
        params['start'] = 0;
        var new_url = url.split('?')[0] + '?' + $.param(params)+'&input_focus='+input.attr('name');

        reload.attr('href', new_url).click();
    }

    $('body').delegate('table > thead > tr > th > input[data-numeric="true"]', 'keypress',
        function(event){
            var value = event.key;
            if(event.charCode !== 0 && !$.isNumeric(value)){
                return false;
            }
        });

    $('body').delegate('table > thead > tr > th > .clear_search', 'click', function(){
        var _this = $(this).parent().find('input');
        _this.val('');
        search(_this);
    });

    $('body').delegate('table > thead > tr > th > input', 'keyup', function(e){
        var _this = $(this);
        if(e.which == 13){
            return search(_this);
        }
        waitForFinalEvent(function(){
            search(_this);
        }, 750, 'datatable_search');
    });

    $('body').delegate(' table > thead > tr > th > select', 'change', function(){
        waitForFinalEvent(function(){
            search($(this));
        }, 750, 'datatable_search');
    });

    $('body').delegate(' table > thead > tr > th > input[type="text"]', 'focusin', function(){
        $(this).select();
    });

    $('body').delegate(' table > thead > tr > th > .exact_match-toggle > .btn', 'click', function(){
        var _this = $(this);
        if(_this.hasClass('btn-gray')){
            _this.removeClass('btn-gray').addClass('btn-silver')
                .attr('title', 'Exact Match: OFF (Click to turn on)')
                .find('>span').html('filter');
            _this.find('button').val('filter');
        }else{
            _this.removeClass('btn-silver').addClass('btn-gray')
                .attr('title', 'Exact Match: ON (Click to turn off)')
                .find('>span').html('exact');
            _this.find('button').val('exact');
        }

        var input = _this.parents('th').find('input');
        if(input.val() !== '')
         search(input);

        return false;
    });

    $('body').delegate('.table-container > table > tbody > tr > td > .dropdown.insert .insert_null', 'click', function(event){

        var td = $(this).parents('td');
        td.find('> .insert_null').remove();
        td.append('<span class="insert_null notifications gray">null</span>');
        td.addClass('insert_null');

        td.find('textarea').val('').trigger('input').css('height', '38px').one('focus', function(){
            $(this).parents('td').removeClass('insert_null').find('>.insert_null').remove();
        });
        td.click();

        return false;
    });



    $(window).on('resize', function(){
        waitForFinalEvent(resize_datatable, 150, 'resize-datatable');
    });


});

