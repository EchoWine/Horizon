<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;
use Auth\Model\User;

class Episode extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'episodes';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		
		$schema -> id();
	
		$schema -> string('name');
	
		$schema -> integer('number');

		$schema -> integer('season_n');

		$schema -> datetime('aired_at');

		$schema -> text('overview');

		$schema -> toOne(Season::class,'season');

		$schema -> toOne(Episode::class,'serie','serie_id');

		$schema -> updated_at();

        $schema -> throughMany('users',User::class) -> resolver(EpisodeUser::class,'episode','user');
		
		$schema -> integer('deleted') -> default(0);
	}

	/**
	 * Consumed By
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