<?php

namespace WT\Api\MangaFox;

use WT\Api\Object;

use CoreWine\Component\DomDocument;

use DateTime;

class ScanObject extends Object{

	/**
	 * Initialize the object with the response in dom
	 *
	 * @param HTML body $responseph

	 */
	public static function create($response){

		$dom = new DomDocument($response);
		$c = new self();

		$raw = $dom -> getElementsByAttribute('id','viewer') -> item(0);

		if(!$raw)
			return;

		$c -> next = $raw -> getElementsByTagName('a') -> item(0) -> getAttribute('href');

		if(!$raw -> getElementsByTagName('img') -> item(0))
			return;
		
		$c -> raw = $raw -> getElementsByTagName('img') -> item(0) -> getAttribute('src');
		

		return $c;
	}

}