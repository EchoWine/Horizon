<?php

namespace WT\Service;
use WT\Api as Api;
use WT\Model\Serie;
use WT\Model\Season;
use WT\Model\Episode;
use WT\Model\Resource;
use Request;
use CoreWine\Component\Collection;
use CoreWine\DataBase\DB;

class WT{

	/**
	 * List of all classes
	 *
	 * @var Array
	 */
	public static $classes = [
		'series' => Serie::class,
		'manga' => Serie::class
	];

	/**
	 * List of all managers
	 *
	 * @var Array
	 */
	public static $managers = [
		Api\BakaUpdates::class,
		Api\TheTVDB::class,
	];

	/**
	 * Get basic path api 
	 *
	 * @return string
	 */
	public static function url(){

		return Request::getDirUrl()."api/v1/";
	}

	/**
	 * Get model given resource name
	 *
	 * @param string $resource_type
	 *
	 * @return string
	 */
	public static function getClassByType($resource_type){

		return isset(self::$classes[$resource_type]) ? self::$classes[$resource_type] : null;
	}

	/**
	 * Discovery new resources
	 *
	 * @param string $user
	 * @param string $database
	 * @param string $key
	 *
	 * @return array
	 */
	public static function discovery($user,$database,$key){

		$response = [];
			
		foreach(self::$managers as $manager){

			$manager = new $manager();

			if($manager -> isResource($database)){
				$response[$manager -> getName()] = $manager -> discovery($key);

				foreach($response[$manager -> getName()] as $n => $k){
					$resource = Resource::where(['database_name' => $manager -> getName(),'database_id' => $k['id']]) -> first();
					
					if($resource){
						$u = $resource -> users -> has($user);
						$r = 1;
					}else{
						$r = 0;
						$u = 0;
					}
					
					$response[$manager -> getName()][$n]['library'] = $r;
					$response[$manager -> getName()][$n]['user'] = $u;

				}
			}

		}

		return $response;
	}

	/**
	 * Add a new resource
	 *
	 * @param string $user
	 * @param string $database
	 * @param mixed $id
	 *
	 * @return array
	 */
	public static function add($user,$database,$id){

		try{
			$response = [];


			$resource = Resource::where(['database_name' => $database,'database_id' => $id]) -> first();

			if($resource){

				if($resource -> users -> has($user)){

					# Some message ??
					return ['message' => 'Already added','status' => 'info'];

				}else{

					$resource -> users -> add($user);
					$resource -> users -> save();
				}

			}else{

				$manager = self::getManagerByDatabase($database);

				$response = $manager -> add($id);

				# Detect type
				$resource_type = $response -> type;

				$model = self::getClassByType($resource_type);

				$resource = Resource::create([
					'name' => $response -> name,
					'type' => $resource_type,
					'database_name' => $database,
					'database_id' => $id,
					'updated_at' => (new \DateTime()) -> format('Y-m-d H:i:s')
				]);

				$detail = new $model();
				$detail -> fillFromDatabaseApi($response,$resource);

				# TEMP-FIX
				$resource = Resource::where(['database_name' => $database,'database_id' => $id]) -> first();

				$resource -> users -> add($user);
				$resource -> users -> save();
			}

		}catch(\Exception $e){

			return ['message' => $e -> getMessage(),'status' => 'error'];
		}
			
		return ['message' => 'Resource added','status' => 'success'];
		
	}

	/**
	 * Get a resource
	 *
	 * @param string $user
	 * @param string $resource_type
	 * @param mixed $id
	 *
	 * @return array
	 */
	public static function get($user,$resource_type,$id){

		$model = self::getClassByType($manager_type);

		$model = $model::where('id',$id) -> first();
		

		return $model -> toArrayComplete();
	}


