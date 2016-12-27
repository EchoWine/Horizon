WT.reader = {};

WT.reader.mode = function(mode){
	$.cookie("wt.reader.mode",mode);
	$('.wt-manga-reader-container').attr('wt-manga-reader-container-mode',mode);
};


$("[name='wt-reader-mode']").on('change',function(){
	WT.reader.mode($(this).val());
});


WT.reader.first = function(){
	if(!window.location.hash || window.location.hash == '#'){
		window.location.hash = "#1";
	}
};

$(document).ready(function(){
	var mode = $.cookie('wt.reader.mode');

	mode = mode ? mode : 'all';
	
	$("[name='wt-reader-mode']").val(mode);
	WT.reader.mode(mode);
	WT.reader.first();
});

