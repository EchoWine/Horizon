<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Cfg;
use CoreWine\Http\Client;
use Music\Model\DownloadStack;
use Music\Model\Video;
use CoreWine\Component\File;

class DownloadCommand extends Command{

	public static $signature = 'music:download';

	public function handle(){

		$ds = DownloadStack::first();

		# No file to download?
		if(!$ds){
			
			# remove progress
			File::remove(media('music.download.log'));
			return;
		}

		File::set(media('music.download.log'),$ds -> id);

		# Set progress to 1
		$ds -> progress = 1;
		$ds -> started_at = new \DateTime();
		$ds -> save();

		$shell = drive('src/Music/Command/yt_download.sh');
		$path = drive('public/uploads/videos/');

		if(!file_exists($path))
			mkdir($path,0755,true);

		$callback = drive('app/console');
		$params = "music:callback";
		$url = $ds -> url;
		
		$command = "bash $shell \"{$path}\" \"$url\" \"{$callback}\" \"{$params}\"";

		$command = str_replace("\\","/",$command);
		echo $command;
		exec($command);

		echo "\nCompleted";
	}
}

?>