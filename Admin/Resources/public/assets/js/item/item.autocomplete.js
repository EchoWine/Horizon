/**
 * Autocomplete
 * 
 * Resolve select/multiselect model relation
 */
item.autocomplete = {};

/**
 * Initialize
 * 
 * Add a hidden input after input detected as autocomplete
 *
 * @return void
 */
item.autocomplete.ini = function(){
	$.map($('[data-autocomplete-column]'),function(field){
		field = $(field);
		column = field.attr('data-autocomplete-column');
		field.after("<input type='hidden' name='"+column+"'>");
	});
};

/**
 * Update the value
 *
 * @param {string} name
 * @param {string} column
 * @param {string} label
 * @param {string} value
 *
 * @return void
 */
item.autocomplete.update = function(name,label,value){
	$("[name='"+name+"']").val(value);
	$("[data-autocomplete-name='"+name+"']").val(label);
};

/**
 * Remove ?
 */
item.autocomplete.remove = function(name){
	$("[data-autocomplete-container='"+name+"']").remove();

};

item.autocomplete.load = function(hidden,name){

	var field = $("[data-autocomplete-name='"+name+"']");
	var url = field.attr('data-autocomplete-url');

	var params = {};
	params['id'] = hidden.val();

	// Make the request
	api.all(url,params,function(response){
		var results = response.data.results;
		var row = results[0];

		if(row)
			item.autocomplete.update(name,row.value,hidden.val());
		
	});


};

item.autocomplete.get = function(element,field_name,field_column,url,search,field_primary,value,callback){

	var params = {};

	params['filter'] = value;
	params['show'] = 5;

	// Make the request
	api.all(url,params,function(response){

		item.autocomplete.remove(field_name);

		var results = '';

		// Reset all other containers
		$("[data-autocomplete-container]").remove();

		$.map(response.data.results,function(row,key){

			results += template.get('autocomplete-result',{
				label:row['value'],
				value:row['id'],
				name:field_name,
				column:field_column
			});
		});

		if(results !== ''){
			var html = template.get('autocomplete-container',{
				results:results,
				name:field_name
			});

			element.after(html);
		}
	});
};

$('body').on('click','[data-autocomplete-insert]',function(){

	var name = $(this).attr('data-autocomplete-insert');
	var label = $(this).attr('data-autocomplete-label');
	var value = $(this).attr('data-autocomplete-value');
	var column = $(this).attr('data-autocomplete-hidden');

	item.autocomplete.update(name,label,value);
});

$('body').on('keyup change focus','[data-autocomplete-name]',function(e){

	if(!$(this).is(":focus"))
		return;

	item.autocomplete.retrieve($(this),function(){

	});
});



item.autocomplete.retrieve = function(field,callback){
	var name = field.attr('data-autocomplete-name');
	var column = field.attr('data-autocomplete-column');
	var url = field.attr('data-autocomplete-url');
	var label = field.attr('data-autocomplete-label');
	var primary = field.attr('data-autocomplete-value');
	var search = field.attr('data-autocomplete-search');
	var value = field.val();

	item.autocomplete.get(field,name,column,url,search,primary,value,callback());

};

$('body').on('blur','[data-autocomplete-name]',function(e){
	var name = $(this).attr('data-autocomplete-name');
	//item.autocomplete.remove(name);
});

/**
 * Close the modal when outside is clicked
 */
$(document).click(function(event){
	if(!$(event.target).is('[data-autocomplete-name]')){

		$.map($('[data-autocomplete-container]'),function(container){
			container = $(container);
			var name = container.attr('data-autocomplete-container');
			if(!$("[data-autocomplete-name='"+name+"']").is(":focus")){
				if(name){
					item.autocomplete.remove(name);
				}
			}
			
		});
	}
	
});

$(document).ready(function(){
	item.autocomplete.ini();
});