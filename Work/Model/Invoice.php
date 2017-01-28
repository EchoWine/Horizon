<?php

namespace Work\Model;

use CoreWine\DataBase\ORM\Model;

class Invoice extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'wk_invoices';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> integer('number');

		$schema -> integer('year');

		$schema -> toOne(\Auth\Model\User::class,'user');

		$schema -> toOne(Profile::class,'profile');

		$schema -> toOne(Customer::class,'customer');

		$schema -> text('items');

		$schema -> float('price_gross');

		$schema -> float('price_tax');

		$schema -> float('price_net');

		$schema -> string('tax_id');

		$schema -> string('stamp_id');

		$schema -> string('template');

		$schema -> date('date');


	}

	/**
	 * Get price gross
	 *
	 * @return float
	 */
	public function getPriceGross(){
		return $this -> price_gross;
	}

	/**
	 * Get price net
	 *
	 * @return float
	 */
	public function getPriceNet(){
		return $this -> getPriceGross() + $this -> getTaxesAmount();
	}

	/**
	 * Get taxes amount
	 *
	 * @return float
	 */
	public function getTaxesAmount(){
		return $this -> getPriceGross() * 0.04;
	}

	/**
	 * Get identification
	 *
	 * @return string
	 */
	public function getIdentification(){
		return sprintf("%03d",$this -> number)."-".$this -> year;
	}

}	


?>