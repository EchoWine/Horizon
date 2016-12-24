Action = {};


Action.is = function(element){
	return element.attr('action-progress') == 1;
}

Action.start = function(element){

	element.attr('action-progress',1);
};

Action.end = function(element){

	element.remove('action-progress');
};