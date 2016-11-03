<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;

class DownloadCommand extends Command{

	public static $signature = 'music:download';

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
		$ds -> started_at = new \DateTime();
		$ds -> save();

		$shell = Cfg::get('app.path.drive').'src/Music/Command/yt_download.sh';
		$path = Cfg::get('app.path.drive.public').'uploads/videos/';
		$callback = "/".Cfg::get('app.path.drive').'app/console';
		$params = "music:callback";
		$url = $ds -> url;
		
		echo "bash $shell \"{$path}\" \"$url\" \"{$callback}\" \"{$params}\" > /dev/null &";
		exec("bash $shell \"{$path}\" \"$url\" \"{$callback}\" \"{$params}\" > /dev/null &");

		echo "\nCompleted";
	}
}

?>