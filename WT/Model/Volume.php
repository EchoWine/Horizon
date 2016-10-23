<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;

class Volume extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'volumes';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> string('number');

		$schema -> toOne(Manga::class,'manga');

		$schema -> toMany('chapters',Chapter::class,'manga');

	}
}

?>