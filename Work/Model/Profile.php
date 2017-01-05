<?php

namespace Work\Model;

use CoreWine\DataBase\ORM\Model;

class Profile extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'wk_profiles';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> string('fullname');

		$schema -> string('address');

		$schema -> string('vat');

		$schema -> string('tax_code');

		$schema -> string('notes');

		$schema -> toOne(\Auth\Model\User::class,'user');

	}
}

?>