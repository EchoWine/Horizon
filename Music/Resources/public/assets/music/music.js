var music = {};
music.interval = null;
music.current = null;

$('body').on('click','[music-play]',function(){

	var url = $(this).attr('music-play');
	music.play($(this).attr('music-n'));
});


music.getVolume = function(){
	return $.cookie('horizon_music_player_volume') ? $.cookie('horizon_music_player_volume') : 0.50;
}

$('body').on('click','#music-player',function(){
	console.log('click');
	music.isPaused() ? music.resume() : music.pause();
});

music.isPaused = function(){
	return music.player.paused;
};

music.resume = function(){
	music.player.play();
};

music.pause = function(){
	music.player.pause();
};

music.play = function(n){

	if(!(video = music.videos[n]))
		return;

	var actual = $("[music-n='"+n+"']");
	
	$('.music-pl-item-active').removeClass('music-pl-item-active');
	actual.addClass('music-pl-item-active');

	music.current = n;

	url.query("Horizon - Music - Player",{video:n});

	clearInterval(music.interval);

	music.player.load();
	$('#music-player').html($('<source src="'+video.file.original+'" type="video/mp4">'));
	music.player.volume = music.getVolume();
	music.player.play();



    $('.music-pl-item-container').animate({scrollTop: $("[music-n='"+n+"']").get(0).offsetTop - 200}, 0);
	$('[music-player-title]').html(video.name);
};


music.next = function(){

	// Next
	key = music.videos[music.current + 1] ? music.current : 0;
	
	// Random
	key = Math.floor((Math.random() * music.videos.length));

	Notify.prompt('Horizon - Music',{
		'body': music.videos[key].name, 
		'icon': music.videos[key].thumb.original
	},function(){
		
	},2000);


	music.play(key);

};

music.load = function(videos){
	var html = '';

	$.map(videos,function(video,key){

		html += template.get('music-playlist-item',{
			'video': video,
			'key': key,
		});
	});

	$('.music-pl-item-container').html(html);
};

$('#music-player').on('ended',function(){
	music.interval = setTimeout(function(){music.next();},1000);
});

$('#music-player').on('volumechange', function(){
    $.cookie('horizon_music_player_volume',$(this).get(0).volume);
});


$(document).ready(function(){

	music.player = $('#music-player').get(0);
	music.load(music.videos);


	music.play(url.getParam('video',-1));

});