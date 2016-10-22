<?php

namespace Auth\Model;

use CoreWine\DataBase\ORM\Model;
use CoreWine\DataBase\ORM\Field\Schema as Field;
use Auth\Field\Schema as AuthField;

use WT\Model\ResourceContainer;
use WT\Model\ResourceContainerUser;
use WT\Model\Serie;
use WT\Model\Manga;

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

		$schema -> toMany(Session::class,'sessions','user_id');

        $schema -> throughMany('containers',ResourceContainer::class) -> resolver(ResourceContainerUser::class,'user','container');
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
		$return = [];

		foreach($this -> containers -> all() as $resource){
			if($resource -> type == 'series'){
				$return[] = $resource -> id;
			}
		}


		return empty($return) ? [] : Serie::whereIn('container_id',$return) -> get();
	}
	
	public function getManga(){
		$return = [];

		foreach($this -> containers -> all() as $resource){
			if($resource -> type == 'manga'){
				$return[] = $resource -> id;
			}
		}

		# This is slow because Manga is loading all chapters, need stop it
		return empty($return) ? [] : Manga::whereIn('container_id',$return) -> get();
	}
}

?>