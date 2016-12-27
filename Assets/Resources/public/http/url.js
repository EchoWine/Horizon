var url = {};


url.query = function(title,params){

    var url = window.location.href.split('?')[0] + "?" + $.param(params);
    
    window.history.pushState({}, title, url);
};

url.getParam = function(name,default_value){

	if(!default_value){
		default_value = '';
	}

	name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
	var results = regex.exec(location.search);
	return results === null ? default_value : decodeURIComponent(results[1].replace(/\+/g, ' '));
};