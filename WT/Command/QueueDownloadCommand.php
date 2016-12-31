<?php

namespace WT\Command;
 
use CoreWine\Console\Command;
use WT\Service\WT;

class QueueDownloadCommand extends Command{

	public static $signature = 'wt:download:tmp';

	public function handle(){
		

		WT::queueDownloadById('manga-fox',569);

	}
}