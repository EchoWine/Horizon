<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

class Manga extends Model implements Resource{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'manga';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
		
		$schema -> string('name');

		$schema -> string('type');

		$schema -> string('alias');

		$schema -> string('genres');

		$schema -> text('overview');

		$schema -> string('status');

		$schema -> file('poster');

		$schema -> file('banner');

		$schema -> toOne(ResourceContainer::class,'container');

		$schema -> toMany(Season::class,'volumes','manga_id');

		$schema -> toMany(Chapter::class,'chapters','manga_id');
		
		$schema -> updated_at();

	}

	/**
	 * Return a complete array of this model (usefull in api)
	 *
	 * @return array
	 */
	public function toArrayComplete(){

		$res = parent::toArray();

		$res['poster'] = $this -> poster() -> getFullPath();
		$res['banner'] = $this -> banner() -> getFullPath();

		foreach(Chapter::where('manga_id',$this -> id) -> get() as $chapters){
			$chapters[] = $chapter -> toArray();
		}
		

		return array_merge($res,['chapters' => $chapters,'container' => $this -> container -> toArray()]);
	}

	/**
	 * Fill this entity using a generic response from database api
	 *
	 * @param object $response
	 * @param Container $container
	 */
	public function fillFromDatabaseApi($response,$container){
		
		$this -> container = $container;

		$this -> name = $response -> name;
		$this -> overview = $response -> overview;
		$this -> status = $response -> status;

		if($response -> poster)
			$this -> poster() -> setByUrl($response -> poster);

		if($response -> banner)
			$this -> banner() -> setByUrl($response -> banner);

		$this -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 

		$this -> save();


		foreach($response -> chapters as $r_chapter){

			/*$season = Season::firstOrCreate([
				'number' => $r_chapter -> season,
				'serie_id' => $this -> id
			]);*/


			$chapter = Chapter::firstOrCreate([
				'number' => $r_chapter -> number,
				'manga_id' => $this -> id
			]);
			/*
				'season_n' => $r_chapter -> season,
				'season_id' => $season -> id,
				*/

			$r_chapter -> name = $r_chapter -> name;
			$r_chapter -> overview = $r_chapter -> overview;
			$r_chapter -> released_at = $r_chapter -> released_at;
			$r_chapter -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s');
			$r_chapter -> save();

		}
	}
}

?>