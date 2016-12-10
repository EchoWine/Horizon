var music = {};
music.actual = null;
music.interval = null;

$('body').on('click','[music-play]',function(){

	var url = $(this).attr('music-play');
	music.play(url,$(this).attr('music-n'));
});


music.play = function(url,n){

	var actual = $("[music-n='"+n+"']");
	
	$('.music-pl-item-active').removeClass('music-pl-item-active');
	actual.addClass('music-pl-item-active');

	music.actual = n;
	clearInterval(music.interval);
	var player = $('#music-player');
	player.get(0).load();
	player.html($("<source src='"+url+"' type='video/mp4'>"));
	player.get(0).volume = $.cookie('horizon_music_player_volume');
	player.get(0).play();


};


music.next = function(){

	var actual = $("[music-n='"+(parseInt(music.actual))+"']");
	next = actual.closest('.music-pl-item').next().find('[music-n]');
	if(next.length == 0)
		return;

	music.play(next.attr('music-play'),next.attr('music-n'));
};

$('#music-player').on('ended',function(){
	console.log('end');
	music.interval = setTimeout(function(){music.next();},10);
});

$('#music-player').on('volumechange', function(){
    $.cookie('horizon_music_player_volume',$(this).get(0).volume);
});

/**
 * Manipulate DATA
 */
$('body').on('submit','#music-playlist-source-add',function(e){
	e.preventDefault();

	var url = $(this).attr('data-url');

	http.post(url,{token:User.token,youtube_url:$(this).find("[name='youtube_url']").val()},function(response){
		modal.close("music-modal-source-add");

		item.addAlert('alert-'+response.status,'.alert-global',response);

	});	
});