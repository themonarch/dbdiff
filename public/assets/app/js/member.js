$(document).ready(function(){
	window.onbeforeunload = goodbye;
	//$('#top-nav').stickyElement();
	//$('.dropdown-select.dropdown.right').stickyElement();
/*
	$('#user-sidebar .item-wrapper > .item > .toggle').parent().click(function(){

        var _this = $(this).find('.toggle');
		if(_this.hasClass('active')){
			_this.removeClass('active').parents('.item-wrapper').find('.sub-items').stop().slideUp(150);
			_this.parent().find('.icon-right-open').stop().hide();
			_this.find('.icon-down-open').stop().show().fadeOut(150, function(){
				_this.parent().find('.icon-right-open').fadeIn(150);
			});
		}else{
			_this.addClass('active').parents('.item-wrapper').find('.sub-items').stop().slideDown(150);
			_this.parent().find('.icon-down-open').stop().hide();
			_this.find('.icon-right-open').stop().show().fadeOut(150, function(){
				_this.parent().find('.icon-down-open').fadeIn(150);
			});
		}
		return false;
	});
*/



});

function goodbye(e) {
	$('body').addClass('loading').attr('style', 'cursor: progress!important;');
	$('a').css('cursor', 'progress');


}


var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
var monthNamesFull = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


//flot graph tooltips
function showTooltip(x, y, contents, color, position, opacity) {
	var tooltips = "";
	for(tooltip in contents){
		if(tooltip != ''){
			tooltips += "<span class=\"tooltip_date\">"+tooltip+"</span><br>";
		}

		for(i = 0; i < contents[tooltip].length; i++){
			tooltips += ""+contents[tooltip][i] + "<br>";
		}
	}


	$("#tooltip").remove();

	var tooltip = $('<div id="tooltip">' + tooltips + '</div>').css({
		top : y + 5
	}).appendTo("body");


	if (position == 'left') {
		tooltip.css('left', x - 35 - tooltip.width());
	}else{
		tooltip.css('left', x + 35);
	}

	tooltip.css('top', y + 5 - tooltip.height() / 2);

}

var previousPoint = null;
function plothover(pos, items) {
	if (items.length > 0) {
		//if (previousPoint != null) {

		//previousPoint = item.dataIndex + "-" + item.seriesIndex;

		var x = pos.pageX;
		/*
		$('#yAxisHighlight').css({
			'color' : 'inherit'
		}).find(".tick_highlight").css({
			'border-top' : 'none',
			'background' : 'none'
		}).parent().removeAttr('id');
		*/
		var tooltip_content = {};
		for ( i = items.length-1; i >= 0; i--) {
			var item = items[i];
			var y = item.datapoint[1].toFixed(3);
			var date = new Date(Math.round(item.datapoint[0].toFixed(2)));
			var month = date.getUTCMonth() + 1;
			var day = date.getUTCDate();
			var year = date.getUTCFullYear();
			var index = "";
			y = parseFloat(y).toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");
			y = '<div class="colorbox" style="background-color: '+$.color.parse(item.series.color).scale('a', item.series.opacity).toString()+'"></div> ' + y;

			if (item.series.datetype == "none") {
				index = '';
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: '
					+ $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y
					+ "" + item.series.units + " - " + item.series.label
					+ '</span>');
			} else if (item.series.datetype == "monthly") {
				index = monthNamesFull[month - 1] + " " + year;
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: '
					+ $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y
					+ "" + item.series.units + " - " + item.series.label
					+ '</span>');
			} else if (item.series.datetype == "monthly-avg") {
				index = "Average for " + monthNamesFull[month - 1] + " " + year;
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: ' + $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y + "" + item.series.units + " - " + item.series.label + '</span>');
			} else if (item.series.datetype == "monthly-sum") {
				index = "Total for " + monthNamesFull[month - 1] + " " + year;
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: ' + $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y + "" + item.series.units + " - " + item.series.label + '</span>');
			} else if (item.series.datetype == "daily-avg") {
				index = "Average for " + monthNamesFull[month - 1]+ " " + day + ', ' + year;
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: ' + $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y + "" + item.series.units + " - " + item.series.label + '</span>');
			} else {
				index = monthNamesFull[month - 1]+ " " + day + ', ' + year;
				if(tooltip_content[index] == undefined) tooltip_content[index] = [];
				tooltip_content[index].push('<span class="tooltip_values" style="color: ' + $.color.parse('#222222').scale('a', item.series.opacity).toString()+';">' + y + "" + item.series.units + " - " + item.series.label + '</span>');
			}
			/*
			$('.y' + item.series.yaxis.n + 'Axis').attr('id', 'yAxisHighlight').find(".tick_highlight").css({
				'border-top' : "2px solid " + item.series.color,
				'background' : $.color.parse(item.series.color).scale('a', .1).toString()
			});
			*/
		}

		showTooltip(pos.pageX, pos.pageY, tooltip_content, item.series.color, pos.tooltip_position);

		//}
	} else {

		previousPoint = null;
		$('#tooltip').remove();
		/*
		$('#yAxisHighlight').css({
			'color' : 'inherit'
		}).find(".tick_highlight").css({
			'border-top' : 'none',
			'background' : 'none'
		}).parent().removeAttr('id');
		*/
	}

}