<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;
use CoreWine\Component\File;

class CallbackDownloadCommand extends Command{

	public static $signature = 'music:callback';

	public function handle(){

		$progress = File::get(media('music.download.log'));
		
		# Is a download in progress?
		$ds = DownloadStack::where('id',$progress) -> first();

		$playlist = $ds -> playlist;
		
		$path = media("videos/");

		$dirs = [];

		foreach(glob("{$path}*") as $dir){

			try{

				# Check the thumb in order to detect if is a video of playlist

				$file = glob($dir."/*.jpg")[0];
				if(filemtime($file) > $ds -> started_at -> getTimestamp()){
					$dirs[] = $dir;
				}
			}catch(\Exception $e){

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

			$uid = basename($dir."/");

			if(!($video = Video::where('uid',$uid) -> first())){
				$video = new Video();
				$video -> uid = $uid;
				$video -> name = basename($files['video'],".".$files['video_ext']);
				$video -> thumb() -> link($files['thumb']);


				$video -> file() -> link($files['video']);

				# Rename video to "original.ext"
				rename($video -> file() -> file(),dirname($video -> file() -> file()."original.".$files['video_ext']));
				$video -> file() -> link("original.".$files['video_ext']);

				$video -> source = "Youtube";
				$video -> save();
			}

			# Add video to playlist
			if(!$playlist -> videos -> has($video))
				$playlist -> videos -> add($video);
		}

		$playlist -> videos -> save();
		$ds -> delete();

		$this -> call('music:download');
	}
}

?>