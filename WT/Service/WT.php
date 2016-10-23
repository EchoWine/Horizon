<?php

namespace WT\Service;
use WT\Api as Api;
use WT\Model\Serie;
use WT\Model\Manga;
use WT\Model\Season;
use WT\Model\Episode;
use WT\Model\ResourceContainer;
use Request;
use CoreWine\Component\Collection;
use CoreWine\DataBase\DB;

/**
 * 
 * manager: A class that manage all calls to api and return "generic response"
 * response: a generic array that contains information about calls retrieved by manager
 * database: database used for api call (e.g. TheTVDB,Baka-Updates...)
 * model: Models
 * resource: All things related to model
 * container: A resource container
 */
class WT{

	/**
	 * List of all classes
	 *
	 * @var Array
	 */
	public static $classes = [
		'series' => Serie::class,
		'manga' => Manga::class
	];

	/**
	 * List of all managers
	 *
	 * @var Array
	 */
	public static $managers = [
		Api\MangaFox::class,
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
	public static function discovery($user,$database_name,$key){

		$response = new Collection();
	

		foreach(self::$managers as $manager){

			$manager = new $manager();

			if($database_name == 'all' || $manager -> getName() == $database_name){
				$response[$manager -> getName()] = $manager -> discovery($key);

				foreach($response[$manager -> getName()] as $n => $k){

					$container = ResourceContainer::where(['database_name' => $k['database'],'database_id' => $k['id']]) -> first();


					if($container){


						$u = $container -> users -> has($user) ? 1 : 0;
						$r = 1;

						$resource_class = self::getClassByType($container -> type);
						$resource = $resource_class::where('container_id',$container -> id) -> first();

						$response[$manager -> getName()][$n]['container'] = $container -> toArray();
						$response[$manager -> getName()][$n]['resource'] = $resource -> toArray();
					}else{
						$r = 0;
						$u = 0;

						$response[$manager -> getName()][$n]['container'] = [];
						$response[$manager -> getName()][$n]['resource'] = [];
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
	 * @param string $database_name
	 * @param mixed $database_id
	 *
	 * @return array
	 */
	public static function add($user,$database_name,$database_id){

		try{
			$response = [];

			$container = ResourceContainer::where(['database_name' => $database_name,'database_id' => $database_id]) -> first();

			if($container){

				$resource_class = self::getClassByType($container -> type);

				$resource = $resource_class::where('container_id',$container -> id) -> first();

				if($container -> users -> has($user)){

					return ['message' => 'Already added','status' => 'info','data' => ['container' => $container -> toArray(),'resource' => $resource -> toArray()]];

				}else{

					$container -> users -> add($user);
					$container -> users -> save();

					return ['message' => 'Added resource to library','status' => 'success','data' => ['container' => $container -> toArray(),'resource' => $resource -> toArray()]];
				}

			}else{

				$manager = self::getManagerByDatabase($database_name);

				$response = $manager -> get($database_id);

				# Detect type
				$resource_type = $response -> type;

				$model = self::getClassByType($resource_type);

				$container = ResourceContainer::create([
					'name' => $response -> name,
					'type' => $resource_type,
					'database_name' => $database_name,
					'database_id' => $database_id,
					'updated_at' => (new \DateTime()) -> format('Y-m-d H:i:s')
				]);

				$resource = new $model();
				$resource -> fillFromDatabaseApi($response,$container);

				# TEMP-FIX
				$container = ResourceContainer::where(['database_name' => $database_name,'database_id' => $database_id]) -> first();

				$container -> users -> add($user);
				$container -> users -> save();

			return ['status' => 'success','message' => 'Added new resource','data' => ['container' => $container -> toArray(),'resource' => $resource -> toArray()]];
			}

		}catch(\Exception $e){

			return ['message' => $e -> getMessage(),'status' => 'error'];
		}
			
		
	}

	/**
	 * Get a resource
	 *
	 * @param string $user
	 * @param string $resource_type
	 * @param mixed $resource_id
	 *
	 * @return array
	 */
	public static function get($user,$resource_type,$resource_id){

		try{
			$resource_class = self::getClassByType($resource_type);

			$resource = $resource_class::where('id',$resource_id) -> first();
			
			return ['status' => 'success','data' => $resource -> toArrayComplete()];
		}catch(\Exception $e){
			return ['status' => 'error','message' => $e -> getMessage()];
		}
	}


	/**
	 * Add a new resource
	 *
	 * @param string $user
	 * @param string $resource_type
	 * @param mixed $resource_id
	 *
	 * @return array
	 */
	public static function sync($user,$resource_type,$resource_id){

		try{

			$response = [];

			$resource_class = self::getClassByType($resource_type);

			if(!$resource_class)
				throw new \Exception("Resource type name invalid");

			$resource = $resource_class::where(['id' => $resource_id]) -> first();

			if(!$resource){
				throw new \Exception("Resource not found");

			}else{

				$manager = self::getManagerByDatabase($resource -> container -> database_name);

				$response = $manager -> get($resource -> container -> database_id);
				
				$container = $resource -> container;

				$container -> updated_at = (new \DateTime()) -> format('Y-m-d H:i:s'); 
				$container -> save();

				$resource -> fillFromDatabaseApi($response,$container);			

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
	 * @param string $resource_type
	 * @param int $resource_id
	 *
	 * @return array
	 */
	public static function delete($user,$resource_type,$resource_id){

		try{

			$response = [];

			$model = self::getClassByType($resource_type);

			if(!$model)
				throw new \Exception("Resource type name invalid");
			
			$resource = $model::where('id',$resource_id) -> first();

			if(!$resource)
				throw new \Exception("The resource doesn't exists");

			$container = $resource -> container;

			if(!$container -> users -> has($user))
				throw new \Exception("The resource insn't in library");


			$container -> users -> remove($user);
			$container -> users -> save();

			# Delete resource if not user have it? 


		}catch(\Exception $e){

			return ['status' => 'error','message' => $e -> getMessage()];
		}
			
		
		return ['status' => 'success','message' => 'Deleted','data' => ['container' => $container -> toArray(),'resource' => $resource -> toArray()]];
	}


	public static function all($user){
		$collection = new Collection();

		$resources = Manga::all() -> toArray(false);
		$resources = new Collection($resources);
		$resources -> addParam('type','manga');
		$collection = $collection -> merge($resources);
		
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
						return $q -> where('resources.database_id',$k['id']) -> where('resources.updated_at','<',$k['updated_at']); 
					});
				}


				$r = $r -> get();

				$r = new Collection($r -> toArray());
				$r -> addParam('type','series');
				$collection = $collection -> merge($r);
			}

			if($manager -> isResource('manga')){
				$res = $manager -> update();


				$r = Manga::leftJoin('resources','resources.id','manga.resource_id') 
				-> select('manga.*');

				# Select only the resource that are in the db and aren't updated
				foreach($res as $k){
					$r = $r -> orWhere(function($q) use ($k){
						return $q -> where('resources.database_id',$k['id']) -> where('resources.updated_at','<',$k['updated_at']); 
					});
				}


				$r = $r -> get();

				$r = new Collection($r -> toArray());
				$r -> addParam('type','manga');
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