	/**
	 * Add a new resource
	 *
	 * @param string $user
	 * @param string $resource_type
	 * @param mixed $id
	 *
	 * @return array
	 */
	public static function sync($user,$resource_type,$id){

		try{
			$response = [];

			$model = self::getClassByType($retype);

			if(!$model)
				throw new \Exception("Resource type name invalid");

			$resource = $model::where(['id' => $id]) -> first();

			if(!$resource){

				throw new \Exception("Resource not found");

				

			}else{

				$database = $resource -> resource -> source_name;
				$id = $resource -> resource -> source_id;

				foreach(self::$managers as $manager){

					$manager = new $manager();

					if($manager -> getName() == $database){
						$response = $manager -> get($id);
						break;
					}

				}

				$resource_node = $resource -> resource;

				$resource_node -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 
				$resource_node -> save();

				$resource -> name = $response -> name;
				$resource -> overview = $response -> overview;
				$resource -> status = $response -> status;
				$resource -> resource = $resource;
				
				if($response -> poster)
					$resource -> poster() -> setByUrl($response -> poster);

				if($response -> banner)
					$resource -> banner() -> setByUrl($response -> banner);

				$resource -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 
				$resource -> save();

				if($retype == 'series' && $manager -> isResource('series')){

					foreach($response -> episodes as $r_episode){

						$season = Season::firstOrCreate([
							'number' => $r_episode -> season,
							'serie_id' => $resource -> id
						]);

						$episode = Episode::firstOrCreate([
							'number' => $r_episode -> number,
							'season_n' => $r_episode -> season,
							'season_id' => $season -> id,
							'serie_id' => $resource -> id
						]);

						$episode -> name = $r_episode -> name;
						$episode -> overview = $r_episode -> overview;
						$episode -> aired_at = $r_episode -> aired_at;
						$episode -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s');
						$episode -> save();

					}
				}

			}

		}catch(\Exception $e){

			return ['message' => $e -> getMessage(),'status' => 'error'];
		}
			
		return ['message' => 'Resource updated','status' => 'success'];
		
	}


	/**
	 * Delete a resource
	 *
	 * @param string $user
	 * @param string $resource
	 * @param string $database
	 * @param mixed $id
	 *
	 * @return array
	 */
	public static function delete($user,$manager_type,$database,$id){

		try{

			$response = [];

			$model = self::getClassByType($manager_type);

			if(!$model)
				throw new \Exception("Resource type name invalid");
			
			$resource = Resource::where(['database_name' => $database,'database_id' => $id]) -> first();

			if(!$resource)
				throw new \Exception("The resource doesn't exists");

			if(!$resource -> users -> has($user))
				throw new \Exception("The resource insn't in library");


			$resource -> users -> remove($user);
			$resource -> users -> save();


		}catch(\Exception $e){

			return ['status' => 'error','message' => $e -> getMessage()];
		}
			
		
		return ['status' => 'success','message' => 'Deleted'];
	}


	public static function all($user){
		$collection = new Collection();

		$series = Serie::all() -> toArray(false);
		$series = new Collection($series);
		$series -> addParam('type','series');
		$collection = $collection -> merge($series);

		return $collection;
	}
	/**
	 * Sync the series with update
	 */
	public static function update(){
		$collection = new Collection();

		foreach(self::$managers as $manager){

			$manager = new $manager();

			if($manager -> isResource('series')){
				$res = $manager -> update();


				$r = Serie::leftJoin('resources','resources.id','series.resource_id') 
				-> select('series.*');

				# Select only the resource that are in the db and aren't updated
				foreach($res as $k){
					$r = $r -> orWhere(function($q) use ($k){
						return $q -> where('resources.source_id',$k['id']) -> where('resources.updated_at','<',$k['updated_at']); 
					});
				}


				$r = $r -> get();

				$r = new Collection($r -> toArray());
				$r -> addParam('type','series');
				$collection = $collection -> merge($r);
			}

		}

		return $collection;

	}


	public static function getManagerByDatabase($database){

		foreach(self::$managers as $manager){

			$manager = new $manager();

			if($manager -> getName() == $database)
				return $manager;
			

		}

		return null;
	}
}

?>