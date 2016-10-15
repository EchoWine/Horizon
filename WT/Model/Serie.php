<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

class Serie extends Model{

	/**
	 * Table name
	 *
	 * @var
	 */
	public static $table = 'series';

	/**
	 * Set schema fields
	 *
	 * @param Schema $schema
	 */
	public static function fields($schema){

		$schema -> id();
		
		$schema -> string('name');

		$schema -> string('genres');

		$schema -> text('overview');

		$schema -> string('status');

		$schema -> file('poster');

		$schema -> file('banner');

		$schema -> toOne(Resource::class,'resource');

		$schema -> toMany(Season::class,'seasons','serie_id');

		$schema -> toMany(Episode::class,'episodes','serie_id');

		$schema -> datetime('updated_at');

	}

	public function toArrayComplete(){

		$res = parent::toArray();

		$res['poster'] = $this -> poster() -> getFullPath();
		$res['banner'] = $this -> banner() -> getFullPath();

		foreach(Episode::where('serie_id',$this -> id) -> get() as $episode){
			$episodes[] = $episode -> toArray();
		}
		

		return array_merge($res,['episodes' => $episodes,'resource' => $this -> resource -> toArray()]);
	}

	public function fillFromDatabaseApi($response,$resource){


		$this -> name = $response -> name;
		$this -> overview = $response -> overview;
		$this -> status = $response -> status;
		$this -> resource = $resource;
		$this -> poster() -> setByUrl($response -> poster);
		$this -> banner() -> setByUrl($response -> banner);
		$this -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 
		$this -> save();


		foreach($response -> episodes as $r_episode){

			$season = Season::firstOrCreate([
				'number' => $r_episode -> season,
				'serie_id' => $this -> id
			]);

			$episode = new Episode();
			$episode -> name = $r_episode -> name;
			$episode -> number = $r_episode -> number;
			$episode -> overview = $r_episode -> overview;
			$episode -> aired_at = $r_episode -> aired_at;
			$episode -> update_at = $r_episode -> updated_at;
			$episode -> season = $season;
			$episode -> season_n = $r_episode -> season;
			$episode -> serie_id = $this -> id;
			$episode -> save();

		}

	}
}

?>