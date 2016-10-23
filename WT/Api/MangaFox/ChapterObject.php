<?php

namespace WT\Api\MangaFox;

use WT\Api\Object;

class ChapterObject extends Object{

	/**
	 * Initialize the object with the response in dom
	 *
	 * @param DOM Object
	 */
	public static function create($dom){

		$c = new self();

		# Retrieve date
		$date = $dom -> getElementsByTagName('span') -> item(0) -> nodeValue;

		switch($date){
			case 'Today':
				$c -> releaset_at = (new DateTime()) -> setTime(00,00,00) -> format('Y-m-d H:i:s');
			break;
			case 'Yesterday':
				$c -> releaset_at = (new DateTime()) -> setTime(00,00,00) -> modify('-1 days') -> format('Y-m-d H:i:s');
			break;
			default:
				$c -> released_at = \DateTime::createFromFormat('M d, Y', $date) -> setTime(00,00,00) -> format('Y-m-d H:i:s');
			break;
		}
		
		# Basic info from link
		$name = $dom -> getElementsByTagName('a') -> item(1) ;
		$href = $name -> getAttribute('href');
		
		# Link for scan
		$c -> scan = $href;

		# Name chapter
		$c -> name = $name -> nodeValue;

		# Number chapter
		$number = intval(preg_replace("/[c]/","",basename(dirname($href))));
		
		if($number == '')
			$number = 0;

		$c -> number = $number;


		# Volume
		$volume = basename(dirname(dirname($href)));

		if(preg_match("/^v([0-9]*)$/",$volume) || $volume == 'vTBD'){

			if($volume == 'vTBD'){
				$volume = 'TBD';
			}else{

				$volume = preg_replace("/[v0]/","",$volume);

				if(!$volume)
					$volume = 0;

				$volume = intval($volume);
			}
			
		}else{
			$volume = -1;
		}

		$c -> volume = $volume;


		return $c;
	}

}