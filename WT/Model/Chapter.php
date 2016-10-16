<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;


class Chapter extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'chapters';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		
		$schema -> id();
	
		$schema -> string('name');
	
		$schema -> string('number');

		$schema -> string('volume_n');

		$schema -> datetime('released_at');

		$schema -> text('overview');

		$schema -> toOne(Volume::class,'volume');

		$schema -> toOne(Manga::class,'manga','manga_id');

		$schema -> updated_at();
	}

}

?>