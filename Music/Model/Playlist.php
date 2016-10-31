<?php

namespace Music\Model;

use CoreWine\DataBase\ORM\Model;

use Auth\Model\User;

class Playlist extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'playlists';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> string('name');

		$schema -> toOne(User::class,'user');

	}
}

?>