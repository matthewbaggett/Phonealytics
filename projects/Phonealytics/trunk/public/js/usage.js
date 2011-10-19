$(document).ready(function(){
	$('.section.most_frequent ul.panes li').hide();
	$('.section.most_frequent ul.panes li:first').show().addClass('first');
	$('.section.most_frequent ul.tabs li:first').addClass('first').addClass('selected');
	$('.section.most_frequent ul.tabs li:last').addClass('last');
	$('.section.most_frequent ul.tabs li a').click(function(e){
		e.preventDefault();
		var li = $(this).parent();
		var rel = li.attr('rel');
		$(".section.most_frequent ul.tabs li.selected").removeClass('selected');
		li.addClass('selected');
		$(".section.most_frequent ul.panes li").hide();
		$(".section.most_frequent ul.panes li[rel='" + rel + "']").show();
	});
});