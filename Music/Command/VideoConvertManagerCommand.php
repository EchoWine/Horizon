<?php

namespace Music\Command;
 
use CoreWine\Console\Command;
use Music\Model\Video;

class VideoConvertManagerCommand extends Command{

	public static $signature = 'music:video:convert:manager';

	public function handle(){
	

		$videos = [];
		foreach(Video::repository() -> whereNull('file_h264') -> take(50) -> get() as $video){

			$path = dirname($video -> file() -> file());
			$destination = "{$path}/original_h264.mp4";
			$command = "ffmpeg -i {$video -> file() -> file()} -c:v libx264 -crf 23 -preset medium -c:a aac -b:a 128k -strict -2 $destination";
			exec($command);
			$video -> file_H264() -> setValue("original_h264.mp4");
			$video -> save();
		}


	}
}

?>