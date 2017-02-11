WT.reader = {};

WT.reader.page = 1;

WT.reader.reset = function(){

	// $("meta[name='viewport']").attr('content','width=device-width, initial-scale=1, maximum-scale=1');

};

WT.reader.mode = function(mode){
	Cookies.set("wt.reader.mode", mode, { expires: 7, secure: true });
	$('.wt-manga-reader-mode').attr('wt-manga-reader-container-mode',mode);
	WT.reader.reset();
};

WT.reader.setPage = function(page){


	if(page < 1 || page > $('.wt-manga-reader-pag.wt-manga-reader').find('[wt-manga-reader-raw]').length)
		return;

	WT.reader.page = page;
	$("[wt-manga-reader-raw]").hide();
	$("[wt-manga-reader-raw='"+page+"']").show();

	$('.wt-manga-reader-select').val(WT.reader.page);
	WT.reader.reset();

	url.query("Horizon - What's Today - Reader",{page:page});
	
};


WT.reader.prev = function(){
	WT.reader.setPage(WT.reader.page - 1);
};

$('.wt-manga-reader-select').on('change',function(){
	WT.reader.setPage($(this).val());
});

$('.wt-manga-reader-move').on('click',function(){
	WT.reader.setPage($(this).attr('value'));
});

WT.reader.next = function(){

	WT.reader.setPage(WT.reader.page + 1);

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
	var mode = Cookies.get('wt.reader.mode','all');

	$("[name='wt-reader-mode']").val(mode);
	WT.reader.mode(mode);
	WT.reader.setPage(url.getParam('page',1));


});

$('.wt-reader-chapters').on('change',function(){
	window.location.href = $(this).val();

});