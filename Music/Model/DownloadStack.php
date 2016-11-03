<?php

namespace Music\Model;

use CoreWine\DataBase\ORM\Model;

use Auth\Model\User;

class DownloadStack extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'download_stack';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> string('name');
	
		$schema -> text('url');

		$schema -> toOne(Playlist::class,'playlist');

		$schema -> toOne(User::class,'user');

		$schema -> integer('progress') -> default(0);

		$schema -> datetime('started_at');
	}
}

?>