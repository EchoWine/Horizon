<?php

namespace Music\Model;

use CoreWine\DataBase\ORM\Model;

use Auth\Model\User;

class PlaylistSync extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'playlists_sync';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> toOne(Playlist::class,'playlist');

		$schema -> text('url');

	}
}

?>