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


		$shell = Cfg::get('app.path.drive').'src/Music/Commands/yt_download.sh';
		$path = Cfg::get('app.path.drive.public').'uploads/videos/';

		echo "bash {$shell} {$path} \"https://www.youtube.com/watch?v=BZP1rYjoBgI\" \"app/console music:callback\"";

		exec('bash {$shell} "{$path}" "https://www.youtube.com/watch?v=BZP1rYjoBgI" "app/console music:callback"');

		# Set progress to 1
		$ds -> progress = 1;
		$ds -> started_at = new \DateTime();
		$ds -> save();

		echo "\nCompleted";
	}
}

?>