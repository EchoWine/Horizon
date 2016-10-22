<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;
use Auth\Model\User;

class ResourceContainer extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'resource_containers';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
	
		$schema -> string('name');

		$schema -> string('type');
	
		$schema -> string('database_name');
	
		$schema -> string('database_id');

		$schema -> updated_at();


        $schema -> throughMany('users',User::class) -> resolver(ResourceContainerUser::class,'container','user');

	}
}

?>