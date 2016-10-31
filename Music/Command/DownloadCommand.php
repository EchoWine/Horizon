<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;

class DownloadCommand extends Command{

	public static $signature = 'music:update';

	public function handle(){

		echo "Initialization...\n\n";
		
		# Is a download in progress?
		$ds = DownloadStack::where('progress',1) -> first();

		# Skip
		if($ds)
			return;

		$ds = DownloadStack::first();

		# No file to download?
		if(!$ds){
			echo "No files to download found.\n";
			return;
		}

		# Set progress to 1
		$ds -> progress = 1;
		$ds -> save();

		$client = new Client();

		# Download video
		$url_video = Cfg::get('app.path.drive.public')."tmp/video.mp4";
		$client -> download($ds -> url_video,$url_video);

		# Download audio
		$url_audio = Cfg::get('app.path.drive.public')."tmp/audio.m4a";
		$client -> download($ds -> url_audio,$url_audio);

		# Merge video+audio
		$url_complete = Cfg::get('app.path.drive.public')."tmp/stream.mp4";
		exec("ffmpeg -i $url_video -i $url_audio -c:v copy -c:a aac -strict experimental $url_complete");

		# Create a new video model with merged file
		$video = new Video();
		$video -> name = $ds -> name;
		$video -> file() -> setByUrl($url_complete);
		$video -> save();

		# Add video to playlist
		$ds -> playlist -> videos -> add($video);
		$ds -> playlist -> videos -> save();

		# Remove all tmps files
		unlink($url_video);
		unlink($url_audio);
		unlink($url_complete);

		# Delete from stack downloads
		$ds -> delete();
		

		echo "\nCompleted";
	}
}

?>