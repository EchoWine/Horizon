<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;
use Music\Model\PlaylistSync;
use CoreWine\Component\File;

class ManagerCommand extends Command{

	public static $signature = 'music:go';

	public function handle(){

			
		if(File::exists(media('music.download.log')))
			return;
		

		# Put in download_stack all playlists

		foreach(PlaylistSync::all() as $pl){
			DownloadStack::create([
				'playlist_id' => $pl -> playlist -> id,
				'url' => $pl -> url,
				'user_id' => $pl -> playlist -> user_id
			]);
		}

		$this -> call('music:download');
		
	}
}

?>