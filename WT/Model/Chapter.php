<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;
use Auth\Model\User;

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
	
		$schema -> float('number');

		$schema -> string('volume_n');

		$schema -> string('scan');

		$schema -> files('raw');

		$schema -> datetime('released_at');

		$schema -> text('overview');

		$schema -> toOne(Volume::class,'volume');

		$schema -> toOne(Manga::class,'manga','manga_id');

		$schema -> updated_at();

        $schema -> throughMany('users',User::class) -> resolver(ChapterUser::class,'chapter','user');
	}

	/**
	 * Consumed by
	 *
	 * @param User $user
	 *
	 * @return bool
	 */
	public function consumedBy($user){
		return $this -> users -> get($user) ? $this -> users -> get($user) -> pivot -> consumed == 1 : 0;
	}
}

?>