<?php

namespace Auth\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;
use Auth\Field\Schema as AuthField;

use WT\Model\ResourceContainer;
use WT\Model\ResourceContainerUser;
use WT\Model\Serie;
use WT\Model\Manga;
use Music\Model\Playlist;
use CoreWine\Component\Collection;

class User extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'users';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> password()
				-> maxLength(128);

		$schema -> string('username')
				-> required()
				-> unique();

		$schema -> email();

		$schema -> string('token');

		$schema -> toMany('sessions',Session::class,'user');

        $schema -> throughMany('containers',ResourceContainer::class) -> resolver(ResourceContainerUser::class,'user','container');

		$schema -> toMany('playlists',Playlist::class,'user');

	}

	/**
	 * Seed
	 *
	 * @param Repository $repository
	 */
	public static function boot(){
		if(User::count() == 0){
			User::create([
				'username' => 'admin',
				'email' => 'admin@admin.com',
				'password' => 'admin'
			]);
		}

	}
	
	public function getSeries(){
		$return = new Collection();

		foreach($this -> containers -> all() as $resource){
			if($resource -> type == 'series'){
				$return[] = $resource -> id;
			}
		}


		return $return -> isEmpty() ? $return : Serie::whereIn('container_id',$return -> toArray()) -> get();
	}
	
	public function getManga(){
		$return = new Collection();

		foreach($this -> containers -> all() as $resource){
			if($resource -> type == 'manga'){
				$return[] = $resource -> id;
			}
		}

		return $return -> isEmpty() ? $return : Manga::whereIn('container_id',$return -> toArray()) -> get();
	}	

}

?>