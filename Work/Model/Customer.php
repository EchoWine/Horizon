<?php

namespace Work\Model;

use CoreWine\DataBase\ORM\Model;

class Customer extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'wk_customers';

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

		$schema -> string('iban');
		
		
		$schema -> toOne(\Auth\Model\User::class,'user');

	}
}

?>