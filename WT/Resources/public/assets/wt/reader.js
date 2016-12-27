WT.reader = {};

WT.reader.page = 1;

WT.reader.reset = function(){

	$("meta[name='viewport']").attr('content','width=device-width, initial-scale=1, maximum-scale=1');

};

WT.reader.mode = function(mode){
	$.cookie("wt.reader.mode",mode);
	$('.wt-manga-reader-mode').attr('wt-manga-reader-container-mode',mode);
	WT.reader.reset();
};


WT.reader.first = function(){
	if(!window.location.hash || window.location.hash == '#'){
		window.location.hash = "#0";
		WT.reader.page = 0;
	}
};

WT.reader.updateSelect = function(){
	$('.wt-manga-reader-select').val(parseInt(window.location.hash.replace("#","")));
	WT.reader.reset();
};

WT.reader.prev = function(){

	var page = parseInt(window.location.hash.replace("#","")) - 1;

	if(page >= 0)
		window.location.hash = '#'+page;

	WT.reader.updateSelect();

};

$('.wt-manga-reader-select').on('change',function(){
	window.location.hash = '#'+$(this).val();
	WT.reader.reset();
});

WT.reader.next = function(){

	var page = parseInt(window.location.hash.replace("#","")) + 1;

	if(page <= $('.wt-manga-reader-all.wt-manga-reader').find('img').length)
		window.location.hash = '#'+page;

	WT.reader.updateSelect();
};



$("[name='wt-reader-mode']").on('change',function(){
	WT.reader.mode($(this).val());
	WT.reader.reset();
});

$('.wt-manga-reader-prev').on('click',function(){
	
	WT.reader.prev();
});

$('.wt-manga-reader-next').on('click',function(){

	WT.reader.next();
});

$('body').keydown(function(e){
	var code = e.keyCode || e.which;
	console.log(code);
	switch(code){

		case 37:
			WT.reader.prev();
		break;
		case 39:
			WT.reader.next();
		break;
	}
});

$(document).ready(function(){
	var mode = $.cookie('wt.reader.mode');

	mode = mode ? mode : 'all';

	$("[name='wt-reader-mode']").val(mode);
	WT.reader.mode(mode);
	WT.reader.first();
});

$('.wt-reader-chapters').on('change',function(){
	window.location.href = $(this).val();
})