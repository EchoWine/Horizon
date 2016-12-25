<?php

namespace WT\Model\Queue;

use CoreWine\DataBase\ORM\Model;

class Chapter extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'queue_chapters';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> string('url');

		$schema -> toOne(\WT\Model\Chapter::class,'chapter');

	}
}

?>