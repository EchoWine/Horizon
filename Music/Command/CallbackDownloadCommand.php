<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;

class CallbackDownloadCommand extends Command{

	public static $signature = 'music:callback';

	public function handle(){

		echo "Initialization...\n\n";
		
		# Is a download in progress?
		$ds = DownloadStack::where('progress',1) -> first();

		$playlist = $ds -> playlist;

		// $ds -> delete();
		
		$path = Cfg::get('app.path.drive.public')."videos/";

		$dirs = [];

		foreach(glob("{$path}*") as $dir){
			if(filemtime($dir) > $ds -> started_at -> getTimestamp()){
				$dirs[] = $dir;
			}
		}

		foreach($dirs as $dir){

			$files = [];

			foreach(glob("{$dir}/*") as $k){
				$ext = pathinfo($k, PATHINFO_EXTENSION);

				if($ext == "jpg"){
					$files['thumb'] = $k;
				}

				if(in_array($ext,['mp4','webm','mkv'])){
					$files['video'] = $k;
					$files['video_ext'] = $ext;
				}

			}

			# Create a new video model with merged file
			$video = new Video();
			$video -> uid = basename($dir."/");
			$video -> name = basename($files['video'],".".$files['video_ext']);
			$video -> thumb() -> link($files['thumb']);
			$video -> file() -> link($files['video']);
			$video -> source = "Youtube";
			$video -> save();

			# Add video to playlist
			$playlist -> videos -> add($video);
		}

		$playlist -> videos -> save();

		echo "\nCompleted";
	}
}

?>