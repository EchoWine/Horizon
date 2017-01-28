<?php

namespace WT\Model;

use CoreWine\DataBase\ORM\Model;

class Serie extends Model implements Resource{

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

		$schema -> toOne(ResourceContainer::class,'container');

		$schema -> toMany('seasons',Season::class,'serie');

		$schema -> toMany('episodes',Episode::class,'serie');
		
		$schema -> updated_at();

	}

	/**
	 * Return a complete array of this model (usefull in api)
	 *
	 * @return array
	 */
	public function toArrayComplete(){

		$res = parent::toArray();

		foreach(Episode::where('serie_id',$this -> id) -> get() as $episode){
			$episodes[] = $episode -> toArray();
		}
		

		return array_merge($res,[
			'type' => 'series',
			'poster' => $this -> poster() -> thumb(540,780),
			'episodes' => $episodes,
			'container' => $this -> container -> toArray()
		]);
	}


	/**
	 * Return a complete array of this model
	 *
	 * @return array
	 */
	public function toArray(){

		$res = parent::toArray();
		
		return array_merge($res,[
			'type' => 'series',
			'poster' => $this -> poster() -> thumb(540,780),
			'new' => \Auth::user() 
				? EpisodeUser::where(['episodes_users.user_id' => \Auth::user() -> id,'episodes_users.serie_id' => $this -> id,'episodes_users.consumed' => 0])
					-> leftJoin('episodes','episodes.id','=','episodes_users.episode_id')
					-> whereNotNull('episodes.aired_at')
					-> where('episodes.aired_at','<=',(new \DateTime()) -> format('Y-m-d'))
					-> select('COUNT(episodes_users.id)')
					-> count() 
				: null
		]);
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

		$this -> save();

		$users = $this -> container -> users;

		foreach($response -> episodes as $r_episode){

			$season = Season::firstOrCreate([
				'number' => $r_episode -> season,
				'serie_id' => $this -> id
			]);


			$episode = Episode::firstOrCreate([
				'number' => $r_episode -> number,
				'season_n' => $r_episode -> season,
				'season_id' => $season -> id,
				'serie_id' => $this -> id
			]);

			$episode -> name = $r_episode -> name;
			$episode -> overview = $r_episode -> overview;

			if($r_episode -> aired_at)
				$episode -> aired_at = $r_episode -> aired_at." 00:00:00";

			$episode -> save();

			# Create EpisodeUser if doesn't exists for every user that have access to this resource
			foreach($users as $user){
				$eu = EpisodeUser::firstOrCreate([
					'container_id' => $this -> container_id,
					'serie_id' => $this -> id,
					'episode_id' => $episode -> id,
					'user_id' => $user -> id
				]);
			}



		}
	}

	/**
	 * Update consumed episode
	 *
	 * @param user $user
	 * @param array $ids
	 *
	 * @return void
	 */
	public function resetAndConsume($user,$ids){

		foreach($this -> episodes as $episode){

			$eu = EpisodeUser::firstOrCreate([
				'container_id' => $this -> container_id,
				'serie_id' => $this -> id,
				'episode_id' => $episode -> id,
				'user_id' => $user -> id
			]);
			
			$eu -> consumed = in_array($episode -> id,$ids) ? 1 : 0;
			$eu -> save();

		}
	}

	/**
	 * Update consumed episodes
	 *
	 * @param user $user
	 * @param array $ids
	 *
	 * @return void
	 */
	public function consume($user,$ids){

		# Check if chapters exists;

		foreach(Episode::whereIn('id',array_keys($ids)) -> where('serie_id',$this -> id) -> get() as $episode){

			$eu = EpisodeUser::firstOrCreate([
				'container_id' => $this -> container_id,
				'serie_id' => $this -> id,
				'episode_id' => $episode -> id,
				'user_id' => $user -> id
			]);

			$eu -> consumed = $ids[$episode -> id];
			$eu -> save();

		}
	}
}

?>