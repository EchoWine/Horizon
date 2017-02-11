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

		# E.g. ?v=a9d8hadh
		$schema -> string('uid');

		# E.g. Youtube
		$schema -> string('source');

		$schema -> file('file') -> filesystem(function($model){
			return "videos/{$model -> uid}/{$model -> file() -> getValue()}";
		});

		$schema -> file('file_H264') -> filesystem(function($model){
			return "videos/{$model -> uid}/original_h264.mp4";
		});

		$schema -> file('thumb') -> filesystem(function($model){
			return "videos/{$model -> uid}/{$model -> thumb() -> getValue()}";
		});

        $schema -> throughMany('playlists',Playlist::class) -> resolver(PlaylistVideo::class,'video','playlist');
	}
}

?>