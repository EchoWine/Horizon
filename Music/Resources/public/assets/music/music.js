var music = {};
music.actual = null;

$('body').on('click','[music-play]',function(){

	var url = $(this).attr('music-play');
	music.play(url,$(this).attr('music-n'));
});

music.interval = null;

music.play = function(url,n){
	music.actual = n;
	clearInterval(music.interval);
	var player = $('#music-player');
	player.get(0).load();
	player.html($("<source src='"+url+"' type='video/mp4'>"));
	player.get(0).play();
};

music.next = function(){

	console.log('next');
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