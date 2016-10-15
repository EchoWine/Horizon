/**
 * Main
 *
 * @var
 */
var WT = {};

/**
 * Pointer to setTimeout of next message
 *
 * @var
 */
WT.interval = false;

/**
 * List of messages that will be randomly displayed during searching
 *
 * @var
 */
WT.waiting = [
	"This is taking too long",
	"I'm searching the answer to the ultimate question of life, the universe, and everything",
    "The latest episode of Game of Thrones has just been released. A moment please",
    "I'm playing chess right now, wanna join me? <a href='https/it.lichess.org/'></a>",
    "OH YEAH! I WON 1,000,000$ !!! NOW I CAN WASTE ALL MY MONEY ON STEAM !!! Oh... I see.. it's just a scam...",
    "Oh, a free Ipad, guess I just need to download this exe file. OH GOD WHAT IS HAPPENING? A VIRUS? WHERE IS MY MLG ANTIVIRUS?",
    "FORNO, ACCENDERE"
];

/**
 * Need to sto sync all operation?
 *
 * @var
 */
WT.stop_sync = true;

/**
 * Call API: discovery
 *
 * @param {string} database
 * @param {string} key
 * @param {closure} callback
 */
WT.discovery = function(database,key,callback){

	http.get(WT.url+"discovery/"+database+"/"+encodeURIComponent(key),{token:WT.token},callback);
};

/**
 * Call API: add
 *
 * @param {string} database
 * @param {int} id
 * @param {closure} callback
 */
WT.add = function(database,id,callback){

	http.post(WT.url+"discovery/"+database+"/"+id,{token:WT.token},callback);
};


/**
 * Call API: all
 *
 * @param {string} resource
 * @param {closure} callback
 */
WT.all = function(resource,callback){

	http.get(WT.url+resource,{token:WT.token},callback);
};

/**
 * Call API: get
 *
 * @param {string} resource
 * @param {int} id
 * @param {closure} callback
 */
WT.get = function(resource,id,callback){

	http.get(WT.url+resource+"/"+id,{token:WT.token},callback);
};

/**
 * Call API: sync
 *
 * @param {string} resource
 * @param {int} id
 * @param {closure} callback
 */
WT.sync = function(resource,id,callback){

	http.put(WT.url+resource+"/"+id,{token:WT.token},callback);
};

/**
 * Call API: remove
 *
 * @param {string} resource
 * @param {int} id
 * @param {closure} callback
 */
WT.remove = function(resource,id,callback){

	http.delete(WT.url+resource+"/"+id,{token:WT.token},callback);
};


/**
 * Get a random number between min and max
 *
 * @param {int} min
 * @param {int} max
 * 
 * @return {int}
 */
WT.random = function(min,max){

    return Math.floor(Math.random()*(max-min+1)+min);
};


/**
 * @Application
 *
 * @var
 */
WT.app = {}


/**
 * @Application
 * 
 * Discovery new resources
 *
 * @param {string} value
 */
WT.app.discovery = function(value){
	
	if(!value)
		return;

	// Set the searching mode to true
	WT.app.searching(true);

	// Set spinner
	$('.wt-search-spinner-container').html(template.get('wt-search-spinner'));

	// Send the request to "discovery"

	WT.discovery('all',val,function(response){

		html = {'library':'','thetvdb':'','baka-updates':''};

		// The response has sent, so set the "searching mode" to false
		WT.app.searching(false);

		console.log(response);
		$.map(response,function(service,key){
			console.log(key);
			$.map(service,function(resource){

				var part = (resource.user == 1) ? 'library' : key;

				html[part] += template.get('wt-search-result',{
					source:resource.source,
					id:resource.id,
					title:resource.name,
					banner:resource.banner,
					user:resource.user ? 1 : 0,
					library:resource.library ? 1 : 0
				});

			});
		});

		console.log(html);
		WT.app.addResultSearch('.wt-search-library',html['library']);

		WT.app.addResultSearch('.wt-search-thetvdb',html['thetvdb']);
		WT.app.addResultSearch('.wt-search-baka-updates',html['baka-updates']);
	});
};

/**
 * @Application
 * 
 * Change message during searching
 *
 * @param {bool} state
 */
WT.app.searching = function(state){

	clearTimeout(WT.interval);

	if(state){

		$('.wt-section-container').attr('data-status',0);

		var waiting = WT.waiting[WT.random(0,WT.waiting.length - 1)];
		var html = template.get("wt-search-waiting",{waiting:waiting});
		$('.wt-search-waiting').html(html);

		WT.interval = setTimeout(function(){
			WT.app.searching(true);

		},5000);

	}else{
		$('.wt-section-container').attr('data-status',1);
	}
};

/**
 * @Application
 * 
 * Add the result retrieved during discovery
 *
 * @param {string} selector
 * @param {string} html
 */
WT.app.addResultSearch = function(selector,html){

	html = $(html);
	
	html.find('img').on('error',function(){
		$(this).hide();
	});

	$(selector).html(html);
};

WT.app.add = function(database,id){

	var element = $("[wt-add='"+database+","+id+"']").first();

	WT.app.searching(true);

	WT.add(database,id,function(response){
		WT.app.searching(false);
		
		item.addAlert('alert-'+response.status,'.alert-global',response);

		if(element){

			res = element.closest('.wt-search-result');
			res.attr('wt-status-user',1);
			res.appendTo($('.wt-search-library'));

		}
	});

};

