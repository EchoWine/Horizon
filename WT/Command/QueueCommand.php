<?php

namespace WT\Command;
 
use CoreWine\Console\Command;
use WT\Service\WT;

class QueueCommand extends Command{

	public static $signature = 'wt:download:queue';

	public function handle(){
		

		WT::queueDownload('manga-fox',40);

	}
}