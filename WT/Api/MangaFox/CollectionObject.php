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
		$rows = $dom -> getElementsByAttribute('id','listing') -> item(0);

		if(!$rows)
			return [];

		$rows = $rows -> getElementsByTagName('tr');
		

		$collection = new Collection();

		$i = 0;
		foreach($rows as $row){
			if($i == 0){

			}else{

				$o = MangaObject::basic($row);
				$collection[$o -> id] = [
					'database' => 'manga-fox',
					'id' => $o -> id,
					'name' => $o -> name,
					'poster' => $o -> poster,
				];
			}

			$i++;
		}

		return $collection;
	}

}