/**
 * Main
 *
 * @var
 */
WT.Resource = {};


$('body').on('click','[wt-resource-seen]',function(){

	$('body').attr('wt-resource-seen',$(this).attr('wt-resource-seen'));
});

WT.Resource.checkPrevious = function(n,checked){

	for(;n>0;n--){

		$('.wt-resource-table').find("[wt-resource-ep-n='"+n+"']").prop('checked',checked);
	}

};

$('.wt-resource-seen-checkbox').on('change',function(){

	if($("[name='wt-resource-seen-type']").val() == 'previous')
		WT.Resource.checkPrevious($(this).attr('wt-resource-ep-n'),$(this).is(':checked'));

})

$('#wt-resource-btn-apply-changes').on('click',function(){

	var c = {};
	$.map($("[name='wt-resource-checkbox[]']"),function(value,key){

		if($(value).is(':checked'))
			c[key] = $(value).val();
	});

	WT.Resource.seen($(this).attr('wt-resource-container-id'),c,function(response){
		item.addAlert('alert-'+response.status,'.alert-global',response);
	});
});


/**
 * Call API
 *
 * @param {integer} id
 * @param {array} object
 * @param {closure} callback
 */
WT.Resource.seen = function(id,consume,callback){

	http.post(WT.url+"resource/consume/"+encodeURIComponent(id),{consume:consume,token:WT.token},callback);
};

$(document).ready(function(){

});