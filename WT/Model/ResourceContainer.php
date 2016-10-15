<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

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

        $schema -> toMany(ResourceContainerUser::class,'container_users','container_id')
                -> to('users','user');


	}
}

?>