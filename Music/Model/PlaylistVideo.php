<?php

namespace Music\Model;

use CoreWine\DataBase\ORM\Model;

use Auth\Model\User;

class PlaylistVideo extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'playlists_videos';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> toOne(Playlist::class,'playlist');

		$schema -> toOne(Video::class,'video');

	}
}

?>