/**
 * @Application
 * 
 * Open a modal that contain all basic info
 *
 * @param {string} type
 * @param {int} id
 */
WT.app.info = function(type,id){

	WT.get(type,id,function(response){

		/*
		// Group episode in season
		var seasons = [];
		for(var i in response.episodes){
			episode = response.episodes[i];

			if(typeof seasons[episode.season_n] == 'undefined')
				seasons[episode.season_n] = [];

			seasons[episode.season_n].push(episode);
		}


		// Templating seasons
		html_seasons = '';
		for(var i in seasons){
			var season = seasons[i];
			html_episodes = '';
			
			// Templating episodes
			for(e = 0; e < season.length; e++){
				episode = season[e];
				html_episodes += template.get('wt-get-episode',{
					number: episode.number,
					name: episode.name,
					season: episode.season_n,
					aired_at : episode.aired_at
				});

			}

			html_seasons += template.get('wt-get-season',{
				'number': i,
				'episodes': html_episodes,
			});

		}*/

		switch(response.status){
			case 'continuing':
				status_type = 'primary';
			break;
			case 'ended':
				status_type = 'danger';
			break;
			default:
				status_type = 'default';
			break;
		}

		content = template.get('wt-get-serie',{
			id:response.id,
			name:response.name,
			banner:response.banner,
			overview:response.overview,
			updated_at:response.updated_at,
			status:response.status,
			status_type:status_type,
			database_id:response.container.database_id,
			database_name:response.container.database_name
		});


		modal.open('modal-wt-get',{"modal-wt-get-body":content});
	});
	

}





/**
 * @Application
 *
 * Sync all the resources
 */
WT.app.syncAll = function(){

	modal.open('modal-wt-sync-all',{},{"close":function(){
		console.log("Stopping...");
		WT.stop_sync = true;
	}});
	
	var status = $('.wt-sync-current-status');
	var progress = $('.wt-sync-current-progress');
	var bar = $('.wt-sync-current-bar');

	var manager = function(results,i,attempt,length){

		if(WT.stop_sync)
			return;

		if(i >= length){
			status.html("Completed");
			progress.html("100%");
			bar.find('span').css('width',"100%");
			return;
		}

		resource = results[i];

		attempt_text = attempt == 0 ? '' : ' #'+(attempt)+'';
		status.html(resource.name+attempt_text);
		p = (i + 1) * (length / 100);
		p = parseFloat(p).toFixed(2);
		progress.html(p+"%");
		bar.find('span').css('width',p+"%");

		WT.sync(resource.type,resource.id,function(response){
			if(response.status == 'success'){
				manager(results,i+1,1,length);
			}else if(response.status == 'error'){
				manager(results,i,attempt + 1,length);
			}
		});
			
	};

	// Retrieve all resources
	WT.all(function(response){
		length = 0;
		for(i in response){
			length++;
		}

		WT.stop_sync = false;

		manager(response,0,1,length);
	});
};

/**
 * @Event
 * 
 * Discovery new resources on submit
 */
$('.wt-search-form').on('submit',function(e){
	e.preventDefault();

	// Retrieve key searched
	val = $(this).find('.wt-search-key').val();

	WT.app.discovery(val);
});

/**
 * @Event
 * 
 * Add a resource on click
 */
$('body').on('click','[wt-add]',function(e){

	var info = $(this).attr('wt-add').split(",");
	var database = info[0];
	var id = info[1];

	WT.app.add(database,id);
});

/**
 * @Event
 * 
 * Remove a resource on click
 */
$('body').on('click','[wt-remove]',function(e){
	WT.app.searching(true);
	var element = $(this);
	info = $(this).attr('wt-remove').split(",");

	WT.remove(info[0],info[1],function(response){
		WT.app.searching(false);
		item.addAlert('alert-'+response.status,'.alert-global',response);
		res = element.closest('.wt-search-result');
		res.attr('wt-status-user',0);
		res.appendTo($('.wt-search-discovery'));
	
	});

});

/**
 * @Event
 * 
 * Sync on click
 */
$('body').on('click','[wt-sync]',function(e){
	
	info = $(this).attr('wt-sync').split(",");

	WT.sync(info[0],info[1],function(response){
		console.log(response);
		item.addAlert('alert-'+response.status,'.alert-global',response);
	});

});


/**
 * @Event 
 * 
 * Display a modal that contains info about a resource on click
 */
$('body').on('click','[wt-info]',function(e){
	info = $(this).attr('wt-info').split(",");

	WT.app.info(info[0],info[1],info[2]);
});

/**
 * @Event 
 * 
 * Sync all on click
 */
$('body').on('click','[wt-sync-all]',function(e){
	
	WT.app.syncAll();

});


// ----------------------------------------------------------------
// 
// 	DASHBOARD
//
// ----------------------------------------------------------------


/**
 * @Event 
 * 
 * Show a season on click
 */
$('body').on('click','.wt-get-season',function(){
	var status = $(this).closest('.wt-get-season-container').attr('data-active') == "1";
	$(this).closest('.wt-get-season-container').attr('data-active',status == "1" ? "0" : "1");
});