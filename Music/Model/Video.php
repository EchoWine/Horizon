<?php

namespace Music\Model;

use CoreWine\DataBase\ORM\Model;

use Auth\Model\User;

class Video extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'videos';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> string('name');
	
		$schema -> file('file');

        $schema -> throughMany('playlists',Playlist::class) -> resolver(PlaylistVideo::class,'video','playlist');
	}
}

?>