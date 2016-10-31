var action_api = {};

$('body').on('submit','[action-modal-api]',function(e){
	e.preventDefault();

	var attr = $(this).attr('action-modal-api').split("|");

	var method = attr[0];
	var url = attr[1];
	var params = {};

	$.map($(this).find('[name]'),function(v){
		params[$(v).attr('name')] = $(v).val();
	});

	console.log(params);

	container_modal = 'alert-modal';
	http.request(method,url,params,function(response){
		if(response.status == 'success' || !container_modal){
			modal.closeActual();
			item.addAlert('alert-success','.alert-global',response);
		}

		if(response.status == 'error'){
			item.addAlert('alert-danger','.'+container_modal,response);
		}
	});
});

$(document).ready(function(){

});