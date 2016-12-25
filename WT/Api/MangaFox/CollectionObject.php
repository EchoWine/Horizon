<?php

namespace WT\Api\MangaFox;

use WT\Api\Object;
use CoreWine\Component\DomDocument;
use CoreWine\Component\Collection;

class CollectionObject extends Object{

	/**
	 * Create a collection of manga
	 *
	 * @param HTML body $response
	 */
	public static function create($response){

		$dom = new DomDocument($response);
		$rows = $dom -> getElementsByAttribute('id','mangalist') -> item(0);

		if(!$rows)
			return [];

		$rows = $rows -> getElementsByTagName('ul') -> item(0) -> getElementsByTagName('li');


		$collection = new Collection();

		foreach($rows as $row){
			$o = MangaObject::basic($row);
			$collection[$o -> id] = [
				'database' => 'manga-fox',
				'id' => $o -> id,
				'name' => $o -> name,
				'poster' => $o -> poster,
			];
		
		}
		
		return $collection;
	}

	/**
	 * Return a list of all manga in releases
	 *
	 * @param string $response
	 *
	 * @return collection
	 */
	public static function releases($response){

		$collection = new Collection();

		$dom = new DomDocument($response);
		$rows = $dom -> getElementsByAttribute('id','updates') -> item(0);

		if(!$rows)
			return $collection;

		$rows = $rows -> getElementsByTagName('li');

		foreach($rows as $row){
			
			$manga = MangaObject::release($row);

			$collection[$manga -> id] = [
				'id' => $manga -> id,
				'chapters' => $manga -> chapters
			];
		}

		return $collection;

	}


}