<?php

namespace Chess\Model;

use CoreWine\DataBase\ORM\Model;

class ChessMatch extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'chess_matches';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();

		$schema -> string('player');

		$schema -> string('opponent');
		
		$schema -> string('score');
		
		$schema -> text('description');

		$schema -> datetime('date');

		$schema -> string('url');

	}
}

?>