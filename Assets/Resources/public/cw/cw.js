var cw = {};

cw.load = function(){

	var d = $('[cw-default]');

	if(d)
		cw.click(d);
};

cw.click = function(element){

	var object = element.attr('cw-show');
	var parts = object.split(":");
	var container = $("[cw-container='"+parts[0]+"']");
	var elements = parts[1].split("|");

	$('[cw-show-active]').removeAttr('cw-show-active');
	element.attr('cw-show-active','');

	if(container)
		cw.show(container,elements);
};

cw.show = function(container,elements){
	$.map(container.find('[cw-element]'),function(val){
		val = $(val);
		type = val.attr('cw-element');
		types = type.split("|");

		var show = false;



		for(i = 0;i < types.length;i++){
			type = types[i];
			if(elements.indexOf(type) != -1){
				show = true;
				break;
			}
		}

		if(show)
			val.show();
		else
			val.hide();

	});
};

$('body').on('click','[cw-show]',function(){
	cw.click($(this));
});

$(document).ready(function(){
	cw.load();
});